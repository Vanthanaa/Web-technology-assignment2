package com.portal;
 
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
 
/** Creates JDBC connections to the admission_portal database. */
public class DBUtil {
    private static final String URL  =
        "jdbc:mysql://localhost:3306/admission_portal?useSSL=false&serverTimezone=UTC";
    private static final String USER = "root";
    private static final String PASS = "";
 
    static {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");   // load the MySQL driver once
        } catch (ClassNotFoundException e) {
            throw new ExceptionInInitializerError(e);
        }
    }
 
    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection(URL, USER, PASS);
    }
}
