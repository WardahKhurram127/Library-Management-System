<?php
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "
    SELECT b.title, b.author, i.issue_date, i.return_date, i.actual_return_date, i.fine
    FROM issued_books i
    JOIN books b ON i.book_id = b.book_id
    WHERE i.user_id = $user_id
    ORDER BY i.issue_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Issued Books</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        table th, table td {
            padding: 14px 12px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #2e86de;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #eaf2fb;
        }

        .fine {
            color: red;
            font-weight: bold;
        }

        .not-returned {
            color: #ff6b6b;
            font-style: italic;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            text-decoration: none;
            color: #2e86de;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📚 My Issued Books</h2>

    <table>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Issued On</th>
            <th>Due Date</th>
            <th>Returned On</th>
            <th>Fine</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['author']) ?></td>
                <td><?= $row['issue_date'] ?></td>
                <td><?= $row['return_date'] ?></td>
                <td>
                    <?= $row['actual_return_date'] ? $row['actual_return_date'] : '<span class="not-returned">Not returned yet</span>' ?>
                </td>
                <td>
                    <?= ($row['fine'] && $row['fine'] > 0) ? "<span class='fine'>₹{$row['fine']}</span>" : '-' ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <a href="student_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>
</body>
</html>
