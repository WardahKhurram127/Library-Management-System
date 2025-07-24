<?php
include 'auth.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: student_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #eef2f3, #8e9eab);
            color: #2c3e50;
        }

        header {
            background-color: #34495e;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h2 {
            margin: 0;
            font-size: 24px;
        }

        header a {
            color: #ecf0f1;
            text-decoration: none;
            font-weight: 500;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .container h3 {
            font-size: 26px;
            margin-bottom: 30px;
            text-align: center;
            color: #2c3e50;
        }

        .menu {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
        }

        .menu li {
            background-color: #f5f7fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #dfe6e9;
        }

        .menu li:hover {
            background-color: #dff9fb;
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .menu a {
            text-decoration: none;
            color: #2d3436;
            font-size: 16px;
            font-weight: 600;
        }

        .menu a:hover {
            color: #0984e3;
        }
    </style>
</head>
<body>
    <header>
        <h2>👨‍💼 Welcome, <?= htmlspecialchars($_SESSION['username']); ?></h2>
        <a href="logout.php">Logout</a>
    </header>

    <div class="container">
        <h3>📚 Library Management Dashboard</h3>
        <ul class="menu">
            <li><a href="add_user.php">🧑 Add New User</a></li>
            <li><a href="add_book.php">➕ Add New Book</a></li>
            <li><a href="manage_books.php">📚 Manage Books</a></li>
            <li><a href="issue_book.php">📤 Issue Book</a></li>
            <li><a href="return_book.php">📥 Return Book</a></li>
            <li><a href="report_issued.php">📑 Issued Books Report</a></li>
            <li><a href="report_fines.php">💰 Fine Report</a></li>
            <li><a href="report_inventory.php">📦 Inventory Report</a></li>
            <li><a href="manage_requests.php">📬 Manage Book Requests</a></li>
            <li><a href="view_students.php">🧑 Manage Students</a></li>
        </ul>
    </div>
</body>
</html>
