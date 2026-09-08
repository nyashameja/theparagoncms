-- ============================================================
-- Migration 004: Articles, Testimonials, Trust Content, Packages
-- ============================================================

CREATE TABLE IF NOT EXISTS article_categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(200) NOT NULL,
    slug        VARCHAR(200) NOT NULL UNIQUE,
    description TEXT         DEFAULT NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL,
    updated_at  DATETIME     NOT NULL,
    deleted_at  DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_tags (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    slug       VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS articles (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(300) NOT NULL,
    slug             VARCHAR(300) NOT NULL UNIQUE,
    excerpt          TEXT         DEFAULT NULL,
    content          MEDIUMTEXT   DEFAULT NULL,
    author_id        INT UNSIGNED DEFAULT NULL,
    featured_image   VARCHAR(500) DEFAULT NULL,
    og_image         VARCHAR(500) DEFAULT NULL,
    status           ENUM('draft','review','scheduled','published','archived') NOT NULL DEFAULT 'draft',
    is_featured      TINYINT(1)   NOT NULL DEFAULT 0,
    allow_comments   TINYINT(1)   NOT NULL DEFAULT 0,
    published_at     DATETIME     DEFAULT NULL,
    meta_title       VARCHAR(300) DEFAULT NULL,
    meta_description VARCHAR(500) DEFAULT NULL,
    canonical_url    VARCHAR(500) DEFAULT NULL,
    schema_type      VARCHAR(100) DEFAULT 'Article',
    view_count       INT UNSIGNED NOT NULL DEFAULT 0,
    created_at       DATETIME     NOT NULL,
    updated_at       DATETIME     NOT NULL,
    deleted_at       DATETIME     DEFAULT NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_published (published_at, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_category_map (
    article_id  INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, category_id),
    FOREIGN KEY (article_id)  REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES article_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_tag_map (
    article_id INT UNSIGNED NOT NULL,
    tag_id     INT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id)     REFERENCES article_tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_revisions (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id       INT UNSIGNED NOT NULL,
    user_id          INT UNSIGNED DEFAULT NULL,
    content_snapshot JSON         NOT NULL,
    created_at       DATETIME     NOT NULL,
    INDEX idx_article (article_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_service_map (
    article_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonials (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author_name   VARCHAR(200) NOT NULL,
    author_title  VARCHAR(200) DEFAULT NULL,
    author_company VARCHAR(200) DEFAULT NULL,
    author_photo  VARCHAR(500) DEFAULT NULL,
    content       TEXT         NOT NULL,
    rating        TINYINT UNSIGNED DEFAULT NULL,
    service_id    INT UNSIGNED DEFAULT NULL,
    project_id    INT UNSIGNED DEFAULT NULL,
    case_study_id INT UNSIGNED DEFAULT NULL,
    source        VARCHAR(100) DEFAULT NULL,
    source_url    VARCHAR(500) DEFAULT NULL,
    status        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    is_featured   TINYINT(1)   NOT NULL DEFAULT 0,
    sort_order    INT UNSIGNED NOT NULL DEFAULT 0,
    permission_granted TINYINT(1) NOT NULL DEFAULT 0,
    created_at    DATETIME     NOT NULL,
    updated_at    DATETIME     NOT NULL,
    deleted_at    DATETIME     DEFAULT NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS client_logos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(200) NOT NULL,
    logo        VARCHAR(500) NOT NULL,
    website     VARCHAR(500) DEFAULT NULL,
    status      ENUM('pending','approved','hidden') NOT NULL DEFAULT 'pending',
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    permission_granted TINYINT(1) NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL,
    updated_at  DATETIME     NOT NULL,
    deleted_at  DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS trust_statistics (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    label       VARCHAR(200) NOT NULL,
    value       VARCHAR(100) NOT NULL,
    description VARCHAR(300) DEFAULT NULL,
    icon        VARCHAR(100) DEFAULT NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL,
    updated_at  DATETIME     NOT NULL,
    deleted_at  DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS packages (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(200) NOT NULL,
    category         VARCHAR(100) NOT NULL DEFAULT 'general',
    short_description TEXT         DEFAULT NULL,
    starting_price   VARCHAR(100) DEFAULT NULL,
    price_note       VARCHAR(200) DEFAULT NULL,
    billing_type     ENUM('once_off','monthly','annual','custom') NOT NULL DEFAULT 'once_off',
    included_items   JSON         DEFAULT NULL,
    exclusions       TEXT         DEFAULT NULL,
    cta_label        VARCHAR(100) DEFAULT 'Get Started',
    cta_url          VARCHAR(500) DEFAULT '/quote',
    service_id       INT UNSIGNED DEFAULT NULL,
    is_featured      TINYINT(1)   NOT NULL DEFAULT 0,
    custom_quote     TINYINT(1)   NOT NULL DEFAULT 0,
    status           ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    sort_order       INT UNSIGNED NOT NULL DEFAULT 0,
    created_at       DATETIME     NOT NULL,
    updated_at       DATETIME     NOT NULL,
    deleted_at       DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS resources (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(300) NOT NULL,
    slug             VARCHAR(300) NOT NULL UNIQUE,
    description      TEXT         DEFAULT NULL,
    cover_image      VARCHAR(500) DEFAULT NULL,
    file_path        VARCHAR(1000) DEFAULT NULL,
    file_size        BIGINT       DEFAULT NULL,
    file_type        VARCHAR(50)  DEFAULT NULL,
    is_gated         TINYINT(1)   NOT NULL DEFAULT 0,
    download_count   INT UNSIGNED NOT NULL DEFAULT 0,
    status           ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    meta_title       VARCHAR(300) DEFAULT NULL,
    meta_description VARCHAR(500) DEFAULT NULL,
    created_at       DATETIME     NOT NULL,
    updated_at       DATETIME     NOT NULL,
    deleted_at       DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
