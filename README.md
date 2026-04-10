# Resume Checker 📄✔️

A web-based application built with PHP that allows users to upload their resumes and get an automated analysis. The system processes the uploaded resumes, likely parsing the content to check against specific criteria, and stores relevant data in a database.

## 🚀 Features

* **Resume Upload:** Simple interface to upload resume files (`upload.php`).
* **Resume Analysis:** Automated checking and processing of resume data (`analyze.php`).
* **Database Integration:** Stores and manages resume data or feedback (`db.php`, `database.sql`).
* **Dependency Management:** Integrated with Composer for handling third-party PHP packages (`composer.json`).

## 🛠️ Tech Stack

* **Backend:** PHP
* **Frontend:** HTML, CSS (24.8%), JavaScript (11.2%)
* **Database:** MySQL
* **Package Manager:** Composer

## 📂 Project Structure

```text
├── assets/          # Contains CSS, JavaScript, and image files for the frontend UI
├── uploads/         # Directory where uploaded resume files are temporarily/permanently stored
├── vendor/          # Composer dependencies
├── analyze.php      # Script handling the core logic for analyzing the uploaded resumes
├── composer.json    # Defines PHP dependencies
├── database.sql     # SQL dump file to create the necessary database schema and tables
├── db.php           # Database connection configuration script
├── index.php        # The main landing page/UI of the application
└── upload.php       # Script handling the file upload mechanism
