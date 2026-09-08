<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\View;
use App\Support\Validator;
use App\Models\User;
use App\Support\Database;

class UsersController
{
    public function index(Request $request): void
    {
        $users = Database::select(
            "SELECT u.*, GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ', ') as roles
             FROM users u
             LEFT JOIN user_roles ur ON ur.user_id = u.id
             LEFT JOIN roles r ON r.id = ur.role_id
             WHERE u.deleted_at IS NULL
             GROUP BY u.id ORDER BY u.created_at DESC"
        );
        View::render('admin/users/index', ['title' => 'Users', 'users' => $users]);
    }

    public function create(Request $request): void
    {
        $roles = Database::select("SELECT * FROM roles ORDER BY name");
        View::render('admin/users/form', ['title' => 'New User', 'user' => [], 'allRoles' => $roles, 'userRoles' => [], 'errors' => []]);
    }

    public function store(Request $request): void
    {
        $data = $request->all();
        $v = Validator::make($data, [
            'name'     => 'required|max:200',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:12|confirmed',
        ]);
        if ($v->fails()) {
            $roles = Database::select("SELECT * FROM roles ORDER BY name");
            View::render('admin/users/form', ['title' => 'New User', 'user' => $data, 'allRoles' => $roles, 'userRoles' => [], 'errors' => $v->errors()]);
            return;
        }
        $id = Database::insert(
            "INSERT INTO users (name, email, password, status, created_at, updated_at) VALUES (?,?,?,?,NOW(),NOW())",
            [$data['name'], strtolower($data['email']), User::hashPassword($data['password']), 'active']
        );
        $this->syncRoles($id, $data['roles'] ?? []);
        Session::flash('success', 'User created.');
        redirect('/admin/users');
    }

    public function edit(Request $request, int $id): void
    {
        $user = Database::selectOne("SELECT * FROM users WHERE id=? AND deleted_at IS NULL", [$id]);
        if (!$user) abort(404);
        $roles = Database::select("SELECT * FROM roles ORDER BY name");
        $userRoles = array_column(Database::select("SELECT role_id FROM user_roles WHERE user_id=?", [$id]), 'role_id');
        View::render('admin/users/form', ['title' => 'Edit User', 'user' => $user, 'allRoles' => $roles, 'userRoles' => $userRoles, 'errors' => []]);
    }

    public function update(Request $request, int $id): void
    {
        $user = Database::selectOne("SELECT * FROM users WHERE id=? AND deleted_at IS NULL", [$id]);
        if (!$user) abort(404);
        $data = $request->all();
        $rules = ['name' => 'required|max:200', 'email' => 'required|email|unique:users,email,' . $id];
        if (!empty($data['password'])) $rules['password'] = 'min:12|confirmed';
        $v = Validator::make($data, $rules);
        if ($v->fails()) {
            $roles = Database::select("SELECT * FROM roles ORDER BY name");
            $userRoles = array_column(Database::select("SELECT role_id FROM user_roles WHERE user_id=?", [$id]), 'role_id');
            View::render('admin/users/form', ['title' => 'Edit User', 'user' => array_merge($user, $data), 'allRoles' => $roles, 'userRoles' => $userRoles, 'errors' => $v->errors()]);
            return;
        }
        $sql = "UPDATE users SET name=?, email=?, status=?, updated_at=NOW()";
        $params = [$data['name'], strtolower($data['email']), $data['status'] ?? 'active'];
        if (!empty($data['password'])) { $sql .= ', password=?'; $params[] = User::hashPassword($data['password']); }
        $params[] = $id;
        Database::update($sql . ' WHERE id=?', $params);
        $this->syncRoles($id, $data['roles'] ?? []);
        Session::flash('success', 'User updated.');
        redirect('/admin/users');
    }

    public function delete(Request $request, int $id): void
    {
        if ($id === (int)Session::get('user_id')) {
            Session::flash('error', 'You cannot delete your own account.');
            redirect('/admin/users');
            return;
        }
        Database::update("UPDATE users SET deleted_at=NOW() WHERE id=?", [$id]);
        Session::flash('success', 'User deleted.');
        redirect('/admin/users');
    }

    private function syncRoles(int $userId, array $roleIds): void
    {
        Database::delete("DELETE FROM user_roles WHERE user_id=?", [$userId]);
        foreach ($roleIds as $roleId) {
            Database::insert("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?,?)", [$userId, (int)$roleId]);
        }
    }
}
