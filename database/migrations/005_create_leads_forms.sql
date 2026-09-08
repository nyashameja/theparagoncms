-- ============================================================
-- Migration 005: Leads, Forms, Analytics, Audit Requests
-- ============================================================

CREATE TABLE IF NOT EXISTS leads (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(200) NOT NULL,
    email           VARCHAR(200) NOT NULL,
    phone           VARCHAR(50)  DEFAULT NULL,
    company         VARCHAR(200) DEFAULT NULL,
    website         VARCHAR(500) DEFAULT NULL,
    service_type    VARCHAR(100) DEFAULT NULL,
    message         TEXT         DEFAULT NULL,
    raw_data        JSON         DEFAULT NULL,
    form_type       VARCHAR(100) NOT NULL DEFAULT 'contact',
    status          ENUM('new','contacted','qualified','proposal_sent','won','lost','spam','archived') NOT NULL DEFAULT 'new',
    priority        ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
    assigned_to     INT UNSIGNED DEFAULT NULL,
    follow_up_date  DATE         DEFAULT NULL,
    source          VARCHAR(100) DEFAULT NULL,
    landing_page    VARCHAR(500) DEFAULT NULL,
    utm_source      VARCHAR(100) DEFAULT NULL,
    utm_medium      VARCHAR(100) DEFAULT NULL,
    utm_campaign    VARCHAR(100) DEFAULT NULL,
    ip_address      VARCHAR(45)  DEFAULT NULL,
    user_agent      VARCHAR(500) DEFAULT NULL,
    consent         TINYINT(1)   NOT NULL DEFAULT 0,
    consent_at      DATETIME     DEFAULT NULL,
    is_duplicate    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at      DATETIME     NOT NULL,
    updated_at      DATETIME     NOT NULL,
    deleted_at      DATETIME     DEFAULT NULL,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    INDEX idx_assigned (assigned_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lead_notes (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id    INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED DEFAULT NULL,
    content    TEXT         NOT NULL,
    created_at DATETIME     NOT NULL,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    INDEX idx_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lead_activities (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id    INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED DEFAULT NULL,
    action     VARCHAR(100) NOT NULL,
    data       JSON         DEFAULT NULL,
    created_at DATETIME     NOT NULL,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    INDEX idx_lead (lead_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lead_attachments (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id       INT UNSIGNED NOT NULL,
    original_name VARCHAR(500) NOT NULL,
    stored_path   VARCHAR(1000) NOT NULL,
    file_size     BIGINT       DEFAULT NULL,
    created_at    DATETIME     NOT NULL,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tags (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lead_tags (
    lead_id INT UNSIGNED NOT NULL,
    tag_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (lead_id, tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_requests (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id      INT UNSIGNED DEFAULT NULL,
    website_url  VARCHAR(1000) NOT NULL,
    name         VARCHAR(200)  NOT NULL,
    email        VARCHAR(200)  NOT NULL,
    phone        VARCHAR(50)   DEFAULT NULL,
    company      VARCHAR(200)  DEFAULT NULL,
    notes        TEXT          DEFAULT NULL,
    status       ENUM('pending','in_progress','completed','sent') NOT NULL DEFAULT 'pending',
    assigned_to  INT UNSIGNED  DEFAULT NULL,
    created_at   DATETIME      NOT NULL,
    updated_at   DATETIME      NOT NULL,
    deleted_at   DATETIME      DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_reports (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    audit_request_id INT UNSIGNED NOT NULL,
    overall_score   TINYINT UNSIGNED DEFAULT NULL,
    performance_score TINYINT UNSIGNED DEFAULT NULL,
    seo_score       TINYINT UNSIGNED DEFAULT NULL,
    mobile_score    TINYINT UNSIGNED DEFAULT NULL,
    accessibility_score TINYINT UNSIGNED DEFAULT NULL,
    content_score   TINYINT UNSIGNED DEFAULT NULL,
    security_score  TINYINT UNSIGNED DEFAULT NULL,
    conversion_score TINYINT UNSIGNED DEFAULT NULL,
    findings        JSON         DEFAULT NULL,
    recommendations JSON         DEFAULT NULL,
    share_token     VARCHAR(64)  DEFAULT NULL UNIQUE,
    share_expires_at DATETIME    DEFAULT NULL,
    created_at      DATETIME     NOT NULL,
    updated_at      DATETIME     NOT NULL,
    FOREIGN KEY (audit_request_id) REFERENCES audit_requests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(200) NOT NULL UNIQUE,
    name       VARCHAR(200) DEFAULT NULL,
    consent    TINYINT(1)   NOT NULL DEFAULT 0,
    ip_address VARCHAR(45)  DEFAULT NULL,
    status     ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
    created_at DATETIME     NOT NULL,
    updated_at DATETIME     DEFAULT NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS analytics_events (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_type    VARCHAR(100) NOT NULL,
    resource_type VARCHAR(100) DEFAULT NULL,
    resource_id   INT UNSIGNED DEFAULT NULL,
    path          VARCHAR(1000) DEFAULT NULL,
    ip_address    VARCHAR(45)  DEFAULT NULL,
    user_agent    VARCHAR(500) DEFAULT NULL,
    referer       VARCHAR(500) DEFAULT NULL,
    data          JSON         DEFAULT NULL,
    created_at    DATETIME     NOT NULL,
    INDEX idx_type (event_type),
    INDEX idx_path (path(255)),
    INDEX idx_created (created_at),
    INDEX idx_resource (resource_type, resource_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS email_logs (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    to_address VARCHAR(200) NOT NULL,
    subject    VARCHAR(500) NOT NULL,
    status     ENUM('sent','failed','queued') NOT NULL DEFAULT 'queued',
    error      TEXT         DEFAULT NULL,
    attempts   TINYINT      NOT NULL DEFAULT 1,
    created_at DATETIME     NOT NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS consent_records (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(200) NOT NULL,
    consent_type VARCHAR(100) NOT NULL,
    policy_version VARCHAR(20) DEFAULT NULL,
    ip_address   VARCHAR(45)  DEFAULT NULL,
    created_at   DATETIME     NOT NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
