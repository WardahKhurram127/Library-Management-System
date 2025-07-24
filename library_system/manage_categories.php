<?php
include 'auth.php';
include 'db.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_name'])) {
    $cat = $_POST['category_name'];
    mysqli_query($conn, "INSERT INTO categories (category_name) VALUES ('$cat')");
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE category_id = $id");
}

$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<h2>Manage Categories</h2>

<form method="post">
    <input type="text" name="category_name" placeholder="New Category Name" required>
    <input type="submit" value="Add Category">
</form>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th><th>Category Name</th><th>Action</th>
    </tr>
    <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
        <tr>
            <td><?= $cat['category_id'] ?></td>
            <td><?= $cat['category_name'] ?></td>
            <td><a href="?delete=<?= $cat['category_id'] ?>" onclick="return confirm('Delete this category?')">Delete</a></td>
        </tr>
    <?php } ?>
</table>

<a href="admin_dashboard.php">Back to Dashboard</a>
