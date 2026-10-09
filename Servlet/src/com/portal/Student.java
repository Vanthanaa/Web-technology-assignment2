package com.portal;
 
import java.util.ArrayList;
import java.util.List;
 
/** A student together with the list of his or her uploaded files. */
public class Student {
    private int id;
    private String name, rollNumber, email, course;
    private List<StudentFile> files = new ArrayList<>();
 
    public Student(int id, String name, String rollNumber, String email, String course) {
        this.id = id; this.name = name; this.rollNumber = rollNumber;
        this.email = email; this.course = course;
    }
    public int getId()               { return id; }
    public String getName()          { return name; }
    public String getRollNumber()    { return rollNumber; }
    public String getEmail()         { return email; }
    public String getCourse()        { return course; }
    public List<StudentFile> getFiles() { return files; }
 
    /** Returns the profile photo or null when the student has none. */
    public StudentFile getPhoto() {
        for (StudentFile f : files) if ("photo".equals(f.getCategory())) return f;
        return null;
    }
}
