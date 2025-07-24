<?php 
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];
    $user_id = $_POST['user_id'];
    $issue_date = $_POST['issue_date'];
    $return_date = $_POST['return_date'];

    $check = mysqli_query($conn, "SELECT available_copies FROM books WHERE book_id = $book_id");
    $row = mysqli_fetch_assoc($check);

    if ($row && $row['available_copies'] > 0) {
        $sql = "INSERT INTO issued_books (book_id, user_id, issue_date, return_date) 
                VALUES ($book_id, $user_id, '$issue_date', '$return_date')";
        if (mysqli_query($conn, $sql)) {
            mysqli_query($conn, "UPDATE books SET available_copies = available_copies - 1 WHERE book_id = $book_id");
            $message = "✅ Book issued successfully!";
        } else {
            $message = "❌ Error issuing book: " . mysqli_error($conn);
        }
    } else {
        $message = "⚠️ No copies available for this book.";
    }
}

$students = mysqli_query($conn, "SELECT * FROM users WHERE role = 'student' AND status = 'active'");
$books = mysqli_query($conn, "SELECT * FROM books WHERE available_copies > 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Book</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 550px;
            margin: 60px auto;
            background: #fff;
            padding: 35px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        select, input[type="date"], input[type="submit"] {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            font-size: 15px;
        }

        input[type="submit"] {
            background-color: #2980b9;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #1f6393;
        }

        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .message.success {
            background-color: #e9f9ee;
            color: #27ae60;
        }

        .message.error {
            background-color: #fdecea;
            color: #c0392b;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        option {
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📚 Issue Book</h2>

        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="user_id">Select Student:</label>
            <select name="user_id" id="user_id" required>
                <option value="">-- Choose Student --</option>
                <?php while ($stu = mysqli_fetch_assoc($students)) { ?>
                    <option value="<?= $stu['user_id'] ?>"><?= $stu['full_name'] ?> (<?= $stu['username'] ?>)</option>
                <?php } ?>
            </select>

            <label for="book_id">Select Book:</label>
            <select name="book_id" id="book_id" required>
                <option value="">-- Choose Book --</option>
                <?php while ($book = mysqli_fetch_assoc($books)) { ?>
                    <option value="<?= $book['book_id'] ?>">
                        <?= $book['title'] ?> by <?= $book['author'] ?> (<?= $book['available_copies'] ?> left)
                    </option>
                <?php } ?>
            </select>

            <label for="issue_date">Issue Date:</label>
            <input type="date" name="issue_date" id="issue_date" required>

            <label for="return_date">Return Date:</label>
            <input type="date" name="return_date" id="return_date" required>

            <input type="submit" value="Issue Book">
        </form>

        <a href="admin_dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
