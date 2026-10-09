<?php
// db.php - connection, upload settings and helper functions (included by every page)
session_start();                                   // used for flash messages
 
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'admission_portal';
 
const UPLOAD_ROOT = __DIR__ . '/uploads/';         // physical folder
const MAX_BYTES   = 2 * 1024 * 1024;               // 2 MB limit
 
// Throw exceptions for SQL errors so they can be caught with try/catch
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $ex) {
    die('Database connection failed. Please start MySQL and import the SQL script.');
}
 
// Escape output to stop cross-site scripting
function e($text) { return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8'); }
 
// Store a one-time message and read it back on the next page
function flash($type, $text) { $_SESSION['flash'] = [$type, $text]; }
function show_flash() {
    if (!empty($_SESSION['flash'])) {
        [$type, $text] = $_SESSION['flash'];
        echo '<div class="msg ' . ($type === 'ok' ? 'ok' : 'err') . '">' . e($text) . '</div>';
        unset($_SESSION['flash']);
    }
}
 
/**
 * Validate one uploaded file.
 * Returns an error string, or '' when the file is acceptable (or not supplied).
 */
function check_upload($field, $allowedExt, $label) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return '';                                  // optional field left empty
    }
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK)              return "$label could not be uploaded.";
    if ($f['size'] > MAX_BYTES)                     return "$label must be 2 MB or smaller.";
 
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true))         return "$label: only " . strtoupper(implode(', ', $allowedExt)) . ' allowed.';
 
    // Look at the real file content, not just the extension
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $okMime = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'pdf' => 'application/pdf'];
    if ($mime !== $okMime[$ext])                    return "$label: content does not match the file extension.";
    return '';
}
 
/**
 * Move a validated upload into uploads/images or uploads/documents.
 * A unique name (uniqid) guarantees an existing file is never overwritten.
 * Returns [relativePath, type, size, originalName].
 */
function save_upload($field) {
    $f   = $_FILES[$field];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $sub = ($ext === 'pdf') ? 'documents/' : 'images/';
    do {
        $name = uniqid('f_', true) . '.' . $ext;    // e.g. f_65a1b2c3d4e5f6.12345678.jpg
    } while (file_exists(UPLOAD_ROOT . $sub . $name));
    move_uploaded_file($f['tmp_name'], UPLOAD_ROOT . $sub . $name);
    return [$sub . $name, $ext, $f['size'], $f['name']];
}
 
// Insert one row of file metadata linked to a student
function save_metadata($conn, $studentId, $category, $info) {
    [$path, $type, $size, $orig] = $info;
    $st = $conn->prepare('INSERT INTO student_files
        (student_id, category, original_name, file_path, file_type, file_size)
        VALUES (?, ?, ?, ?, ?, ?)');
    $st->bind_param('issssi', $studentId, $category, $orig, $path, $type, $size);
    $st->execute();
}
 
// Delete a stored file from disk (ignores missing files)
function remove_file($relativePath) {
    $full = UPLOAD_ROOT . $relativePath;
    if (is_file($full)) unlink($full);
}
 
// Shared server-side validation for the text fields
function validate_student($d, $needPassword) {
    $err = [];
    if (!preg_match('/^[A-Za-z][A-Za-z .]{2,59}$/', $d['name']))               $err[] = 'Enter a valid name.';
    if (!preg_match('/^[0-9]{2}[A-Za-z]{2,4}[0-9]{3}$/', $d['roll_number']))   $err[] = 'Roll number format is invalid.';
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL))                       $err[] = 'Enter a valid email.';
    if ($d['course'] === '')                                                   $err[] = 'Select a course.';
    if ($needPassword && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $d['password']))
        $err[] = 'Password needs 8+ characters with upper, lower, digit and symbol.';
    return $err;
}
