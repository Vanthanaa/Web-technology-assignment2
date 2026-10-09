package com.portal;
 
import java.io.IOException;
import java.util.List;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.*;
 
/** Loads students from MySQL and forwards them to the JSP view. */
@WebServlet("/students")
public class StudentListServlet extends HttpServlet {
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        try {
            List<Student> students = new StudentDAO().findAllWithFiles();
            req.setAttribute("students", students);          // data for the JSP
            req.getRequestDispatcher("/WEB-INF/views/students.jsp").forward(req, resp);
        } catch (Exception e) {
            throw new ServletException(e);
        }
    }
}
