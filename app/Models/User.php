<?php

namespace App\Models;

use App\Support\Database;

class User extends BaseModel
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        return Database::selectOne('SELECT * FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1', [$email]);
    }

    public static function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, $user['password_hash']);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function createPasswordReset(int $userId): string
    {
        $token  = bin2hex(random_bytes(32));
        $hash   = hash('sha256', $token);
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        Database::query(
            'DELETE FROM password_resets WHERE user_id = ?',
            [$userId]
        );
        Database::query(
            'INSERT INTO password_resets (user_id, token_hash, expires_at, created_at) VALUES (?, ?, ?, NOW())',
            [$userId, $hash, $expiry]
        );

        return $token;
    }

    public static function findByResetToken(string $token): ?array
    {
        $hash = hash('sha256', $token);
        return Database::selectOne(
            'SELECT pr.*, u.email FROM password_resets pr
             JOIN users u ON u.id = pr.user_id
             WHERE pr.token_hash = ? AND pr.expires_at > NOW() AND pr.used_at IS NULL',
            [$hash]
        );
    }

    public static function clearResetToken(string $token): void
    {
        $hash = hash('sha256', $token);
        Database::query('UPDATE password_resets SET used_at = NOW() WHERE token_hash = ?', [$hash]);
    }

    public static function hasPermission(int $userId, string $permission): bool
    {
        $row = Database::selectOne(
            'SELECT 1 FROM user_roles ur
             JOIN role_permissions rp ON rp.role_id = ur.role_id
             JOIN permissions p ON p.id = rp.permission_id
             WHERE ur.user_id = ? AND p.slug = ? LIMIT 1',
            [$userId, $permission]
        );
        return $row !== null;
    }

    public static function getRoles(int $userId): array
    {
        return Database::select(
            'SELECT r.* FROM roles r JOIN user_roles ur ON ur.role_id = r.id WHERE ur.user_id = ?',
            [$userId]
        );
    }

    public static function isActive(array $user): bool
    {
        return (bool) $user['is_active'];
    }
}
