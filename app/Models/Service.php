<?php

namespace App\Models;

use App\Support\Database;

class Service extends BaseModel
{
    protected static string $table = 'services';

    public static function findBySlug(string $slug): ?array
    {
        return Database::selectOne('SELECT * FROM services WHERE slug = ? AND deleted_at IS NULL LIMIT 1', [$slug]);
    }

    public static function published(): array
    {
        return Database::select('SELECT * FROM services WHERE status = ? AND deleted_at IS NULL ORDER BY sort_order ASC, name ASC', ['published']);
    }

    public static function featured(): array
    {
        return Database::select('SELECT * FROM services WHERE status = ? AND is_featured = 1 AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT 6', ['published']);
    }

    public static function getWithRelated(int $id): ?array
    {
        $service = static::find($id);
        if (!$service) return null;

        $service['faqs'] = Database::select('SELECT * FROM service_faqs WHERE service_id = ? ORDER BY sort_order ASC', [$id]);
        $service['features'] = Database::select('SELECT * FROM service_features WHERE service_id = ? ORDER BY sort_order ASC', [$id]);
        $service['process_steps'] = Database::select('SELECT * FROM service_process_steps WHERE service_id = ? ORDER BY step_number ASC', [$id]);
        $service['deliverables'] = Database::select('SELECT * FROM service_deliverables WHERE service_id = ? ORDER BY sort_order ASC', [$id]);
        $service['benefits'] = Database::select('SELECT * FROM service_benefits WHERE service_id = ? ORDER BY sort_order ASC', [$id]);

        return $service;
    }

    public static function getBySlugWithRelated(string $slug): ?array
    {
        $service = static::findBySlug($slug);
        if (!$service) return null;
        return static::getWithRelated($service['id']);
    }

    public static function reorder(array $ids): void
    {
        foreach ($ids as $order => $id) {
            Database::update('UPDATE services SET sort_order = ? WHERE id = ?', [$order, $id]);
        }
    }

    public static function getFaqs(int $serviceId): array
    {
        return Database::select('SELECT * FROM service_faqs WHERE service_id = ? ORDER BY sort_order ASC', [$serviceId]);
    }
}
