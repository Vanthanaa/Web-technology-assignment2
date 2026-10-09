package com.portal;
 
/** One uploaded file (row of student_files). */
public class StudentFile {
    private int fileId;
    private String category, originalName, filePath, fileType, uploadDate;
    private int fileSize;
 
    public StudentFile(int fileId, String category, String originalName, String filePath,
                       String fileType, int fileSize, String uploadDate) {
        this.fileId = fileId; this.category = category; this.originalName = originalName;
        this.filePath = filePath; this.fileType = fileType;
        this.fileSize = fileSize; this.uploadDate = uploadDate;
    }
    public int getFileId()          { return fileId; }
    public String getCategory()     { return category; }
    public String getOriginalName() { return originalName; }
    public String getFilePath()     { return filePath; }
    public String getFileType()     { return fileType; }
    public int getFileSize()        { return fileSize; }
    public String getUploadDate()   { return uploadDate; }
    public boolean isImage()        { return !"pdf".equals(fileType); }
}
