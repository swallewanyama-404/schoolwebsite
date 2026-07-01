-- Database: school_website_db
-- Run this entire file once in phpMyAdmin SQL tab


-- Create and select the database
CREATE DATABASE IF NOT EXISTS school_website_db;
USE school_website_db;

--  ADMIN Table

-- TABLE 1: admin_users
CREATE TABLE IF NOT EXISTS admin_users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    email         VARCHAR(200) NOT NULL UNIQUE,
    password      VARCHAR(255) NOT NULL,
    role          ENUM('super_admin','editor','staff') DEFAULT 'editor',
    profile_photo VARCHAR(500),
    last_login    DATETIME,
    is_active     TINYINT(1) DEFAULT 1,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLE school_info
CREATE TABLE IF NOT EXISTS school_info (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key   VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description   VARCHAR(300),
    updated_by    INT UNSIGNED,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- audit_log
CREATE TABLE IF NOT EXISTS audit_log (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id    INT UNSIGNED,
    action      VARCHAR(100) NOT NULL,
    table_name  VARCHAR(80),
    record_id   INT UNSIGNED,
    description TEXT,
    ip_address  VARCHAR(45),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_admin (admin_id, created_at)
) ENGINE=InnoDB;

-- TABLE  news_categories
CREATE TABLE IF NOT EXISTS news_categories (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL UNIQUE,
    slug       VARCHAR(120) NOT NULL UNIQUE,
    color      VARCHAR(7) DEFAULT '#1565C0',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLE news
CREATE TABLE IF NOT EXISTS news (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id    INT UNSIGNED,
    title          VARCHAR(400) NOT NULL,
    slug           VARCHAR(450) NOT NULL UNIQUE,
    excerpt        TEXT,
    body           LONGTEXT NOT NULL,
    featured_image VARCHAR(500),
    author_id      INT UNSIGNED,
    views          INT UNSIGNED DEFAULT 0,
    is_published   TINYINT(1) DEFAULT 0,
    is_featured    TINYINT(1) DEFAULT 0,
    published_at   DATETIME,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id)   REFERENCES admin_users(id)     ON DELETE SET NULL
) ENGINE=InnoDB;

-- TABLE events
CREATE TABLE IF NOT EXISTS events (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title          VARCHAR(300) NOT NULL,
    description    TEXT,
    location       VARCHAR(200),
    event_date     DATE NOT NULL,
    start_time     TIME,
    end_time       TIME,
    featured_image VARCHAR(500),
    is_published   TINYINT(1) DEFAULT 1,
    created_by     INT UNSIGNED,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_date (event_date, is_published)
) ENGINE=InnoDB;


-- TABLE  departments
CREATE TABLE IF NOT EXISTS departments (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(150) NOT NULL UNIQUE,
    description  TEXT,
    head_of_dept VARCHAR(200),
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLE  staff
CREATE TABLE IF NOT EXISTS staff (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_id INT UNSIGNED,
    first_name    VARCHAR(100) NOT NULL,
    last_name     VARCHAR(100) NOT NULL,
    title         VARCHAR(50) DEFAULT 'Mr',
    role          VARCHAR(200) NOT NULL,
    subjects      VARCHAR(300),
    qualification VARCHAR(300),
    bio           TEXT,
    photo         VARCHAR(500),
    email         VARCHAR(200),
    is_management TINYINT(1) DEFAULT 0,
    sort_order    SMALLINT DEFAULT 0,
    is_active     TINYINT(1) DEFAULT 1,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_active (is_active, sort_order)
) ENGINE=InnoDB;

-- TABLE subjects
CREATE TABLE IF NOT EXISTS subjects (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_id INT UNSIGNED,
    name          VARCHAR(150) NOT NULL,
    level         SET('O_LEVEL','A_LEVEL') NOT NULL,
    is_compulsory TINYINT(1) DEFAULT 0,
    description   TEXT,
    sort_order    SMALLINT DEFAULT 0,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- TABLE gallery_albums
CREATE TABLE IF NOT EXISTS gallery_albums (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(200) NOT NULL,
    description  TEXT,
    cover_image  VARCHAR(500),
    sort_order   SMALLINT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLE gallery_photos
CREATE TABLE IF NOT EXISTS gallery_photos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    album_id    INT UNSIGNED NOT NULL,
    filename    VARCHAR(500) NOT NULL,
    caption     VARCHAR(300),
    sort_order  SMALLINT DEFAULT 0,
    uploaded_by INT UNSIGNED,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (album_id)    REFERENCES gallery_albums(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id)   ON DELETE SET NULL,
    INDEX idx_album (album_id, sort_order)
) ENGINE=InnoDB;


-- TABLE admissions_requirements
CREATE TABLE IF NOT EXISTS admissions_requirements (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    level       ENUM('S1','S5','OTHER') NOT NULL,
    title       VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    sort_order  SMALLINT DEFAULT 0
) ENGINE=InnoDB;

-- TABLE admissions_documents
CREATE TABLE IF NOT EXISTS admissions_documents (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200) NOT NULL,
    description VARCHAR(300),
    filename    VARCHAR(500) NOT NULL,
    file_size   VARCHAR(20),
    level       ENUM('S1','S5','ALL') DEFAULT 'ALL',
    downloads   INT UNSIGNED DEFAULT 0,
    sort_order  SMALLINT DEFAULT 0,
    is_active   TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLE admissions_enquiries
CREATE TABLE IF NOT EXISTS admissions_enquiries (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_name    VARCHAR(200) NOT NULL,
    parent_phone   VARCHAR(30)  NOT NULL,
    parent_email   VARCHAR(200),
    student_name   VARCHAR(200) NOT NULL,
    entry_level    ENUM('S1','S5') NOT NULL,
    current_school VARCHAR(200),
    ple_aggregate  TINYINT UNSIGNED,
    uce_aggregate TINYINT UNSIGNED,
    message        TEXT,
    status         ENUM('new','contacted','enrolled','declined') DEFAULT 'new',
    admin_notes    TEXT,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status, created_at)
) ENGINE=InnoDB;

-- TABLE contact_messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(200) NOT NULL,
    email      VARCHAR(200) NOT NULL,
    phone      VARCHAR(30),
    subject    VARCHAR(300) NOT NULL,
    message    TEXT NOT NULL,
    ip_address VARCHAR(45),
    is_read    TINYINT(1) DEFAULT 0,
    replied_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_unread (is_read, created_at)
) ENGINE=InnoDB;

-- TABLE  testimonials
CREATE TABLE IF NOT EXISTS testimonials (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author_name  VARCHAR(200) NOT NULL,
    author_role  VARCHAR(100),
    photo        VARCHAR(500),
    content      TEXT NOT NULL,
    rating       TINYINT DEFAULT 5,
    sort_order   SMALLINT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLE 17: faqs
CREATE TABLE IF NOT EXISTS faqs (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question     VARCHAR(400) NOT NULL,
    answer       TEXT NOT NULL,
    category     ENUM('admissions','fees','academics','boarding','general') DEFAULT 'general',
    sort_order   SMALLINT DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cat (category, sort_order)
) ENGINE=InnoDB;

-- TABLE page_content
CREATE TABLE IF NOT EXISTS page_content (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page       VARCHAR(60)  NOT NULL,
    section    VARCHAR(100) NOT NULL,
    content    LONGTEXT NOT NULL,
    updated_by INT UNSIGNED,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_page_section (page, section),
    FOREIGN KEY (updated_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- TABLE newsletter_subscribers
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(200) NOT NULL UNIQUE,
    name            VARCHAR(200),
    is_confirmed    TINYINT(1) DEFAULT 0,
    confirm_token   VARCHAR(64),
    subscribed_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at DATETIME,
    INDEX idx_confirmed (is_confirmed)
) ENGINE=InnoDB;

-- STARTER DATA — Insert after all tables are created

-- Default admin account
-- Email: admin@school.ug
INSERT INTO admin_users (name, email, password, role) VALUES
('Admin', 'admin@stmarys.ac.ug', '$2y$10$6QfQdOlY1fSQU3ZLnmRIzurK.XKhVnt1/dslkyZRm1TEQ1JJ560ZS', 'super_admin');

-- School settings
INSERT INTO school_info (setting_key, setting_value) VALUES
('school_name',    'St. Mary\'s Secondary School'),
('school_phone',   '+256 700 123456'),
('school_email',   'info@stmarys.ac.ug'),
('school_address', 'Plot 45, Ntinda Road, Kampala'),
('founded_year',   '1985'),
('total_students', '1200');

-- News categories
INSERT INTO news_categories (name, slug, color) VALUES
('General',    'general',    '#607080'),
('Academics',  'academics',  '#1565C0'),
('Sports',     'sports',     '#1B5E20'),
('Events',     'events',     '#E65100'),
('Admissions', 'admissions', '#4A148C'),
('Notice',     'notice',     '#B71C1C');

-- Sample events
INSERT INTO events (title, location, event_date, start_time, created_by) VALUES
('Sports Day',       'School Grounds', '2026-08-15', '08:00:00', 1),
('Prize-Giving Day', 'Main Hall',      '2026-08-28', '10:00:00', 1),
('Parents Meeting',  'Main Hall',      '2026-07-10', '09:00:00', 1);

-- Departments
INSERT INTO departments (name, description) VALUES
('Sciences',   'Physics, Chemistry, Biology, Maths'),
('Arts',       'History, Geography, Literature'),
('Commerce',   'Economics, Commerce, Entrepreneurship'),
('ICT',        'Computer Studies, IT'),
('Languages',  'English, Kiswahili, French'),
('Humanities', 'CRE, IRE, General Paper'),
('Technical',  'Food & Nutrition, Art & Design');

-- Staff
INSERT INTO staff (first_name, last_name, title, role, is_management, sort_order) VALUES
('John',  'Ssemwanga', 'Mr',  'Headteacher',        1, 1),
('Agnes', 'Namirembe', 'Mrs', 'Deputy Headteacher',  1, 2);

-- Subjects
INSERT INTO subjects (name, level, is_compulsory, sort_order) VALUES
('English Language', 'O_LEVEL,A_LEVEL', 1, 1),
('Mathematics',      'O_LEVEL,A_LEVEL', 1, 2),
('Physics',          'O_LEVEL,A_LEVEL', 0, 3),
('Chemistry',        'O_LEVEL,A_LEVEL', 0, 4),
('Biology',          'O_LEVEL,A_LEVEL', 0, 5),
('Entrepreneurship', 'O_LEVEL',         1, 6),
('Computer Studies', 'O_LEVEL,A_LEVEL', 0, 7);

-- Gallery albums
INSERT INTO gallery_albums (name, sort_order) VALUES
('Sports Day 2025',       1),
('Prize-Giving Day 2025', 2),
('Science Fair 2025',     3),
('School Campus',         4);

-- Admissions requirements
INSERT INTO admissions_requirements (level, title, description, sort_order) VALUES
('S1', 'PLE Results',       'Minimum aggregate of 20 in PLE. Originals required.',               1),
('S1', 'Birth Certificate', 'Original birth certificate required.',                              2),
('S1', 'Passport Photos',   'Four recent colour passport-size photos.',                          3),
('S5', 'UCE Results',       'Minimum 6 passes including English.',                               1),
('S5', 'UCE Certificate',   'Original Uganda Certificate of Education required.',                2);

-- Admissions documents
INSERT INTO admissions_documents (title, filename, level) VALUES
('S1 Application Form', 'assets/downloads/s1-form.pdf', 'S1'),
('S5 Application Form', 'assets/downloads/s5-form.pdf', 'S5'),
('Fee Structure 2026',  'assets/downloads/fees.pdf',    'ALL');

-- Testimonials
INSERT INTO testimonials (author_name, author_role, content) VALUES
('Mary Nakato', 'Former Student 2025',  'St. Mary\'s gave me the foundation I needed. I scored 25 in UACE.'),
('Mr. Kaggwa',  'Parent of S4 Student', 'My daughter improved from D to A in Maths in one term. Amazing.');

-- FAQs
INSERT INTO faqs (question, answer, category, sort_order) VALUES
('What PLE aggregate is needed for S1?', 'Minimum aggregate of 20 points.',                          'admissions', 1),
('How do I pay fees?',                   'Pay via MTN Mobile Money to 0700 123456 or Stanbic Bank.', 'fees',       1);

-- Page content (CMS blocks)
INSERT INTO page_content (page, section, content) VALUES
('home',  'hero_title',    'Excellence in Education Since 1985'),
('home',  'hero_subtitle', 'Shaping leaders through quality education.'),
('about', 'mission',       'To provide holistic, values-based education.'),
('about', 'vision',        'To be Uganda\'s leading secondary school.'),
('about', 'core_values',   'Excellence, Integrity, Service, Innovation.');

-- VIEWS — Create after all 19 tables exist

-- VIEW 1: Published news pre-joined with category and author
CREATE OR REPLACE VIEW vw_published_news AS
SELECT
    n.id, n.title, n.slug, n.excerpt,
    n.featured_image, n.views, n.published_at,
    nc.name  AS category_name,
    nc.color AS category_color,
    au.name  AS author_name
FROM news n
LEFT JOIN news_categories nc ON nc.id = n.category_id
LEFT JOIN admin_users     au ON au.id = n.author_id
WHERE n.is_published = 1
ORDER BY n.published_at DESC;

-- VIEW 2: Upcoming events (today or future only)
CREATE OR REPLACE VIEW vw_upcoming_events AS
SELECT id, title, description, location, event_date, start_time, end_time
FROM events
WHERE is_published = 1 AND event_date >= CURDATE()
ORDER BY event_date ASC;

-- VIEW 3: Active staff pre-joined with department name
CREATE OR REPLACE VIEW vw_staff_directory AS
SELECT
    s.id, s.title, s.first_name, s.last_name,
    CONCAT(s.title, ' ', s.first_name, ' ', s.last_name) AS full_name,
    s.role, s.subjects, s.photo,
    s.is_management, s.sort_order,
    d.name AS department_name
FROM staff s
LEFT JOIN departments d ON d.id = s.department_id
WHERE s.is_active = 1
ORDER BY s.sort_order, s.last_name;

-- VIEW 4: Count of unread contact messages
CREATE OR REPLACE VIEW vw_unread_messages AS
SELECT COUNT(*) AS unread_count
FROM contact_messages
WHERE is_read = 0;

-- VIEW 5: Admissions enquiries grouped by status
CREATE OR REPLACE VIEW vw_admissions_summary AS
SELECT status, COUNT(*) AS total
FROM admissions_enquiries
GROUP BY status;

-- VERIFICATION — Run these after import to confirm everything

-- Test 1: Should return 3 rows (school_name, school_phone etc)
-- SELECT setting_key, setting_value FROM school_info LIMIT 3;

-- Test 2: Should return 6 rows (General, Academics, Sports...)
-- SELECT id, name, slug, color FROM news_categories ORDER BY name;

-- Test 3: Should return 2 staff rows
-- SELECT s.first_name, s.last_name, s.role, d.name AS dept
-- FROM staff s LEFT JOIN departments d ON d.id = s.department_id;

-- Test 4: Should return upcoming events (dates in the future)
-- SELECT title, event_date, location FROM vw_upcoming_events;

-- Test 5: Should return 23 (19 tables + 5 views - 1 = counted by MySQL)
-- SELECT COUNT(*) AS total FROM information_schema.TABLES
-- WHERE TABLE_SCHEMA = 'school_website_db';

