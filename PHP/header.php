<?php /* header.php - common top bar, navigation and flash message */ ?>
<!DOCTYPE html>
<html lang="en"><head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="../Frontend/style.css">
</head><body>
<header class="top-bar">
  Admin Panel &nbsp;|&nbsp;
  <a href="view_students.php" style="color:#fff">Students</a> &nbsp;
  <a href="add_student.php" style="color:#fff">Add Student</a>
</header>
<main class="card wide">
<?php show_flash(); ?>
