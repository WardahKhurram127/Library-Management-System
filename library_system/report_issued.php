<?php
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$result = mysqli_query($conn, "
    SELECT u.full_name, b.title, i.issue_date, i.return_date, i.actual_return_date, i.fine
    FROM issued_books i
    JOIN users u ON i.user_id = u.user_id
    JOIN books b ON i.book_id = b.book_id
    ORDER BY i.issue_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issued / Returned Books Report</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f8fc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 60px auto;
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }

        th, td {
            padding: 14px 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:hover {
            background-color: #f0f8ff;
        }

        .not-returned {
            color: #c0392b;
            font-weight: bold;
        }

        .fine {
            color: #d35400;
            font-weight: 500;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            background-color: #2e86de;
            color: #fff;
            padding: 10px 18px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .back-link:hover {
            background-color: #1b4f72;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                background-color: #2e86de;
                text-align: left;
            }

            td {
                text-align: left;
                padding-left: 50%;
                position: relative;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 15px;
                font-weight: bold;
                color: #555;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📖 Issued / Returned Books Report</h2>

        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Book</th>
                    <th>Issued</th>
                    <th>Due</th>
                    <th>Returned</th>
                    <th>Fine</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td data-label="Student"><?= $row['full_name'] ?></td>
                        <td data-label="Book"><?= $row['title'] ?></td>
                        <td data-label="Issued"><?= $row['issue_date'] ?></td>
                        <td data-label="Due"><?= $row['return_date'] ?></td>
                        <td data-label="Returned">
                            <?= $row['actual_return_date'] ? $row['actual_return_date'] : '<span class="not-returned">Not returned</span>' ?>
                        </td>
                        <td data-label="Fine">
                            <?= $row['fine'] ? '<span class="fine">₹' . $row['fine'] . '</span>' : '-' ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div style="text-align: center;">
            <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
