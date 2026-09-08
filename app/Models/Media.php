<?php

namespace App\Models;

use App\Support\Database;

class Media extends BaseModel
{
    protected static string $table = 'media';

    public static function upload(array $file, int $userId = 0, string $folder = ''): array
    {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'mp4', 'webm', 'ogg', 'mp3', 'wav', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'zip'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            throw new \RuntimeException('File type not allowed.');
        }

        // MIME check
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'application/pdf', 'video/mp4', 'video/webm', 'audio/ogg', 'audio/mpeg',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv', 'application/zip',
        ];

        if (!in_array($mime, $allowedMimes)) {
            throw new \RuntimeException('File MIME type not allowed.');
        }

        if ($file['size'] > 20 * 1024 * 1024) {
            throw new \RuntimeException('File exceeds maximum size of 20MB.');
        }

        // Generate safe filename
        $safeName = bin2hex(random_bytes(16)) . '.' . $ext;
        $year     = date('Y');
        $month    = date('m');
        $relDir   = "uploads/$year/$month";
        $absDir   = public_path($relDir);

        if (!is_dir($absDir)) {
            mkdir($absDir, 0755, true);
        }

        $destPath = "$absDir/$safeName";
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw new \RuntimeException('Failed to move uploaded file.');
        }

        // Auto-orient images and generate WebP
        $webpPath = null;
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
            self::autoOrient($destPath);
            $webpPath = self::generateWebP($destPath, "$absDir/$safeName");
        }

        $originalName = htmlspecialchars(pathinfo($file['name'], PATHINFO_FILENAME), ENT_QUOTES);
        $id = Database::insert(
            'INSERT INTO media (user_id, folder, original_name, stored_name, file_path, file_size, mime_type, extension, webp_path, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [$userId, $folder, $originalName, $safeName, "$relDir/$safeName", $file['size'], $mime, $ext,
             $webpPath ? "$relDir/" . basename($webpPath) : null]
        );

        return ['id' => $id, 'path' => "$relDir/$safeName", 'url' => url("$relDir/$safeName")];
    }

    private static function autoOrient(string $path): void
    {
        if (!function_exists('exif_read_data') || !function_exists('imagecreatefromjpeg')) return;
        try {
            $exif = @exif_read_data($path);
            if (!$exif || !isset($exif['Orientation'])) return;
            $img = imagecreatefromjpeg($path);
            if (!$img) return;
            $orientation = $exif['Orientation'];
            if ($orientation === 3) $img = imagerotate($img, 180, 0);
            elseif ($orientation === 6) $img = imagerotate($img, -90, 0);
            elseif ($orientation === 8) $img = imagerotate($img, 90, 0);
            imagejpeg($img, $path, 90);
            imagedestroy($img);
        } catch (\Throwable) {}
    }

    private static function generateWebP(string $sourcePath, string $destBase): ?string
    {
        if (!function_exists('imagewebp')) return null;
        try {
            $mime = mime_content_type($sourcePath);
            $img = match ($mime) {
                'image/jpeg' => imagecreatefromjpeg($sourcePath),
                'image/png'  => imagecreatefrompng($sourcePath),
                'image/gif'  => imagecreatefromgif($sourcePath),
                default      => null,
            };
            if (!$img) return null;
            $webpPath = preg_replace('/\.[^.]+$/', '.webp', $destBase);
            imagewebp($img, $webpPath, 85);
            imagedestroy($img);
            return $webpPath;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function search(string $query, string $type = '', int $page = 1, int $perPage = 30): array
    {
        $where  = ['deleted_at IS NULL'];
        $params = [];

        if ($query) {
            $where[]  = '(original_name LIKE ? OR alt_text LIKE ?)';
            $q = '%' . $query . '%';
            array_push($params, $q, $q);
        }

        if ($type) {
            if ($type === 'image') {
                $where[] = "mime_type LIKE 'image/%'";
            } elseif ($type === 'video') {
                $where[] = "mime_type LIKE 'video/%'";
            } elseif ($type === 'document') {
                $where[] = "mime_type NOT LIKE 'image/%' AND mime_type NOT LIKE 'video/%'";
            }
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);
        $total  = (int) (Database::selectOne("SELECT COUNT(*) as c FROM media $whereClause", $params)['c'] ?? 0);
        $offset = ($page - 1) * $perPage;
        $data   = Database::select("SELECT * FROM media $whereClause ORDER BY created_at DESC LIMIT $perPage OFFSET $offset", $params);

        return ['data' => $data, 'total' => $total, 'current_page' => $page, 'per_page' => $perPage, 'last_page' => (int) ceil($total / $perPage)];
    }
}
