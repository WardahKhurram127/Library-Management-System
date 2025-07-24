<?php 
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: admin_dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

if (isset($_POST['request_issue'])) {
    $book_id = $_POST['book_id'];
    $check = mysqli_query($conn, "SELECT * FROM book_requests WHERE book_id=$book_id AND user_id=$user_id AND status='pending'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO book_requests (book_id, user_id, request_type) VALUES ($book_id, $user_id, 'issue')");
        $message = "✅ Issue request sent!";
    } else {
        $message = "⚠️ You already requested this book.";
    }
}

if (isset($_POST['request_return'])) {
    $book_id = $_POST['book_id'];
    $check = mysqli_query($conn, "SELECT * FROM book_requests WHERE book_id=$book_id AND user_id=$user_id AND status='pending' AND request_type='return'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO book_requests (book_id, user_id, request_type) VALUES ($book_id, $user_id, 'return')");
        $message = "✅ Return request sent!";
    } else {
        $message = "⚠️ You already requested to return this book.";
    }
}

$available_books = mysqli_query($conn, "SELECT * FROM books WHERE available_copies > 0");

$my_books = mysqli_query($conn, "
    SELECT b.title, b.book_id, i.issue_date, i.return_date
    FROM issued_books i
    JOIN books b ON i.book_id = b.book_id
    WHERE i.user_id = $user_id AND i.actual_return_date IS NULL
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f5f7fa;
        }

        header {
            background-color: #2e86de;
            color: #fff;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h2 {
            margin: 0;
            font-size: 24px;
        }

        header a {
            color: #fefefe;
            text-decoration: none;
            font-weight: bold;
            background-color: #1b4f72;
            padding: 8px 14px;
            border-radius: 5px;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .message {
            background-color: #dff9fb;
            color: #0984e3;
            border: 1px solid #7ed6df;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 20px;
        }

        select, button {
            padding: 10px;
            border-radius: 5px;
            margin-right: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #27ae60;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #219150;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #dcdde1;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #2e86de;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f1f2f6;
        }

        hr {
            border: none;
            border-top: 2px solid #dfe6e9;
            margin: 40px 0 30px;
        }
    </style>
</head>
<body>

<header>
    <h2>🎓 Welcome, <?= $_SESSION['username'] ?></h2>
    <a href="logout.php">Logout</a>
</header>

<div class="container">

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <h3>📚 Request a Book</h3>
    <form method="POST">
        <select name="book_id" required>
            <option value="">-- Select a book --</option>
            <?php while ($book = mysqli_fetch_assoc($available_books)) { ?>
                <option value="<?= $book['book_id'] ?>">
                    <?= $book['title'] ?> by <?= $book['author'] ?> (<?= $book['available_copies'] ?> available)
                </option>
            <?php } ?>
        </select>
        <button type="submit" name="request_issue">Request Issue</button>
    </form>

    <hr>

    <h3>📖 My Issued Books</h3>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Issued On</th>
                <th>Due Date</th>
                <th>Return</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($my_books)) { ?>
            <tr>
                <td><?= $row['title'] ?></td>
                <td><?= $row['issue_date'] ?></td>
                <td><?= $row['return_date'] ?></td>
                <td>
                    <form method="POST" style="margin: 0;">
                        <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
                        <button type="submit" name="request_return">Request Return</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>

</body>
</html>
