# 📚 Library Management System

A web-based Library Management System built with **PHP** and **MySQL** that allows efficient management of users, books, issues/returns, and fines. It features role-based access for admins and students, making it suitable for schools, colleges, and small libraries.

---

## 🔑 Features

- 🧑‍💼 **Admin Panel**
  - Add new users (admin or student)
  - Add, edit, delete books
  - Issue and return books
  - Track fine collection and pending returns
  - View reports and summaries

- 🎓 **Student Panel**
  - View available books
  - See issued books and due dates
  - Track fines and return history

- 🧮 **Fine Management**
  - Automatically calculate fines on late returns
  - Summary of collected and pending fines

- 🔒 **Authentication**
  - Role-based login (admin/student)
  - Session-based access control

---

## 🛠️ Tech Stack

- **Frontend**: HTML, CSS
- **Backend**: PHP
- **Database**: MySQL

---

## ⚙️ Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/library-management-system.git
   cd library-management-system
````

2. **Import the database**

   * Open `phpMyAdmin` (or any MySQL tool)
   * Create a new database (e.g., `library_db`)
   * Import the provided `database.sql` file into it

3. **Configure the database connection**

   * Open `db.php`
   * Update the host, username, password, and database name as per your setup:

     ```php
     $conn = mysqli_connect("localhost", "root", "", "library_db");
     ```

4. **Run the system**

   * Place the project in your `htdocs` or server directory
   * Open `http://localhost/library-management-system/login.php`

---

## 🔐 Default Admin Login

```
Username: admin
Password: admin123
```

(Or use your own credentials as created via the user form)

---

## 📸 Screenshots

