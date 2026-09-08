<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\Validator;
use App\Services\AuthService;
use App\Models\User;

class AuthController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    public function loginForm(Request $request): string
    {
        return view('admin.auth.login', ['title' => 'Admin Login']);
    }

    public function login(Request $request): string
    {
        $v = Validator::make($request->body, [
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old.email', $request->get('email', ''));
            redirect('/admin/login');
        }

        $result = $this->auth->attempt(
            $request->get('email'),
            $request->get('password'),
            $request->ip
        );

        if (!$result['success']) {
            if (!empty($result['requires_2fa'])) {
                redirect('/admin/2fa');
            }
            Session::flash('error', $result['error'] ?? 'Login failed.');
            Session::flash('_old.email', $request->get('email', ''));
            redirect('/admin/login');
        }

        $intended = Session::getFlash('intended') ?? '/admin';
        redirect($intended);
    }

    public function twoFactorForm(Request $request): string
    {
        if (!Session::has('_2fa_user_id')) redirect('/admin/login');
        return view('admin.auth.two-factor', ['title' => 'Two-Factor Authentication']);
    }

    public function twoFactor(Request $request): string
    {
        if (!Session::has('_2fa_user_id')) redirect('/admin/login');

        $code   = preg_replace('/\s+/', '', $request->get('code', ''));
        $result = $this->auth->verify2fa($code, $request->ip);

        if (!$result['success']) {
            Session::flash('error', $result['error'] ?? 'Invalid code.');
            redirect('/admin/2fa');
        }

        redirect('/admin');
    }

    public function logout(Request $request): void
    {
        $this->auth->logout();
        redirect('/admin/login');
    }

    public function forgotForm(Request $request): string
    {
        return view('admin.auth.forgot', ['title' => 'Reset Password']);
    }

    public function forgot(Request $request): string
    {
        $v = Validator::make($request->body, ['email' => 'required|email']);
        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            redirect('/admin/forgot-password');
        }

        $user = User::findByEmail($request->get('email'));
        if ($user) {
            $token = User::createPasswordReset($user['id']);
            $resetUrl = url('admin/reset-password/' . $token);

            \App\Support\Mailer::to($user['email'], $user['name'])
                ->subject('Reset Your Password — The Paragon .Design')
                ->view('emails.password-reset', ['name' => $user['name'], 'url' => $resetUrl])
                ->send();
        }

        // Always show success to prevent email enumeration
        Session::flash('success', 'If that email address is in our system, you will receive a password reset link shortly.');
        redirect('/admin/forgot-password');
    }

    public function resetForm(Request $request, array $params): string
    {
        $token  = $params['token'] ?? '';
        $record = User::findByResetToken($token);
        if (!$record) {
            Session::flash('error', 'This reset link is invalid or has expired.');
            redirect('/admin/forgot-password');
        }
        return view('admin.auth.reset', ['title' => 'Set New Password', 'token' => $token]);
    }

    public function reset(Request $request): string
    {
        $v = Validator::make($request->body, [
            'token'                 => 'required',
            'password'              => 'required|min:8|confirmed',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            redirect('/admin/reset-password/' . $request->get('token'));
        }

        $token  = $request->get('token');
        $record = User::findByResetToken($token);

        if (!$record) {
            Session::flash('error', 'This reset link is invalid or has expired. Please request a new one.');
            redirect('/admin/forgot-password');
        }

        User::update((int) $record['user_id'], [
            'password_hash' => User::hashPassword($request->get('password')),
        ]);
        User::clearResetToken($token);

        \App\Support\Logger::audit('password_reset', ['user_id' => $record['user_id']]);

        Session::flash('success', 'Your password has been reset. Please log in.');
        redirect('/admin/login');
    }
}
