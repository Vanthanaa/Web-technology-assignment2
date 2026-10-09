<?php
// delete_student.php - DELETE a student, the file rows (cascade) and the files on disk
require 'db.php';
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {                  // refuse direct URL access
    flash('err', 'Invalid request.');
    header('Location: view_students.php');
    exit;
}
$id = (int)($_POST['id'] ?? 0);
 
// 1. Remember the file paths before the rows disappear
$q = $conn->prepare('SELECT file_path FROM student_files WHERE student_id = ?');
$q->bind_param('i', $id);
$q->execute();
$paths = array_column($q->get_result()->fetch_all(MYSQLI_ASSOC), 'file_path');
 
// 2. Delete the student; ON DELETE CASCADE removes student_files rows
$d = $conn->prepare('DELETE FROM students WHERE student_id = ?');
$d->bind_param('i', $id);
$d->execute();
 
// 3. Remove the physical files
foreach ($paths as $p) remove_file($p);
 
flash('ok', $d->affected_rows ? 'Student deleted.' : 'Student not found.');
header('Location: view_students.php');
