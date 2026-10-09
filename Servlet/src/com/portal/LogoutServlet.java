package com.portal;
 
import java.io.IOException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.*;
 
/** Destroys the session and returns to the login page. */
@WebServlet("/logout")
public class LogoutServlet extends HttpServlet {
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        HttpSession session = req.getSession(false);       // do not create a new one
        if (session != null) session.invalidate();
        resp.sendRedirect(req.getContextPath() + "/login.jsp");
    }
}
