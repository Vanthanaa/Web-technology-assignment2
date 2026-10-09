<%@ page contentType="text/html;charset=UTF-8" %>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="top-bar">Student Admission Portal</header>
  <main class="card" style="max-width:380px">
    <h1>Login</h1>
    <% String error = (String) request.getAttribute("error"); %>
    <% if (error != null) { %>
      <div class="msg err"><%= error %></div>
    <% } %>
    <form method="post" action="login">
      <div class="field"><label>Username</label>
        <input type="text" name="username" required></div>
      <div class="field"><label>Password</label>
        <input type="password" name="password" required></div>
      <button class="btn primary" type="submit">Sign in</button>
    </form>
  </main>
</body>
</html>
