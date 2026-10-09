package com.portal;
 
import java.io.IOException;
import javax.servlet.*;
import javax.servlet.annotation.WebFilter;
import javax.servlet.http.*;
 
/** Blocks every protected URL unless the user has logged in. */
@WebFilter(urlPatterns = {"/students", "/file"})
public class AuthFilter implements Filter {
 
    @Override
    public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain)
            throws IOException, ServletException {
        HttpServletRequest req = (HttpServletRequest) request;
        HttpServletResponse resp = (HttpServletResponse) response;
 
        HttpSession session = req.getSession(false);        // existing session only
        boolean loggedIn = session != null && session.getAttribute("user") != null;
 
        if (loggedIn) {
            // do not let the browser cache protected pages after logout
            resp.setHeader("Cache-Control", "no-store");
            chain.doFilter(request, response);               // allow the request
        } else {
            resp.sendRedirect(req.getContextPath() + "/login.jsp");
        }
    }
}
