package com.portal;
 
import java.security.MessageDigest;
import java.sql.*;
import java.util.*;
 
/** All database access of the portal (JDBC with PreparedStatement). */
public class StudentDAO {
 
    /** Checks username and password against admin_users. */
    public boolean validateLogin(String username, String password) throws Exception {
        String sql = "SELECT 1 FROM admin_users WHERE username = ? AND password_hash = ?";
        try (Connection con = DBUtil.getConnection();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, username);
            ps.setString(2, sha256(password));
            try (ResultSet rs = ps.executeQuery()) {
                return rs.next();                       // true when a row matches
            }
        }
    }
 
    /** Loads every student and attaches the associated file records. */
    public List<Student> findAllWithFiles() throws SQLException {
        Map<Integer, Student> map = new LinkedHashMap<>();
        try (Connection con = DBUtil.getConnection()) {
            // 1. students
            try (Statement st = con.createStatement();
                 ResultSet rs = st.executeQuery(
                     "SELECT student_id, name, roll_number, email, course " +
                     "FROM students ORDER BY name")) {
                while (rs.next()) {
                    map.put(rs.getInt(1), new Student(rs.getInt(1), rs.getString(2),
                            rs.getString(3), rs.getString(4), rs.getString(5)));
                }
            }
            // 2. file metadata of all students
            try (Statement st = con.createStatement();
                 ResultSet rs = st.executeQuery(
                     "SELECT file_id, student_id, category, original_name, file_path, " +
                     "file_type, file_size, upload_date FROM student_files ORDER BY file_id")) {
                while (rs.next()) {
                    Student s = map.get(rs.getInt("student_id"));
                    if (s != null) s.getFiles().add(new StudentFile(
                        rs.getInt("file_id"), rs.getString("category"),
                        rs.getString("original_name"), rs.getString("file_path"),
                        rs.getString("file_type"), rs.getInt("file_size"),
                        rs.getString("upload_date")));
                }
            }
        }
        return new ArrayList<>(map.values());
    }
 
    /** Finds one file record by id (null when it does not exist). */
    public StudentFile findFile(int fileId) throws SQLException {
        String sql = "SELECT file_id, category, original_name, file_path, file_type, " +
                     "file_size, upload_date FROM student_files WHERE file_id = ?";
        try (Connection con = DBUtil.getConnection();
             PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, fileId);
            try (ResultSet rs = ps.executeQuery()) {
                if (!rs.next()) return null;
                return new StudentFile(rs.getInt(1), rs.getString(2), rs.getString(3),
                        rs.getString(4), rs.getString(5), rs.getInt(6), rs.getString(7));
            }
        }
    }
 
    /** SHA-256 hex digest, same as MySQL SHA2(text, 256). */
    private String sha256(String text) throws Exception {
        byte[] d = MessageDigest.getInstance("SHA-256").digest(text.getBytes("UTF-8"));
        StringBuilder sb = new StringBuilder();
        for (byte b : d) sb.append(String.format("%02x", b));
        return sb.toString();
    }
}
