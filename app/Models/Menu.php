<?php

namespace App\Models;

use App\Support\Database;
use App\Support\Cache;

class Menu extends BaseModel
{
    protected static string $table = 'menus';

    public static function getByLocation(string $location): array
    {
        return Cache::remember("menu_$location", 300, function () use ($location) {
            $menu = Database::selectOne('SELECT * FROM menus WHERE location = ? AND is_active = 1 LIMIT 1', [$location]);
            if (!$menu) return [];
            return self::buildTree((int) $menu['id']);
        });
    }

    private static function buildTree(int $menuId, int $parentId = 0): array
    {
        $items = Database::select(
            'SELECT * FROM menu_items WHERE menu_id = ? AND parent_id = ? ORDER BY sort_order ASC',
            [$menuId, $parentId]
        );
        foreach ($items as &$item) {
            $item['children'] = self::buildTree($menuId, $item['id']);
        }
        return $items;
    }

    public static function flush(): void
    {
        $menus = Database::select('SELECT location FROM menus');
        foreach ($menus as $menu) {
            Cache::forget('menu_' . $menu['location']);
        }
    }
}
