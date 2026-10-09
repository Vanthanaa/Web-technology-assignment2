<?php
// view_students.php - READ: list of all students with a small photo
require 'db.php';
include 'header.php';
 
// LEFT JOIN so students without a photo are still listed
$sql = "SELECT s.student_id, s.name, s.roll_number, s.email, s.course, f.file_path
        FROM students s
        LEFT JOIN student_files f ON f.student_id = s.student_id AND f.category = 'photo'
        ORDER BY s.student_id DESC";
$rows = $conn->query($sql);
?>
<h1>Student Records</h1>
<table class="data">
  <tr><th>Photo</th><th>Name</th><th>Roll No.</th><th>Course</th><th>Actions</th></tr>
  <?php if ($rows->num_rows === 0): ?>
    <tr><td colspan="5">No students registered yet.</td></tr>
  <?php endif; ?>
  <?php while ($r = $rows->fetch_assoc()): ?>
  <tr>
    <td><?php if ($r['file_path']): ?>
          <img class="thumb" src="uploads/<?= e($r['file_path']) ?>" alt="photo">
        <?php else: ?>&mdash;<?php endif; ?></td>
    <td><?= e($r['name']) ?></td>
    <td><?= e($r['roll_number']) ?></td>
    <td><?= e($r['course']) ?></td>
    <td>
      <a href="student_details.php?id=<?= (int)$r['student_id'] ?>">View</a> |
      <a href="edit_student.php?id=<?= (int)$r['student_id'] ?>">Edit</a> |
      <!-- POST form so a record cannot be deleted by just opening a link -->
      <form method="post" action="delete_student.php" style="display:inline"
            onsubmit="return confirm('Delete this student and all files?')">
        <input type="hidden" name="id" value="<?= (int)$r['student_id'] ?>">
        <button class="btn danger" style="padding:3px 10px">Delete</button>
      </form>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
<?php include 'footer.php'; ?>
