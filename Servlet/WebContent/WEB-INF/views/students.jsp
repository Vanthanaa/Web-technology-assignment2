<%@ page contentType="text/html;charset=UTF-8" import="java.util.*,com.portal.*" %>
<%
    // Data placed in the request by StudentListServlet
    List<Student> students = (List<Student>) request.getAttribute("students");
%>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Profiles</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="top-bar">
    Student Profiles &nbsp;|&nbsp; Logged in as <%= session.getAttribute("user") %>
    &nbsp; <a href="logout" style="color:#fff">Logout</a>
  </header>
 
  <main class="card wide">
    <h1>Registered Students (<%= students.size() %>)</h1>
 
    <% if (students.isEmpty()) { %>
      <p>No students found.</p>
    <% } %>
 
    <% for (Student s : students) {                 // loop over every student %>
      <section style="display:flex;gap:18px;border-bottom:1px solid #c9d3df;padding:14px 0;flex-wrap:wrap">
        <% StudentFile photo = s.getPhoto(); %>
        <% if (photo != null) { %>
          <!-- the image is served by FileServlet, not by a direct path -->
          <img class="profile-img" src="file?id=<%= photo.getFileId() %>" alt="Photo of <%= s.getName() %>">
        <% } else { %>
          <div class="profile-img" style="background:#eef2f7;text-align:center;line-height:140px">No photo</div>
        <% } %>
 
        <div>
          <h3 style="margin:0"><%= s.getName() %></h3>
          <p style="margin:4px 0"><%= s.getRollNumber() %> &middot; <%= s.getCourse() %></p>
          <p style="margin:4px 0"><%= s.getEmail() %></p>
 
          <b>Files:</b>
          <% if (s.getFiles().isEmpty()) { %>
            <i>none uploaded</i>
          <% } else { %>
            <ul>
            <% for (StudentFile f : s.getFiles()) {  // loop over this student's files %>
              <li>
                <a href="file?id=<%= f.getFileId() %>" target="_blank">
                  <%= f.getOriginalName() %></a>
                (<%= f.getCategory() %>, <%= f.getFileType().toUpperCase() %>,
                 <%= f.getFileSize() / 1024 %> KB)
              </li>
            <% } %>
            </ul>
          <% } %>
        </div>
      </section>
    <% } %>
  </main>
</body>
</html>
