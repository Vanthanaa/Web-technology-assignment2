package com.portal;
 
import java.io.IOException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.*;
 
/** Handles the login form and creates the session. */
@WebServlet("/login")
public class LoginServlet extends HttpServlet {
 
    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        String user = req.getParameter("username");
        String pass = req.getParameter("password");
        try {
            if (user != null && pass != null && new StudentDAO().validateLogin(user.trim(), pass)) {
                req.changeSessionId();                       // stops session fixation
                HttpSession session = req.getSession(true);  // create the session
                session.setAttribute("user", user.trim());   // marks the user as logged in
                session.setMaxInactiveInterval(15 * 60);     // 15 minutes
                resp.sendRedirect(req.getContextPath() + "/students");
            } else {
                req.setAttribute("error", "Invalid username or password.");
                req.getRequestDispatcher("/login.jsp").forward(req, resp);
            }
        } catch (Exception e) {
            throw new ServletException(e);
        }
    }
 
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        req.getRequestDispatcher("/login.jsp").forward(req, resp);
    }
}
