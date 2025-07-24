<?php
include 'auth.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: student_dashboard.php");
    exit;
}
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
    $category_id = (int) $_POST['category'];
    $copies = (int) $_POST['copies'];

    $query = "INSERT INTO books (title, author, isbn, category_id, total_copies, available_copies)
              VALUES ('$title', '$author', '$isbn', $category_id, $copies, $copies)";
    
    if (mysqli_query($conn, $query)) {
        $message = "✅ Book added successfully!";
    } else {
        $message = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Book</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 400px;
            margin: 60px auto;
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #2e86de;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #1b4f72;
        }
        .message {
            text-align: center;
            font-weight: bold;
            color: green;
            margin-bottom: 20px;
        }
        .error {
            color: red;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #2e86de;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Book</h2>

        <?php if ($message): ?>
            <p class="message <?php echo (strpos($message, 'Error') !== false) ? 'error' : ''; ?>">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Title:</label>
            <input type="text" name="title" required>

            <label>Author:</label>
            <input type="text" name="author" required>

            <label>ISBN:</label>
            <input type="text" name="isbn" required>

            
             <label>Category:</label>
<select name="category" required>
    <option value="">-- Select Category --</option>
    <?php
    $catQuery = mysqli_query($conn, "SELECT category_id, category_name FROM categories");
    while ($cat = mysqli_fetch_assoc($catQuery)) {
        echo "<option value='{$cat['category_id']}'>{$cat['category_name']}</option>";
    }
    ?>
</select>

            <label>Total Copies:</label>
            <input type="number" name="copies" min="1" required>

            <input type="submit" value="Add Book">
        </form>

        <a href="admin_dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
