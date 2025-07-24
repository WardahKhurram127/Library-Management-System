<?php
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_id = $_POST['issue_id'];
    $actual_return = $_POST['actual_return_date'];

    $res = mysqli_query($conn, "SELECT * FROM issued_books WHERE issue_id = $issue_id");
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $expected_return = $row['return_date'];
        $book_id = $row['book_id'];

        $days_late = (strtotime($actual_return) - strtotime($expected_return)) / (60*60*24);
        $fine = ($days_late > 0) ? $days_late * 100 : 0;

        mysqli_query($conn, "
            UPDATE issued_books 
            SET actual_return_date = '$actual_return', fine = $fine 
            WHERE issue_id = $issue_id
        ");

        mysqli_query($conn, "UPDATE books SET available_copies = available_copies + 1 WHERE book_id = $book_id");

        $message = "✅ Book returned successfully. Fine: ₹$fine";
    } else {
        $message = "❌ Invalid issue record.";
    }
}

$issued = mysqli_query($conn, "
    SELECT i.issue_id, b.title, u.full_name, i.issue_date, i.return_date
    FROM issued_books i
    JOIN books b ON i.book_id = b.book_id
    JOIN users u ON i.user_id = u.user_id
    WHERE i.actual_return_date IS NULL
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Book</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f6fb;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            background: #fff;
            margin: 60px auto;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 500;
        }

        select, input[type="date"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #2c80b4;
        }

        .message {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .back-link {
            display: block;
            margin-top: 25px;
            text-align: center;
            text-decoration: none;
            color: #2980b9;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #1c5980;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📚 Return Book</h2>

        <?php if (!empty($message)): ?>
            <div class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="issue_id">Select Issued Book:</label>
            <select name="issue_id" id="issue_id" required>
                <option value="">-- Choose Issued Book --</option>
                <?php while ($row = mysqli_fetch_assoc($issued)) { ?>
                    <option value="<?= $row['issue_id'] ?>">
                        <?= $row['title'] ?> → <?= $row['full_name'] ?> (Due: <?= $row['return_date'] ?>)
                    </option>
                <?php } ?>
            </select>

            <label for="actual_return_date">Actual Return Date:</label>
            <input type="date" name="actual_return_date" id="actual_return_date" required>

            <input type="submit" value="Return Book">
        </form>

        <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
