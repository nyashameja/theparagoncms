<?php

namespace App\Services;

use App\Models\User;
use App\Support\Session;
use App\Support\Logger;
use App\Support\RateLimit;

class AuthService
{
    public function attempt(string $email, string $password, string $ip): array
    {
        $key = 'login:' . $ip;

        if (!RateLimit::check($key, 5, 300)) {
            return ['success' => false, 'error' => 'Too many failed login attempts. Please try again in 5 minutes.', 'throttled' => true];
        }

        $user = User::findByEmail($email);

        if (!$user || !User::verifyPassword($user, $password)) {
            Logger::audit('login_failed', ['email' => $email, 'ip' => $ip]);
            return ['success' => false, 'error' => 'Invalid email address or password.'];
        }

        if (!User::isActive($user)) {
            return ['success' => false, 'error' => 'Your account has been deactivated. Please contact an administrator.'];
        }

        // Check for 2FA
        if ($user['two_factor_enabled']) {
            Session::set('_2fa_user_id', $user['id']);
            return ['success' => false, 'requires_2fa' => true];
        }

        $this->login($user, $ip);
        RateLimit::clear($key);

        return ['success' => true, 'user' => $user];
    }

    public function verify2fa(string $code, string $ip): array
    {
        $userId = Session::get('_2fa_user_id');
        if (!$userId) return ['success' => false, 'error' => 'Session expired.'];

        $user = User::find($userId);
        if (!$user) return ['success' => false, 'error' => 'User not found.'];

        if (!$this->verifyTotp($user['two_factor_secret'], $code)) {
            // Check recovery codes
            if (!$this->useRecoveryCode($user['id'], $code)) {
                return ['success' => false, 'error' => 'Invalid verification code.'];
            }
        }

        Session::forget('_2fa_user_id');
        $this->login($user, $ip);

        return ['success' => true, 'user' => $user];
    }

    private function login(array $user, string $ip): void
    {
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role'] ?? 'admin');
        Session::set('_last_activity', time());

        User::update($user['id'], ['last_login_at' => date('Y-m-d H:i:s'), 'last_login_ip' => $ip]);
        Logger::audit('login_success', ['user_id' => $user['id'], 'email' => $user['email']]);
    }

    public function logout(): void
    {
        Logger::audit('logout', ['user_id' => Session::get('user_id')]);
        Session::destroy();
        Session::start();
    }

    public function check(): bool
    {
        return Session::has('user_id');
    }

    public function user(): ?array
    {
        $id = Session::get('user_id');
        if (!$id) return null;
        return User::find($id);
    }

    public function userId(): ?int
    {
        return Session::get('user_id');
    }

    public function hasRole(string $role): bool
    {
        return Session::get('user_role') === $role;
    }

    private function verifyTotp(string $secret, string $code): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        $timestamp = floor(time() / 30);

        for ($i = -1; $i <= 1; $i++) {
            $t = $timestamp + $i;
            $hash = hash_hmac('sha1', pack('N*', 0) . pack('N*', $t), base32_decode($secret), true);
            $offset = ord($hash[strlen($hash) - 1]) & 0x0F;
            $otp = (
                ((ord($hash[$offset]) & 0x7F) << 24) |
                ((ord($hash[$offset + 1]) & 0xFF) << 16) |
                ((ord($hash[$offset + 2]) & 0xFF) << 8) |
                (ord($hash[$offset + 3]) & 0xFF)
            ) % 1000000;

            if (str_pad((string) $otp, 6, '0', STR_PAD_LEFT) === $code) return true;
        }

        return false;
    }

    private function useRecoveryCode(int $userId, string $code): bool
    {
        $codes = \App\Support\Database::select('SELECT * FROM two_factor_recovery_codes WHERE user_id = ? AND used_at IS NULL', [$userId]);
        foreach ($codes as $row) {
            if (password_verify($code, $row['code_hash'])) {
                \App\Support\Database::update('UPDATE two_factor_recovery_codes SET used_at = NOW() WHERE id = ?', [$row['id']]);
                return true;
            }
        }
        return false;
    }
}

function base32_decode(string $encoded): string
{
    $encoded = strtoupper($encoded);
    $chars   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $buffer  = 0;
    $bitsLeft = 0;
    $result  = '';

    for ($i = 0; $i < strlen($encoded); $i++) {
        $pos = strpos($chars, $encoded[$i]);
        if ($pos === false) continue;
        $buffer = ($buffer << 5) | $pos;
        $bitsLeft += 5;
        if ($bitsLeft >= 8) {
            $result .= chr(($buffer >> ($bitsLeft - 8)) & 0xFF);
            $bitsLeft -= 8;
        }
    }

    return $result;
}
