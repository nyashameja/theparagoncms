<?php

namespace App\Models;

use App\Support\Database;

class Testimonial extends BaseModel
{
    protected static string $table = 'testimonials';

    public static function approved(int $limit = 0): array
    {
        $sql = "SELECT * FROM testimonials WHERE status = 'approved' AND deleted_at IS NULL ORDER BY sort_order ASC, created_at DESC";
        if ($limit > 0) $sql .= " LIMIT $limit";
        return Database::select($sql);
    }

    public static function featured(int $limit = 6): array
    {
        return Database::select(
            "SELECT * FROM testimonials WHERE status = 'approved' AND is_featured = 1 AND deleted_at IS NULL ORDER BY sort_order ASC LIMIT ?",
            [$limit]
        );
    }
}
