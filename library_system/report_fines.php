<?php
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$total_fine_res = mysqli_query($conn, "SELECT SUM(fine) as total FROM issued_books WHERE fine > 0");
$total_fine = mysqli_fetch_assoc($total_fine_res)['total'] ?? 0;

$fine_details = mysqli_query($conn, "
    SELECT u.full_name, u.email, b.title, i.fine, i.actual_return_date
    FROM issued_books i
    JOIN users u ON i.user_id = u.user_id
    JOIN books b ON i.book_id = b.book_id
    WHERE i.fine > 0
    ORDER BY i.fine DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fine Summary</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #eef3f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 950px;
            margin: 50px auto;
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        h2 {
            font-size: 30px;
            color: #1f4e78;
            margin-bottom: 30px;
            text-align: center;
        }

        .fine-box {
            background-color: #e6f3ff;
            padding: 18px 25px;
            border-radius: 10px;
            text-align: center;
            font-size: 26px;
            font-weight: 600;
            color: #2164a8;
            margin-bottom: 35px;
            border: 2px solid #cce1f3;
        }

        .fine-box .icon {
            font-size: 28px;
            vertical-align: middle;
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 14px 16px;
            border: 1px solid #dbe7f2;
            text-align: left;
            font-size: 15px;
        }

        th {
            background-color: #1f4e78;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f7fafd;
        }

        tr:hover {
            background-color: #eef7ff;
        }

        .back-link {
            display: inline-block;
            margin-top: 40px;
            background-color: #1f4e78;
            color: #fff;
            padding: 12px 22px;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .back-link:hover {
            background-color: #15426a;
        }

        .no-fines {
            text-align: center;
            color: #777;
            font-size: 17px;
            margin-top: 30px;
        }

        h3 {
            margin-top: 20px;
            color: #1f4e78;
            font-size: 22px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>💰 Fine Collection Summary</h2>

        <div class="fine-box">
            <span class="icon">₹</span><?= number_format($total_fine, 2) ?>
        </div>

        <?php if (mysqli_num_rows($fine_details) > 0): ?>
            <h3>Students with Fines</h3>
            <table>
                <tr>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Book Title</th>
                    <th>Returned On</th>
                    <th>Fine (₹)</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($fine_details)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= $row['actual_return_date'] ?: '<em>Not returned</em>' ?></td>
                        <td><?= number_format($row['fine'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <div class="no-fines">🎉 No fines recorded. Great job!</div>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
