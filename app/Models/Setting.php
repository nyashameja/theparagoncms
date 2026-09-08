<?php

namespace App\Models;

use App\Support\Database;
use App\Support\Cache;

class Setting extends BaseModel
{
    protected static string $table = 'settings';

    private static ?array $cache = null;

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();
        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $existing = Database::selectOne('SELECT id FROM settings WHERE `key` = ?', [$key]);
        if ($existing) {
            Database::update('UPDATE settings SET `value` = ?, updated_at = NOW() WHERE `key` = ?', [$value, $key]);
        } else {
            Database::insert('INSERT INTO settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, NOW(), NOW())', [$key, $value]);
        }
        self::$cache = null;
        Cache::forget('site_settings');
    }

    public static function setMany(array $data): void
    {
        Database::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                self::set($key, $value);
            }
        });
    }

    public static function all(bool $fresh = false): array
    {
        if (self::$cache !== null && !$fresh) return self::$cache;

        if (!$fresh) {
            $cached = Cache::get('site_settings');
            if ($cached !== null) {
                self::$cache = $cached;
                return $cached;
            }
        }

        try {
            $rows  = Database::select('SELECT `key`, `value` FROM settings WHERE deleted_at IS NULL');
            $result = [];
            foreach ($rows as $row) {
                $result[$row['key']] = $row['value'];
            }
            self::$cache = $result;
            Cache::put('site_settings', $result, 300);
            return $result;
        } catch (\Throwable) {
            return [];
        }
    }

    public static function getBrand(): array
    {
        $all = self::all();
        return [
            'name'       => $all['business_name'] ?? 'The Paragon .Design',
            'tagline'    => $all['tagline'] ?? 'Premium Digital Agency',
            'logo'       => $all['logo_primary'] ?? '',
            'logo_dark'  => $all['logo_dark'] ?? '',
            'logo_icon'  => $all['logo_icon'] ?? '',
            'favicon'    => $all['favicon'] ?? '',
            'colors'     => [
                'primary_bg'    => $all['color_primary_bg']    ?? '#FFFFFF',
                'alt_bg'        => $all['color_alt_bg']        ?? '#F5F5F3',
                'primary_text'  => $all['color_primary_text']  ?? '#111111',
                'secondary_text'=> $all['color_secondary_text']?? '#5E5E5E',
                'strong_black'  => $all['color_strong_black']  ?? '#050505',
                'accent'        => $all['color_accent']        ?? '#D71920',
                'accent_dark'   => $all['color_accent_dark']   ?? '#A80F17',
                'border'        => $all['color_border']        ?? '#E6E6E3',
                'success'       => $all['color_success']       ?? '#16845B',
                'warning'       => $all['color_warning']       ?? '#C47A12',
            ],
        ];
    }

    public static function getContact(): array
    {
        $all = self::all();
        return [
            'email'       => $all['contact_email']       ?? '',
            'phone'       => $all['contact_phone']       ?? '',
            'whatsapp'    => $all['whatsapp_number']      ?? '',
            'address'     => $all['address_line1']        ?? '',
            'address2'    => $all['address_line2']        ?? '',
            'city'        => $all['address_city']         ?? 'Johannesburg',
            'province'    => $all['address_province']     ?? 'Gauteng',
            'postal_code' => $all['address_postal']       ?? '',
            'country'     => $all['address_country']      ?? 'South Africa',
        ];
    }
}
