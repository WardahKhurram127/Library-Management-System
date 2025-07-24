<?php
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$total = mysqli_query($conn, "SELECT COUNT(*) as total FROM books");
$available = mysqli_query($conn, "SELECT COUNT(*) as available FROM books WHERE status='available'");
$issued = mysqli_query($conn, "SELECT COUNT(*) as issued FROM books WHERE status='issued'");
$reserved = mysqli_query($conn, "SELECT COUNT(*) as reserved FROM books WHERE status='reserved'");

$t = mysqli_fetch_assoc($total)['total'];
$a = mysqli_fetch_assoc($available)['available'];
$i = mysqli_fetch_assoc($issued)['issued'];
$r = mysqli_fetch_assoc($reserved)['reserved'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Inventory Report</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f2f5f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 80px auto;
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            padding: 25px 15px;
            border-radius: 10px;
            background-color: #f1f9ff;
            border: 1px solid #dce6f1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-title {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2e86de;
        }

        .back-link {
            display: inline-block;
            margin-top: 35px;
            padding: 10px 18px;
            background-color: #2e86de;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .back-link:hover {
            background-color: #1b4f72;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📚 Book Inventory Report</h2>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total Books</div>
                <div class="stat-value"><?= $t ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Available</div>
                <div class="stat-value"><?= $a ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Issued</div>
                <div class="stat-value"><?= $i ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Reserved</div>
                <div class="stat-value"><?= $r ?></div>
            </div>
        </div>

        <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
