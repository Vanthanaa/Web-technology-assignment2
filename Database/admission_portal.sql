-- Database script for the Student Admission Portal (24CS082)
CREATE DATABASE IF NOT EXISTS admission_portal
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE admission_portal;
 
-- Main table: one row per registered student
CREATE TABLE IF NOT EXISTS students (
  student_id    INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  roll_number   VARCHAR(15)  NOT NULL UNIQUE,   -- prevents duplicate roll numbers
  email         VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,          -- bcrypt hash, never plain text
  course        VARCHAR(60)  NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;
 
-- Metadata of every uploaded file, linked to a student
CREATE TABLE IF NOT EXISTS student_files (
  file_id       INT AUTO_INCREMENT PRIMARY KEY,
  student_id    INT NOT NULL,
  category      ENUM('photo','document') NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  file_path     VARCHAR(255) NOT NULL,          -- relative to the uploads folder
  file_type     VARCHAR(10)  NOT NULL,          -- jpg, png, pdf
  file_size     INT NOT NULL,                   -- bytes
  upload_date   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
    ON DELETE CASCADE                           -- files rows go with the student
) ENGINE = InnoDB;
 
-- Login accounts used by the Servlet portal (Question 3)
CREATE TABLE IF NOT EXISTS admin_users (
  admin_id      INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50) NOT NULL UNIQUE,
  password_hash CHAR(64)    NOT NULL            -- SHA-256 hex digest
) ENGINE = InnoDB;
 
INSERT IGNORE INTO admin_users (username, password_hash)
VALUES ('admin', SHA2('Admin@123', 256));
