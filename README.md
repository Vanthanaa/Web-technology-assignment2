# Student Admission Portal

**U21CS501 – Web Technology, Assignment II**
Jayavanthanaa T (24CS082) – Department of Computer Science and Engineering, KPR Institute of Engineering and Technology

A three-part admission portal that shares one MySQL database (`admission_portal`):

| Part | Folder | Technology | What it does |
|---|---|---|---|
| Question 1 | `Frontend/` | HTML5, CSS3, JavaScript | Registration form with live validation, password strength meter, file checks, responsive layout |
| Question 2 | `PHP/` | PHP 8, MySQL (mysqli) | Add, view, edit and delete students; uploads stored in `uploads/images` and `uploads/documents`; file metadata in MySQL |
| Question 3 | `Servlet/` | Servlet, JSP, JDBC (Tomcat 9) | Login with session filter, student profiles rendered by JSP, files streamed by `FileServlet` |

## How to run

1. Start **Apache** and **MySQL** in XAMPP and import `Database/admission_portal.sql` in phpMyAdmin.
2. Copy this folder to `C:\xampp\htdocs\StudentAdmissionPortal`.
3. Question 1: http://localhost/StudentAdmissionPortal/Frontend/index.html
4. Question 2: http://localhost/StudentAdmissionPortal/PHP/view_students.php
5. Question 3: import `Servlet` into Eclipse as a Dynamic Web Project (source `src`, content `WebContent`),
   add `mysql-connector-j-8.x.jar` to `WEB-INF/lib`, check `uploadDir` in `web.xml`, and run on **Tomcat 9**.
   Open http://localhost:8080/StudentPortal/ and log in with **admin / Admin@123**.

## Test results

All 33 test cases in the report passed (Q1: 11/11, Q2: 12/12, Q3: 10/10). Output screenshots are in `screenshots/`.

## GitHub Repository

[To Be Added]
