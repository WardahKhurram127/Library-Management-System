<?php
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] == 'block') {
        mysqli_query($conn, "UPDATE users SET status = 'blocked' WHERE user_id = $id");
    } elseif ($_GET['action'] == 'unblock') {
        mysqli_query($conn, "UPDATE users SET status = 'active' WHERE user_id = $id");
    }
}

$students = mysqli_query($conn, "SELECT * FROM users WHERE role = 'student'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Members</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f8f9fa;
            margin: 20px;
            color: #333;
        }
        h2 {
            color: #444;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background-color: #343a40;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f1f1f1;
        }
        a.button {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            color: #fff;
        }
        a.block {
            background-color: #dc3545;
        }
        a.unblock {
            background-color: #28a745;
        }
        a.button:hover {
            opacity: 0.9;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>👥 Student Members</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($stu = mysqli_fetch_assoc($students)) { ?>
        <tr>
            <td><?= $stu['user_id'] ?></td>
            <td><?= $stu['username'] ?></td>
            <td><?= $stu['full_name'] ?></td>
            <td><?= $stu['email'] ?></td>
            <td><?= ucfirst($stu['status']) ?></td>
            <td>
                <?php if ($stu['status'] == 'active') { ?>
                    <a class="button block" href="?action=block&id=<?= $stu['user_id'] ?>" onclick="return confirm('Block this user?')">Block</a>
                <?php } else { ?>
                    <a class="button unblock" href="?action=unblock&id=<?= $stu['user_id'] ?>" onclick="return confirm('Unblock this user?')">Unblock</a>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

<a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>

</body>
</html>
