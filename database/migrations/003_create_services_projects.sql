-- ============================================================
-- Migration 003: Services, Projects, Case Studies
-- ============================================================

CREATE TABLE IF NOT EXISTS industries (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(200) NOT NULL,
    slug             VARCHAR(200) NOT NULL UNIQUE,
    description      TEXT         DEFAULT NULL,
    hero_image       VARCHAR(500) DEFAULT NULL,
    icon             VARCHAR(100) DEFAULT NULL,
    status           ENUM('draft','published','archived') NOT NULL DEFAULT 'published',
    is_featured      TINYINT(1)   NOT NULL DEFAULT 0,
    sort_order       INT UNSIGNED NOT NULL DEFAULT 0,
    meta_title       VARCHAR(300) DEFAULT NULL,
    meta_description VARCHAR(500) DEFAULT NULL,
    created_at       DATETIME     NOT NULL,
    updated_at       DATETIME     NOT NULL,
    deleted_at       DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS locations (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(200) NOT NULL,
    slug             VARCHAR(200) NOT NULL UNIQUE,
    type             ENUM('primary','service_area') NOT NULL DEFAULT 'service_area',
    heading          VARCHAR(300) DEFAULT NULL,
    description      TEXT         DEFAULT NULL,
    address          VARCHAR(500) DEFAULT NULL,
    status           ENUM('draft','published','archived') NOT NULL DEFAULT 'published',
    sort_order       INT UNSIGNED NOT NULL DEFAULT 0,
    meta_title       VARCHAR(300) DEFAULT NULL,
    meta_description VARCHAR(500) DEFAULT NULL,
    schema_latitude  DECIMAL(10,8) DEFAULT NULL,
    schema_longitude DECIMAL(11,8) DEFAULT NULL,
    created_at       DATETIME     NOT NULL,
    updated_at       DATETIME     NOT NULL,
    deleted_at       DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS services (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(200) NOT NULL,
    slug                VARCHAR(200) NOT NULL UNIQUE,
    short_description   TEXT         DEFAULT NULL,
    long_description    MEDIUMTEXT   DEFAULT NULL,
    hero_headline       VARCHAR(500) DEFAULT NULL,
    hero_copy           TEXT         DEFAULT NULL,
    icon                VARCHAR(100) DEFAULT NULL,
    hero_image          VARCHAR(500) DEFAULT NULL,
    starting_price      VARCHAR(100) DEFAULT NULL,
    price_label         VARCHAR(200) DEFAULT NULL,
    primary_cta_label   VARCHAR(100) DEFAULT NULL,
    primary_cta_url     VARCHAR(500) DEFAULT NULL,
    secondary_cta_label VARCHAR(100) DEFAULT NULL,
    secondary_cta_url   VARCHAR(500) DEFAULT NULL,
    status              ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    is_featured         TINYINT(1)   NOT NULL DEFAULT 0,
    sort_order          INT UNSIGNED NOT NULL DEFAULT 0,
    meta_title          VARCHAR(300) DEFAULT NULL,
    meta_description    VARCHAR(500) DEFAULT NULL,
    og_image            VARCHAR(500) DEFAULT NULL,
    schema_type         VARCHAR(100) DEFAULT NULL,
    created_at          DATETIME     NOT NULL,
    updated_at          DATETIME     NOT NULL,
    deleted_at          DATETIME     DEFAULT NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_faqs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id  INT UNSIGNED NOT NULL,
    question    VARCHAR(500) NOT NULL,
    answer      TEXT         NOT NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_features (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id  INT UNSIGNED NOT NULL,
    title       VARCHAR(200) NOT NULL,
    description TEXT         DEFAULT NULL,
    icon        VARCHAR(100) DEFAULT NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_benefits (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id  INT UNSIGNED NOT NULL,
    title       VARCHAR(200) NOT NULL,
    description TEXT         DEFAULT NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_deliverables (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id  INT UNSIGNED NOT NULL,
    item        VARCHAR(300) NOT NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_process_steps (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id  INT UNSIGNED NOT NULL,
    step_number INT UNSIGNED NOT NULL DEFAULT 1,
    title       VARCHAR(200) NOT NULL,
    description TEXT         DEFAULT NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(300) NOT NULL,
    slug             VARCHAR(300) NOT NULL UNIQUE,
    client_name      VARCHAR(200) DEFAULT NULL,
    is_anonymised    TINYINT(1)   NOT NULL DEFAULT 0,
    summary          TEXT         DEFAULT NULL,
    cover_image      VARCHAR(500) DEFAULT NULL,
    card_image       VARCHAR(500) DEFAULT NULL,
    project_year     SMALLINT UNSIGNED DEFAULT NULL,
    project_url      VARCHAR(500) DEFAULT NULL,
    challenge        TEXT         DEFAULT NULL,
    approach         TEXT         DEFAULT NULL,
    deliverables     TEXT         DEFAULT NULL,
    outcome          TEXT         DEFAULT NULL,
    industry_id      INT UNSIGNED DEFAULT NULL,
    status           ENUM('draft','review','published','archived') NOT NULL DEFAULT 'draft',
    is_featured      TINYINT(1)   NOT NULL DEFAULT 0,
    display_priority INT UNSIGNED NOT NULL DEFAULT 99,
    view_count       INT UNSIGNED NOT NULL DEFAULT 0,
    meta_title       VARCHAR(300) DEFAULT NULL,
    meta_description VARCHAR(500) DEFAULT NULL,
    og_image         VARCHAR(500) DEFAULT NULL,
    created_at       DATETIME     NOT NULL,
    updated_at       DATETIME     NOT NULL,
    deleted_at       DATETIME     DEFAULT NULL,
    FOREIGN KEY (industry_id) REFERENCES industries(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_services (
    project_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (project_id, service_id),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_media (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    media_id    INT UNSIGNED DEFAULT NULL,
    url         VARCHAR(1000) DEFAULT NULL,
    type        ENUM('image','video','mockup_mobile','mockup_tablet','mockup_desktop') NOT NULL DEFAULT 'image',
    caption     VARCHAR(500) DEFAULT NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_results (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id  INT UNSIGNED NOT NULL,
    metric      VARCHAR(200) NOT NULL,
    value       VARCHAR(100) DEFAULT NULL,
    description TEXT         DEFAULT NULL,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS case_studies (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title             VARCHAR(300) NOT NULL,
    slug              VARCHAR(300) NOT NULL UNIQUE,
    project_id        INT UNSIGNED DEFAULT NULL,
    client_name       VARCHAR(200) DEFAULT NULL,
    sector            VARCHAR(200) DEFAULT NULL,
    executive_summary TEXT         DEFAULT NULL,
    challenge         TEXT         DEFAULT NULL,
    goals             TEXT         DEFAULT NULL,
    strategy          TEXT         DEFAULT NULL,
    creative_approach TEXT         DEFAULT NULL,
    technical_impl    TEXT         DEFAULT NULL,
    deliverables      TEXT         DEFAULT NULL,
    cover_image       VARCHAR(500) DEFAULT NULL,
    status            ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    is_featured       TINYINT(1)   NOT NULL DEFAULT 0,
    published_at      DATETIME     DEFAULT NULL,
    meta_title        VARCHAR(300) DEFAULT NULL,
    meta_description  VARCHAR(500) DEFAULT NULL,
    og_image          VARCHAR(500) DEFAULT NULL,
    created_at        DATETIME     NOT NULL,
    updated_at        DATETIME     NOT NULL,
    deleted_at        DATETIME     DEFAULT NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS case_study_blocks (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    case_study_id  INT UNSIGNED NOT NULL,
    block_type     ENUM('text','image','quote','statistics','comparison','video','gallery','cta') NOT NULL DEFAULT 'text',
    content        MEDIUMTEXT   DEFAULT NULL,
    data           JSON         DEFAULT NULL,
    sort_order     INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (case_study_id) REFERENCES case_studies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS case_study_results (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    case_study_id INT UNSIGNED NOT NULL,
    metric        VARCHAR(200) NOT NULL,
    value         VARCHAR(100) DEFAULT NULL,
    description   TEXT         DEFAULT NULL,
    sort_order    INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (case_study_id) REFERENCES case_studies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
