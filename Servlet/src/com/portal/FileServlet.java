package com.portal;
 
import java.io.*;
import java.nio.file.*;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.*;
 
/**
 * Streams a stored image or document to the browser.
 * The client sends only a file id (/file?id=7); the real path comes from MySQL,
 * so the user can never request an arbitrary file on the server.
 */
@WebServlet("/file")
public class FileServlet extends HttpServlet {
 
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        int id;
        try {
            id = Integer.parseInt(req.getParameter("id"));
        } catch (NumberFormatException e) {
            resp.sendError(HttpServletResponse.SC_BAD_REQUEST);
            return;
        }
 
        try {
            StudentFile meta = new StudentDAO().findFile(id);
            if (meta == null) { resp.sendError(HttpServletResponse.SC_NOT_FOUND); return; }
 
            // uploadDir is configured in web.xml (the PHP uploads folder)
            Path base = Paths.get(getServletContext().getInitParameter("uploadDir")).normalize();
            Path file = base.resolve(meta.getFilePath()).normalize();
            if (!file.startsWith(base) || !Files.isRegularFile(file)) {
                resp.sendError(HttpServletResponse.SC_NOT_FOUND);
                return;
            }
 
            // Tell the browser what is coming
            String mime = getServletContext().getMimeType(file.getFileName().toString());
            resp.setContentType(mime != null ? mime : "application/octet-stream");
            resp.setContentLengthLong(Files.size(file));
            resp.setHeader("Content-Disposition",
                "inline; filename=\"" + meta.getOriginalName().replace("\"", "") + "\"");
 
            // Copy the file to the response in 4 KB blocks
            try (InputStream in = Files.newInputStream(file);
                 OutputStream out = resp.getOutputStream()) {
                byte[] buf = new byte[4096];
                int n;
                while ((n = in.read(buf)) != -1) out.write(buf, 0, n);
            }
        } catch (java.sql.SQLException e) {
            throw new ServletException(e);
        }
    }
}
