<?php
// student_details.php - READ one student and display the uploaded files
require 'db.php';
$id = (int)($_GET['id'] ?? 0);
 
$st = $conn->prepare('SELECT * FROM students WHERE student_id = ?');
$st->bind_param('i', $id);
$st->execute();
$s = $st->get_result()->fetch_assoc();
if (!$s) { flash('err', 'Student not found.'); header('Location: view_students.php'); exit; }
 
$fs = $conn->prepare('SELECT * FROM student_files WHERE student_id = ? ORDER BY upload_date');
$fs->bind_param('i', $id);
$fs->execute();
$files = $fs->get_result();
include 'header.php';
?>
<h1><?= e($s['name']) ?></h1>
<p><b>Roll No:</b> <?= e($s['roll_number']) ?> &nbsp;
   <b>Email:</b> <?= e($s['email']) ?> &nbsp;
   <b>Course:</b> <?= e($s['course']) ?></p>
 
<h3>Uploaded Files</h3>
<table class="data">
  <tr><th>Preview</th><th>Name</th><th>Type</th><th>Size</th><th>Uploaded</th></tr>
  <?php while ($f = $files->fetch_assoc()): ?>
  <tr>
    <td><?php if ($f['file_type'] === 'pdf'): ?>
          <a href="uploads/<?= e($f['file_path']) ?>" target="_blank">Open PDF</a>
        <?php else: ?>
          <a href="uploads/<?= e($f['file_path']) ?>" target="_blank">
            <img class="thumb" src="uploads/<?= e($f['file_path']) ?>" alt=""></a>
        <?php endif; ?></td>
    <td><?= e($f['original_name']) ?> (<?= e($f['category']) ?>)</td>
    <td><?= strtoupper(e($f['file_type'])) ?></td>
    <td><?= round($f['file_size'] / 1024) ?> KB</td>
    <td><?= e($f['upload_date']) ?></td>
  </tr>
  <?php endwhile; ?>
</table>
<p><a class="btn" href="view_students.php">&larr; Back</a></p>
<?php include 'footer.php'; ?>
