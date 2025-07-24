<?php
include 'auth.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: student_dashboard.php");
    exit;
}
include 'db.php';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    mysqli_query($conn, "DELETE FROM books WHERE book_id = $id");
}
if (isset($_GET['increment'])) {
    $id = (int) $_GET['increment'];
    mysqli_query($conn, "UPDATE books SET available_copies = available_copies + 1, total_copies = total_copies + 1 WHERE book_id = $id");
}

$query = "SELECT b.*, c.category_name 
          FROM books b
          LEFT JOIN categories c ON b.category_id = c.category_id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Books</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f9f9f9;
        }
        table {
            width: 90%;
            margin: 40px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2e86de;
            color: white;
        }
        a.btn {
            padding: 6px 12px;
            background-color: #e74c3c;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }
        a.btn:hover {
            background-color: #c0392b;
        }
        a.increment {
            background-color: #27ae60;
        }
        a.increment:hover {
            background-color: #1e8449;
        }
        h2 {
            text-align: center;
            margin-top: 30px;
        }
        .back {
            display: block;
            margin: 20px auto;
            width: 180px;
            padding: 10px;
            background-color: #555;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<h2>📚 Manage Books</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Author</th>
        <th>ISBN</th>
        <th>Category</th>
        <th>Status</th>
        <th>Available Copies</th>
        <th>Actions</th>
    </tr>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['book_id'] ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['author']) ?></td>
                <td><?= htmlspecialchars($row['isbn']) ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td><?= $row['available_copies'] > 0 ? 'Available' : 'Out of stock' ?></td>
                <td><?= $row['available_copies'] ?></td>
                <td>
                    <a href="?increment=<?= $row['book_id'] ?>" class="btn increment">➕</a>
                    <a href="?delete=<?= $row['book_id'] ?>" class="btn" onclick="return confirm('Delete this book?')">🗑️</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8" style="text-align:center;">No books found.</td></tr>
    <?php endif; ?>
</table>

<a href="admin_dashboard.php" class="back">← Back to Dashboard</a>

</body>
</html>
