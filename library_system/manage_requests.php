<?php 
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    $req = mysqli_query($conn, "
        SELECT br.*, b.available_copies 
        FROM book_requests br 
        JOIN books b ON br.book_id = b.book_id 
        WHERE request_id = $request_id
    ");
    $data = mysqli_fetch_assoc($req);

    $book_id = $data['book_id'];
    $user_id = $data['user_id'];
    $type = $data['request_type'];
    $copies = $data['available_copies'];

    if ($action === 'approve') {
        if ($type === 'issue') {
            if ($copies > 0) {
                $today = date("Y-m-d");
                $return_date = date("Y-m-d", strtotime("+7 days"));

                mysqli_query($conn, "
                    INSERT INTO issued_books (book_id, user_id, issue_date, return_date)
                    VALUES ($book_id, $user_id, '$today', '$return_date')
                ");
                mysqli_query($conn, "UPDATE books SET available_copies = available_copies - 1 WHERE book_id = $book_id");
                mysqli_query($conn, "UPDATE book_requests SET status='approved' WHERE request_id = $request_id");

                $message = "✅ Book issued to user.";
            } else {
                $message = "❌ No copies left.";
            }
        } elseif ($type === 'return') {
            $issued = mysqli_query($conn, "
                SELECT * FROM issued_books 
                WHERE book_id = $book_id AND user_id = $user_id AND actual_return_date IS NULL
                LIMIT 1
            ");
            $row = mysqli_fetch_assoc($issued);

            if ($row) {
                $issue_id = $row['issue_id'];
                $expected_return = $row['return_date'];
                $actual_return = date("Y-m-d");

                $days_late = (strtotime($actual_return) - strtotime($expected_return)) / (60 * 60 * 24);
                $fine = ($days_late > 0) ? $days_late * 2 : 0;

                mysqli_query($conn, "
                    UPDATE issued_books 
                    SET actual_return_date = '$actual_return', fine = $fine 
                    WHERE issue_id = $issue_id
                ");

                mysqli_query($conn, "UPDATE books SET available_copies = available_copies + 1 WHERE book_id = $book_id");
                mysqli_query($conn, "UPDATE book_requests SET status='approved' WHERE request_id = $request_id");

                $message = "✅ Book returned successfully. Fine ₹$fine";
            } else {
                $message = "❌ No issued record found.";
            }
        }
    } elseif ($action === 'reject') {
        mysqli_query($conn, "UPDATE book_requests SET status='rejected' WHERE request_id = $request_id");
        $message = "❌ Request rejected.";
    }
}

$requests = mysqli_query($conn, "
    SELECT r.*, b.title, u.full_name 
    FROM book_requests r
    JOIN books b ON r.book_id = b.book_id
    JOIN users u ON r.user_id = u.user_id
    WHERE r.status = 'pending'
    ORDER BY r.request_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Book Requests</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #2e86de;
            margin-bottom: 20px;
        }

        a {
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            color: #2e86de;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            border-radius: 6px;
        }

        .message.success {
            color: green;
            background-color: #e8f5e9;
            border: 1px solid #a5d6a7;
        }

        .message.error {
            color: red;
            background-color: #ffebee;
            border: 1px solid #ef9a9a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #2e86de;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        form button {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button[name="action"][value="approve"] {
            background-color: #27ae60;
            color: white;
        }

        form button[name="action"][value="reject"] {
            background-color: #e74c3c;
            color: white;
        }

        form button:hover {
            opacity: 0.85;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📚 Manage Book Requests</h2>
    <a href="admin_dashboard.php">← Back to Dashboard</a>

    <?php if ($message): ?>
        <div class="message <?= (strpos($message, '❌') !== false) ? 'error' : 'success' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Book</th>
                <th>Type</th>
                <th>Requested On</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($requests)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= ucfirst($row['request_type']) ?></td>
                <td><?= $row['request_date'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">
                        <button type="submit" name="action" value="approve">✅ Approve</button>
                        <button type="submit" name="action" value="reject" onclick="return confirm('Reject this request?')">❌ Reject</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
