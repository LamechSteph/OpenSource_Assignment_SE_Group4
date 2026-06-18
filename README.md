# Final Year Project Tracking System (FYPTS)

## Degree Program

Bachelor of Science in Software Engineering

## Group Number

[Group No: 04]

## Project Overview

The Final Year Project Tracking System (FYPTS) is a web-based application developed using PHP and MySQL. The system assists universities and academic departments in managing and tracking final year student projects.

The system allows administrators and supervisors to record project information, update project progress, monitor project status, and search projects by title. Students can view the status of their projects through the system.

## Project Description

The project was developed as part of the CP 222 Open Source Technologies course assignment. It demonstrates the use of open-source technologies including PHP, MySQL, Git, and GitHub.

Key Features:

- User authentication
- User management
- Add project information
- Edit project details
- Delete projects
- View projects
- Search projects by title
- Update project status
- Track project progress percentage
- Dashboard statistics
- Project progress (NEW FEATURE)

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- Bootstrap 5
- JavaScript
- Git
- GitHub
- XAMPP

## Installation Steps

### Prerequisites

- XAMPP
- PHP 8+
- MySQL
- Web Browser
- Git

### Setup Instructions

1. Clone the repository:

git clone [https://github.com/LamechSteph/OpenSource_Assignment_SE_Group4.git]

2. Copy project folder into XAMPP htdocs directory.

3. Start Apache and MySQL services.

4. Open phpMyAdmin.

5. Create a database named:

fypts

6. Import the SQL file:

database/fypts.sql

7. Configure database connection in:

config/db.php

8. Open browser and visit:

http://localhost/FYPTS

## Git Commands Used

Initialize Repository:

git init

Add Files:

git add .

Commit Changes:

git commit -m "Initial project setup - Final Year Project Tracking System"

Create Branch:

git checkout -b development

Merge Branch:

git merge development

Push Repository:

git push origin main

## GitHub Repository Link

Repository URL:

[https://github.com/LamechSteph/OpenSource_Assignment_SE_Group4.git]

## Source Code Summary

The application is divided into the following modules:

1. Authentication Module
2. User Management Module
3. Project Management Module
4. Project Tracking Module
5. Search Module
6. Dashboard Module

## Challenges Encountered

- Database connectivity configuration
- User authentication implementation
- Session management
- Search optimization
- Git branch management

## Conclusion

The Final Year Project Tracking System successfully provides a centralized platform for managing and monitoring final year student projects. The system improves project visibility, simplifies project tracking, and demonstrates the practical use of PHP, MySQL, Git, and GitHub in software development.
