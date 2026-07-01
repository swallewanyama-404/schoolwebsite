<?php 

// INPUT SANITIZER — Run on EVERY $_POST/$_GET. No exceptions.

function clean($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


// ADMIN AUTHENTICATION

// Verify admin login — uses EMAIL amd PASSWORD
function loginAdmin($pdo, $email, $password) {
    $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE email = ? AND is_active = 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        // Update last_login timestamp
        $upd = $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?');
        $upd->execute([$admin['id']]);
        return $admin;
    }
    return false;
}

// Session guard — call at top of EVERY admin page
function requireAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ' . BASE_URL . 'admin/login.php');
        exit;
    }
}

// Get admin by ID
function getAdminById($pdo, $id) {
    $stmt = $pdo->prepare('SELECT id, name, email, role, profile_photo FROM admin_users WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}


// AUDIT LOG — Record every admin action
function logAction($pdo, ?int $admin_id,
 $action, $table_name, $record_id, $description) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $pdo->prepare(
        'INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    return $stmt->execute([$admin_id, $action, $table_name, $record_id, $description, $ip]);
}

// READ — Get recent audit log entries (admin panel)
function getAuditLog($pdo, $limit = 20) {
    $stmt = $pdo->prepare(
        'SELECT al.*, au.name AS admin_name
         FROM audit_log al
         LEFT JOIN admin_users au ON au.id = al.admin_id
         ORDER BY al.created_at DESC LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}



// SCHOOL INFO (settings)


// READ — Get a single setting by key
function getSetting($pdo, $key) {
    $stmt = $pdo->prepare('SELECT setting_value FROM school_info WHERE setting_key = ?');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : null;
}

// READ — Get ALL settings (admin settings page)
function getAllSettings($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM school_info ORDER BY setting_key ASC');
    $stmt->execute();
    return $stmt->fetchAll();
}

// UPDATE — Save a setting value
function updateSetting($pdo, $key, $value, $admin_id) {
    $stmt = $pdo->prepare(
        'UPDATE school_info SET setting_value = ?, updated_by = ? WHERE setting_key = ?'
    );
    return $stmt->execute([$value, $admin_id, $key]);
}



// NEWS CATEGORIES


// READ — Get all categories
function getAllCategories($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM news_categories ORDER BY name ASC');
    $stmt->execute();
    return $stmt->fetchAll();
}

// READ — Get single category by ID
function getCategoryById($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM news_categories WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}



// NEWS CRUD


// READ — Get published news using the VIEW (already joined with category + author)
function getPublishedNews($pdo, $limit = 5) {
    $stmt = $pdo->prepare(
        'SELECT * FROM vw_published_news LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// READ — Get single news article by ID
function getNewsById($pdo, $id) {
    $stmt = $pdo->prepare(
        'SELECT n.*, nc.name AS category_name, nc.color AS category_color, au.name AS author_name
         FROM news n
         LEFT JOIN news_categories nc ON nc.id = n.category_id
         LEFT JOIN admin_users au     ON au.id  = n.author_id
         WHERE n.id = ?'
    );
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// READ — Get single news article by slug
function getNewsBySlug($pdo, $slug) {
    $stmt = $pdo->prepare(
        'SELECT n.*, nc.name AS category_name, nc.color AS category_color, au.name AS author_name
         FROM news n
         LEFT JOIN news_categories nc ON nc.id = n.category_id
         LEFT JOIN admin_users au     ON au.id  = n.author_id
         WHERE n.slug = ? AND n.is_published = 1'
    );
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

// READ — Get ALL news for admin panel
function getAllNews($pdo) {
    $stmt = $pdo->prepare(
        'SELECT n.*, nc.name AS category_name
         FROM news n
         LEFT JOIN news_categories nc ON nc.id = n.category_id
         ORDER BY n.created_at DESC'
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

// CREATE — Add new news article
function createNews($pdo, $category_id, $title, $slug, $excerpt, $body, $image, $author_id) {
    $stmt = $pdo->prepare(
        'INSERT INTO news (category_id, title, slug, excerpt, body, featured_image, author_id, is_published)
         VALUES (?, ?, ?, ?, ?, ?, ?, 0)'
    );
    return $stmt->execute([$category_id, $title, $slug, $excerpt, $body, $image, $author_id]);
}

// UPDATE — Edit a news article
function updateNews($pdo, $id, $category_id, $title, $slug, $excerpt, $body, $image) {
    $stmt = $pdo->prepare(
        'UPDATE news SET category_id = ?, title = ?, slug = ?, excerpt = ?,
         body = ?, featured_image = ? WHERE id = ?'
    );
    return $stmt->execute([$category_id, $title, $slug, $excerpt, $body, $image, $id]);
}

// UPDATE — Toggle publish/unpublish
function togglePublish($pdo, $id, $status) {
    $published_at = $status ? 'NOW()' : 'NULL';
    $stmt = $pdo->prepare(
        "UPDATE news SET is_published = ?, published_at = $published_at WHERE id = ?"
    );
    return $stmt->execute([$status, $id]);
}

// UPDATE — Increment article view count
function incrementViews($pdo, $id) {
    $stmt = $pdo->prepare('UPDATE news SET views = views + 1 WHERE id = ?');
    return $stmt->execute([$id]);
}

// DELETE — Remove a news article
function deleteNews($pdo, $id) {
    $stmt = $pdo->prepare('DELETE FROM news WHERE id = ?');
    return $stmt->execute([$id]);
}



// EVENTS CRUD


// READ — Get upcoming events using the VIEW
function getUpcomingEvents($pdo, $limit = 5) {
    $stmt = $pdo->prepare('SELECT * FROM vw_upcoming_events LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// READ — Get ALL events (admin panel)
function getAllEvents($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM events ORDER BY event_date DESC');
    $stmt->execute();
    return $stmt->fetchAll();
}

// CREATE — Add a new event
function createEvent($pdo, $title, $description, $location, $event_date, $start_time, $end_time, $admin_id) {
    $stmt = $pdo->prepare(
        'INSERT INTO events (title, description, location, event_date, start_time, end_time, created_by)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    return $stmt->execute([$title, $description, $location, $event_date, $start_time, $end_time, $admin_id]);
}

// DELETE — Remove an event
function deleteEvent($pdo, $id) {
    $stmt = $pdo->prepare('DELETE FROM events WHERE id = ?');
    return $stmt->execute([$id]);
}



// DEPARTMENTS CRUD


// READ — Get all departments
function getAllDepartments($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM departments ORDER BY name ASC');
    $stmt->execute();
    return $stmt->fetchAll();
}

// CREATE — Add a department
function createDepartment($pdo, $name, $description, $head) {
    $stmt = $pdo->prepare(
        'INSERT INTO departments (name, description, head_of_dept) VALUES (?, ?, ?)'
    );
    return $stmt->execute([$name, $description, $head]);
}

// DELETE — Remove a department
function deleteDepartment($pdo, $id) {
    $stmt = $pdo->prepare('DELETE FROM departments WHERE id = ?');
    return $stmt->execute([$id]);
}



// STAFF CRUD


// READ — Get active staff using the VIEW (already joined with department)
function getAllStaff($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM vw_staff_directory');
    $stmt->execute();
    return $stmt->fetchAll();
}

// READ — Get management/leadership staff only
function getManagementStaff($pdo) {
    $stmt = $pdo->prepare(
        'SELECT * FROM vw_staff_directory WHERE is_management = 1 ORDER BY sort_order ASC'
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

// READ — Get single staff member
function getStaffById($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM staff WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// CREATE — Add a new staff member
function createStaff($pdo, $dept_id, $first_name, $last_name, $title, $role, $subjects, $qualification, $bio, $photo, $email, $is_management, $sort_order) {
    $stmt = $pdo->prepare(
        'INSERT INTO staff (department_id, first_name, last_name, title, role, subjects,
         qualification, bio, photo, email, is_management, sort_order)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    return $stmt->execute([$dept_id, $first_name, $last_name, $title, $role, $subjects,
                           $qualification, $bio, $photo, $email, $is_management, $sort_order]);
}

// UPDATE — Edit a staff member
function updateStaff($pdo, $id, $dept_id, $first_name, $last_name, $title, $role, $subjects, $bio, $photo, $is_active) {
    $stmt = $pdo->prepare(
        'UPDATE staff SET department_id = ?, first_name = ?, last_name = ?, title = ?,
         role = ?, subjects = ?, bio = ?, photo = ?, is_active = ? WHERE id = ?'
    );
    return $stmt->execute([$dept_id, $first_name, $last_name, $title, $role,
                           $subjects, $bio, $photo, $is_active, $id]);
}

// DELETE — Remove a staff member
function deleteStaff($pdo, $id) {
    $stmt = $pdo->prepare('DELETE FROM staff WHERE id = ?');
    return $stmt->execute([$id]);
}



// SUBJECTS CRUD


// READ — Get all subjects
function getAllSubjects($pdo) {
    $stmt = $pdo->prepare(
        'SELECT s.*, d.name AS department_name
         FROM subjects s
         LEFT JOIN departments d ON d.id = s.department_id
         ORDER BY s.sort_order ASC'
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

// READ — Get subjects by level (O_LEVEL or A_LEVEL)
function getSubjectsByLevel($pdo, $level) {
    $stmt = $pdo->prepare(
        "SELECT * FROM subjects WHERE FIND_IN_SET(?, level) ORDER BY sort_order ASC"
    );
    $stmt->execute([$level]);
    return $stmt->fetchAll();
}



// GALLERY CRUD


// READ — Get all published albums
function getPublishedAlbums($pdo) {
    $stmt = $pdo->prepare(
        'SELECT * FROM gallery_albums WHERE is_published = 1 ORDER BY sort_order ASC'
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

// READ — Get photos for a specific album
function getPhotosByAlbum($pdo, $album_id) {
    $stmt = $pdo->prepare(
        'SELECT * FROM gallery_photos WHERE album_id = ? ORDER BY sort_order ASC'
    );
    $stmt->execute([$album_id]);
    return $stmt->fetchAll();
}

// CREATE — Add photo to album
function addPhoto($pdo, $album_id, $filename, $caption, $admin_id) {
    $stmt = $pdo->prepare(
        'INSERT INTO gallery_photos (album_id, filename, caption, uploaded_by) VALUES (?, ?, ?, ?)'
    );
    return $stmt->execute([$album_id, $filename, $caption, $admin_id]);
}



// ADMISSIONS — REQUIREMENTS


// READ — Get requirements by level (S1 or S5)
function getRequirementsByLevel($pdo, $level) {
    $stmt = $pdo->prepare(
        'SELECT * FROM admissions_requirements WHERE level = ? ORDER BY sort_order ASC'
    );
    $stmt->execute([$level]);
    return $stmt->fetchAll();
}

// READ — Get all downloadable documents
function getAdmissionDocuments($pdo) {
    $stmt = $pdo->prepare(
        'SELECT * FROM admissions_documents WHERE is_active = 1 ORDER BY sort_order ASC'
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

// UPDATE — Increment download counter
function incrementDownloads($pdo, $id) {
    $stmt = $pdo->prepare('UPDATE admissions_documents SET downloads = downloads + 1 WHERE id = ?');
    return $stmt->execute([$id]);
}



// ADMISSIONS ENQUIRIES CRUD


// CREATE — Save an admissions enquiry (new table columns)
function saveEnquiry($pdo, $parent_name, $parent_phone, $parent_email, $student_name, $entry_level, $current_school, $ple_aggregate, $message) {
    $stmt = $pdo->prepare(
        'INSERT INTO admissions_enquiries
         (parent_name, parent_phone, parent_email, student_name, entry_level, current_school, ple_aggregate, message)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    return $stmt->execute([$parent_name, $parent_phone, $parent_email, $student_name,
                           $entry_level, $current_school, $ple_aggregate, $message]);
}

// READ — Get all enquiries (admin panel)
function getAllEnquiries($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM admissions_enquiries ORDER BY created_at DESC');
    $stmt->execute();
    return $stmt->fetchAll();
}

// UPDATE — Change enquiry status
function updateEnquiryStatus($pdo, $id, $status, $notes) {
    $stmt = $pdo->prepare(
        'UPDATE admissions_enquiries SET status = ?, admin_notes = ? WHERE id = ?'
    );
    return $stmt->execute([$status, $notes, $id]);
}

// DELETE — Remove an enquiry
function deleteEnquiry($pdo, $id) {
    $stmt = $pdo->prepare('DELETE FROM admissions_enquiries WHERE id = ?');
    return $stmt->execute([$id]);
}



// CONTACT MESSAGES CRUD


// CREATE — Save contact form submission (new table: contact_messages)
function saveContact($pdo, $name, $email, $phone, $subject, $message) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $pdo->prepare(
        'INSERT INTO contact_messages (name, email, phone, subject, message, ip_address)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    return $stmt->execute([$name, $email, $phone, $subject, $message, $ip]);
}

// READ — Get all contact messages (admin panel)
function getAllContacts($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM contact_messages ORDER BY created_at DESC');
    $stmt->execute();
    return $stmt->fetchAll();
}

// READ — Count unread messages using the VIEW
function getUnreadCount($pdo) {
    $stmt = $pdo->prepare('SELECT unread_count FROM vw_unread_messages');
    $stmt->execute();
    $row = $stmt->fetch();
    return $row ? $row['unread_count'] : 0;
}

// UPDATE — Mark message as read
function markMessageRead($pdo, $id) {
    $stmt = $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?');
    return $stmt->execute([$id]);
}

// DELETE — Remove a contact message
function deleteContact($pdo, $id) {
    $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = ?');
    return $stmt->execute([$id]);
}



// TESTIMONIALS CRUD


// READ — Get published testimonials
function getTestimonials($pdo) {
    $stmt = $pdo->prepare(
        'SELECT * FROM testimonials WHERE is_published = 1 ORDER BY sort_order ASC'
    );
    $stmt->execute();
    return $stmt->fetchAll();
}

// CREATE — Add testimonial
function createTestimonial($pdo, $author_name, $author_role, $content, $rating) {
    $stmt = $pdo->prepare(
        'INSERT INTO testimonials (author_name, author_role, content, rating) VALUES (?, ?, ?, ?)'
    );
    return $stmt->execute([$author_name, $author_role, $content, $rating]);
}



// FAQS CRUD


// READ — Get FAQs by category
function getFaqsByCategory($pdo, $category) {
    $stmt = $pdo->prepare(
        'SELECT * FROM faqs WHERE category = ? AND is_published = 1 ORDER BY sort_order ASC'
    );
    $stmt->execute([$category]);
    return $stmt->fetchAll();
}

// READ — Get ALL FAQs (admin panel)
function getAllFaqs($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM faqs ORDER BY category, sort_order ASC');
    $stmt->execute();
    return $stmt->fetchAll();
}



// PAGE CONTENT (CMS)


// READ — Get a single content block
function getPageContent($pdo, $page, $section) {
    $stmt = $pdo->prepare(
        'SELECT content FROM page_content WHERE page = ? AND section = ?'
    );
    $stmt->execute([$page, $section]);
    $row = $stmt->fetch();
    return $row ? $row['content'] : '';
}

// UPDATE — Save a content block
function updatePageContent($pdo, $page, $section, $content, $admin_id) {
    $stmt = $pdo->prepare(
        'UPDATE page_content SET content = ?, updated_by = ? WHERE page = ? AND section = ?'
    );
    return $stmt->execute([$content, $admin_id, $page, $section]);
}



// NEWSLETTER SUBSCRIBERS


// CREATE — Add a new subscriber
function addSubscriber($pdo, $email, $name) {
    $token = bin2hex(random_bytes(32));
    $stmt  = $pdo->prepare(
        'INSERT INTO newsletter_subscribers (email, name, confirm_token) VALUES (?, ?, ?)'
    );
    return $stmt->execute([$email, $name, $token]);
}

// UPDATE — Confirm subscription via token
function confirmSubscriber($pdo, $token) {
    $stmt = $pdo->prepare(
        'UPDATE newsletter_subscribers SET is_confirmed = 1, confirm_token = NULL WHERE confirm_token = ?'
    );
    return $stmt->execute([$token]);
}

// READ — Get all confirmed subscribers
function getSubscribers($pdo) {
    $stmt = $pdo->prepare(
        'SELECT * FROM newsletter_subscribers WHERE is_confirmed = 1 AND unsubscribed_at IS NULL'
    );
    $stmt->execute();
    return $stmt->fetchAll();
}
?>
