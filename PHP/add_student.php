<?php
// add_student.php - CREATE: shows the form and inserts the record + files
require 'db.php';
$errors = [];
$old = ['name' => '', 'roll_number' => '', 'email' => '', 'course' => ''];
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Read and trim the input
    $d = [
        'name'        => trim($_POST['name'] ?? ''),
        'roll_number' => strtoupper(trim($_POST['roll_number'] ?? '')),
        'email'       => trim($_POST['email'] ?? ''),
        'course'      => trim($_POST['course'] ?? ''),
        'password'    => $_POST['password'] ?? '',
    ];
    $old = $d;
 
    // 2. Validate text fields and both files
    $errors = validate_student($d, true);
    foreach ([check_upload('photo', ['jpg', 'jpeg', 'png'], 'Photo'),
              check_upload('document', ['pdf', 'jpg', 'jpeg', 'png'], 'Document')] as $msg) {
        if ($msg !== '') $errors[] = $msg;
    }
 
    // 3. Reject duplicate roll number or email
    if (!$errors) {
        $st = $conn->prepare('SELECT student_id FROM students WHERE roll_number = ? OR email = ?');
        $st->bind_param('ss', $d['roll_number'], $d['email']);
        $st->execute();
        if ($st->get_result()->num_rows > 0) $errors[] = 'Roll number or email is already registered.';
    }
 
    // 4. Save everything inside one transaction
    if (!$errors) {
        $saved = [];                                          // files written so far
        $conn->begin_transaction();
        try {
            $hash = password_hash($d['password'], PASSWORD_DEFAULT);
            $st = $conn->prepare('INSERT INTO students (name, roll_number, email, password_hash, course)
                                  VALUES (?, ?, ?, ?, ?)');
            $st->bind_param('sssss', $d['name'], $d['roll_number'], $d['email'], $hash, $d['course']);
            $st->execute();
            $id = $conn->insert_id;
 
            foreach (['photo' => 'photo', 'document' => 'document'] as $field => $cat) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $info = save_upload($field);
                    $saved[] = $info[0];
                    save_metadata($conn, $id, $cat, $info);   // path, type, date in MySQL
                }
            }
            $conn->commit();
            flash('ok', 'Student registered successfully.');
            header('Location: view_students.php');
            exit;
        } catch (mysqli_sql_exception $ex) {
            $conn->rollback();                                // undo DB changes
            foreach ($saved as $p) remove_file($p);           // undo file changes
            $errors[] = 'Could not save the record. Please try again.';
        }
    }
}
include 'header.php';
?>
<h1>Add Student</h1>
<?php foreach ($errors as $m): ?><div class="msg err"><?= e($m) ?></div><?php endforeach; ?>
 
<form method="post" enctype="multipart/form-data">
  <div class="field"><label>Full Name</label>
    <input type="text" name="name" value="<?= e($old['name']) ?>" required></div>
  <div class="field"><label>Roll Number</label>
    <input type="text" name="roll_number" value="<?= e($old['roll_number']) ?>" required></div>
  <div class="field"><label>Email</label>
    <input type="email" name="email" value="<?= e($old['email']) ?>" required></div>
  <div class="field"><label>Password</label>
    <input type="password" name="password" required></div>
  <div class="field"><label>Course</label>
    <input type="text" name="course" value="<?= e($old['course']) ?>" required></div>
  <div class="field"><label>Profile Photo (JPG/PNG)</label>
    <input type="file" name="photo" accept=".jpg,.jpeg,.png"></div>
  <div class="field"><label>ID Proof / Certificate (PDF/JPG/PNG)</label>
    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"></div>
  <button class="btn primary" type="submit">Save Student</button>
</form>
<?php include 'footer.php'; ?>
