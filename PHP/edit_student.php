<?php
// edit_student.php - UPDATE student details and optionally add/replace files
require 'db.php';
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$errors = [];
 
$st = $conn->prepare('SELECT * FROM students WHERE student_id = ?');
$st->bind_param('i', $id);
$st->execute();
$s = $st->get_result()->fetch_assoc();
if (!$s) { flash('err', 'Student not found.'); header('Location: view_students.php'); exit; }
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = [
        'name'        => trim($_POST['name'] ?? ''),
        'roll_number' => strtoupper(trim($_POST['roll_number'] ?? '')),
        'email'       => trim($_POST['email'] ?? ''),
        'course'      => trim($_POST['course'] ?? ''),
        'password'    => '',
    ];
    $errors = validate_student($d, false);                    // password not changed here
    foreach ([check_upload('photo', ['jpg', 'jpeg', 'png'], 'Photo'),
              check_upload('document', ['pdf', 'jpg', 'jpeg', 'png'], 'Document')] as $msg) {
        if ($msg !== '') $errors[] = $msg;
    }
    if (!$errors) {                                           // duplicate check excluding self
        $c = $conn->prepare('SELECT student_id FROM students
                             WHERE (roll_number = ? OR email = ?) AND student_id <> ?');
        $c->bind_param('ssi', $d['roll_number'], $d['email'], $id);
        $c->execute();
        if ($c->get_result()->num_rows > 0) $errors[] = 'Roll number or email belongs to another student.';
    }
    if (!$errors) {
        $oldFiles = [];                                       // replaced files to delete later
        $newFiles = [];
        $conn->begin_transaction();
        try {
            $u = $conn->prepare('UPDATE students SET name=?, roll_number=?, email=?, course=?
                                 WHERE student_id=?');
            $u->bind_param('ssssi', $d['name'], $d['roll_number'], $d['email'], $d['course'], $id);
            $u->execute();
 
            foreach (['photo' => 'photo', 'document' => 'document'] as $field => $cat) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    // collect the old file of this category, then remove its row
                    $q = $conn->prepare('SELECT file_path FROM student_files WHERE student_id=? AND category=?');
                    $q->bind_param('is', $id, $cat);
                    $q->execute();
                    foreach ($q->get_result() as $row) $oldFiles[] = $row['file_path'];
                    $del = $conn->prepare('DELETE FROM student_files WHERE student_id=? AND category=?');
                    $del->bind_param('is', $id, $cat);
                    $del->execute();
 
                    $info = save_upload($field);
                    $newFiles[] = $info[0];
                    save_metadata($conn, $id, $cat, $info);
                }
            }
            $conn->commit();
            foreach ($oldFiles as $p) remove_file($p);        // only after a successful commit
            flash('ok', 'Student updated successfully.');
            header('Location: view_students.php');
            exit;
        } catch (mysqli_sql_exception $ex) {
            $conn->rollback();
            foreach ($newFiles as $p) remove_file($p);
            $errors[] = 'Update failed. Please try again.';
        }
    }
    $s = array_merge($s, $d);                                 // keep typed values on error
}
include 'header.php';
?>
<h1>Edit Student</h1>
<?php foreach ($errors as $m): ?><div class="msg err"><?= e($m) ?></div><?php endforeach; ?>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= $id ?>">
  <div class="field"><label>Full Name</label><input type="text" name="name" value="<?= e($s['name']) ?>"></div>
  <div class="field"><label>Roll Number</label><input type="text" name="roll_number" value="<?= e($s['roll_number']) ?>"></div>
  <div class="field"><label>Email</label><input type="email" name="email" value="<?= e($s['email']) ?>"></div>
  <div class="field"><label>Course</label><input type="text" name="course" value="<?= e($s['course']) ?>"></div>
  <div class="field"><label>New Photo (optional)</label><input type="file" name="photo" accept=".jpg,.jpeg,.png"></div>
  <div class="field"><label>New Document (optional)</label><input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"></div>
  <button class="btn primary" type="submit">Update</button>
  <a class="btn" href="view_students.php">Cancel</a>
</form>
<?php include 'footer.php'; ?>
