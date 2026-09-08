-- ============================================================
-- Migration 002: Settings, Menus, Media
-- ============================================================

CREATE TABLE IF NOT EXISTS settings (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key`      VARCHAR(200) NOT NULL UNIQUE,
    `value`    MEDIUMTEXT   DEFAULT NULL,
    created_at DATETIME     NOT NULL,
    updated_at DATETIME     NOT NULL,
    deleted_at DATETIME     DEFAULT NULL,
    INDEX idx_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS menus (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    location   VARCHAR(50)  NOT NULL UNIQUE,
    is_active  TINYINT(1)   NOT NULL DEFAULT 1,
    created_at DATETIME     NOT NULL,
    updated_at DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS menu_items (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_id     INT UNSIGNED NOT NULL,
    parent_id   INT UNSIGNED NOT NULL DEFAULT 0,
    label       VARCHAR(200) NOT NULL,
    url         VARCHAR(500) NOT NULL,
    target      VARCHAR(20)  NOT NULL DEFAULT '_self',
    css_class   VARCHAR(100) DEFAULT NULL,
    icon        VARCHAR(100) DEFAULT NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL,
    updated_at  DATETIME     NOT NULL,
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
    INDEX idx_menu (menu_id, parent_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS media (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED DEFAULT NULL,
    folder        VARCHAR(200) DEFAULT NULL,
    original_name VARCHAR(500) NOT NULL,
    stored_name   VARCHAR(500) NOT NULL,
    file_path     VARCHAR(1000) NOT NULL,
    file_size     BIGINT       NOT NULL DEFAULT 0,
    mime_type     VARCHAR(100) NOT NULL,
    extension     VARCHAR(20)  NOT NULL,
    width         INT UNSIGNED DEFAULT NULL,
    height        INT UNSIGNED DEFAULT NULL,
    webp_path     VARCHAR(1000) DEFAULT NULL,
    alt_text      VARCHAR(500) DEFAULT NULL,
    title         VARCHAR(500) DEFAULT NULL,
    caption       TEXT         DEFAULT NULL,
    credit        VARCHAR(300) DEFAULT NULL,
    focal_x       DECIMAL(5,2) DEFAULT NULL,
    focal_y       DECIMAL(5,2) DEFAULT NULL,
    created_at    DATETIME     NOT NULL,
    updated_at    DATETIME     NOT NULL,
    deleted_at    DATETIME     DEFAULT NULL,
    INDEX idx_folder (folder),
    INDEX idx_mime (mime_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS media_usage (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    media_id   INT UNSIGNED NOT NULL,
    model_type VARCHAR(100) NOT NULL,
    model_id   INT UNSIGNED NOT NULL,
    field      VARCHAR(100) DEFAULT NULL,
    created_at DATETIME     NOT NULL,
    INDEX idx_media (media_id),
    INDEX idx_model (model_type, model_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pages (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(300) NOT NULL,
    slug             VARCHAR(300) NOT NULL UNIQUE,
    template         VARCHAR(100) NOT NULL DEFAULT 'default',
    status           ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    meta_title       VARCHAR(300) DEFAULT NULL,
    meta_description VARCHAR(500) DEFAULT NULL,
    og_image         VARCHAR(500) DEFAULT NULL,
    is_noindex       TINYINT(1)   NOT NULL DEFAULT 0,
    schema_type      VARCHAR(100) DEFAULT NULL,
    created_at       DATETIME     NOT NULL,
    updated_at       DATETIME     NOT NULL,
    deleted_at       DATETIME     DEFAULT NULL,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS page_sections (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id     INT UNSIGNED NOT NULL,
    section_type VARCHAR(100) NOT NULL,
    heading     VARCHAR(500) DEFAULT NULL,
    content     MEDIUMTEXT   DEFAULT NULL,
    data        JSON         DEFAULT NULL,
    background  VARCHAR(50)  NOT NULL DEFAULT 'white',
    spacing     VARCHAR(50)  NOT NULL DEFAULT 'normal',
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL,
    updated_at  DATETIME     NOT NULL,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    INDEX idx_page (page_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS redirects (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    source_path VARCHAR(1000) NOT NULL,
    target      VARCHAR(1000) NOT NULL,
    code        SMALLINT     NOT NULL DEFAULT 301,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    note        VARCHAR(500) DEFAULT NULL,
    hit_count   INT UNSIGNED NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL,
    updated_at  DATETIME     NOT NULL,
    deleted_at  DATETIME     DEFAULT NULL,
    INDEX idx_source (source_path(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS not_found_logs (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    path         VARCHAR(1000) NOT NULL,
    full_url     VARCHAR(2000) DEFAULT NULL,
    referrer     VARCHAR(2000) DEFAULT NULL,
    ip_address   VARCHAR(45)   DEFAULT NULL,
    hits         INT UNSIGNED  NOT NULL DEFAULT 1,
    last_seen_at DATETIME      NOT NULL,
    created_at   DATETIME      NOT NULL,
    INDEX idx_path (path(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
