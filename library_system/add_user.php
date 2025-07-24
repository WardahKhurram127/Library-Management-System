<?php
include 'auth.php';
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";
$username = $full_name = $email = $password = $role = "";
$edit_mode = false;

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE user_id=$delete_id");
    $message = "🗑️ User deleted successfully!";
}

// Handle load for editing
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE user_id=$edit_id");
    if ($res && mysqli_num_rows($res) === 1) {
        $edit_mode = true;
        $data = mysqli_fetch_assoc($res);
        $username = $data['username'];
        $full_name = $data['full_name'];
        $email = $data['email'];
        $role = $data['role'];
    }
}

// Handle form submission (Add or Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'];
    $user_id = $_POST['user_id'] ?? null;

    if ($user_id) {
        // Update
        if ($password) {
            mysqli_query($conn, "UPDATE users SET username='$username', full_name='$full_name', email='$email', password='$password', role='$role' WHERE user_id=$user_id");
        } else {
            mysqli_query($conn, "UPDATE users SET username='$username', full_name='$full_name', email='$email', role='$role' WHERE user_id=$user_id");
        }
        $message = "✏️ User updated successfully!";
        $edit_mode = false;
    } else {
        // Add new
        $exists = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($exists) > 0) {
            $message = "⚠️ Username already exists.";
        } else {
            mysqli_query($conn, "INSERT INTO users (username, full_name, email, password, role, status)
                                VALUES ('$username', '$full_name', '$email', '$password', '$role', 'active')");
            $message = "✅ User added successfully!";
        }
    }

    // Reset form values
    $username = $full_name = $email = $password = $role = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $edit_mode ? 'Edit User' : 'Add New User' ?></title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f1f4f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            background: #fff;
            margin: 50px auto;
            padding: 40px 35px;
            border-radius: 10px;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            color: #27ae60;
        }

        .message.error {
            color: #e74c3c;
        }

        form label {
            font-weight: bold;
            color: #555;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form select {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        form input[type="submit"] {
            width: 100%;
            background-color: #3498db;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #2980b9;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #3498db;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px 12px;
            text-align: left;
        }

        th {
            background-color: #ecf0f1;
        }

        .actions a {
            margin-right: 8px;
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        .actions a.delete {
            color: #e74c3c;
        }

        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2><?= $edit_mode ? 'Edit User' : 'Add New User' ?></h2>

    <?php if ($message): ?>
        <div class="message <?= strpos($message, 'exists') !== false ? 'error' : '' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="user_id" value="<?= $_GET['edit'] ?? '' ?>">

        <label>Username:</label>
        <input type="text" name="username" value="<?= $username ?>" required>

        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?= $full_name ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= $email ?>" required>

        <label>Password: <?= $edit_mode ? '(Leave blank to keep current)' : '' ?></label>
        <input type="password" name="password" <?= $edit_mode ? '' : 'required' ?>>

        <label>Role:</label>
        <select name="role" required>
            <option value="student" <?= $role == 'student' ? 'selected' : '' ?>>Student</option>
            <option value="admin" <?= $role == 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <input type="submit" value="<?= $edit_mode ? 'Update User' : 'Add User' ?>">
    </form>

    <table>
        <tr>
            <th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Actions</th>
        </tr>
        <?php
        $users = mysqli_query($conn, "SELECT * FROM users ORDER BY user_id DESC");
        while ($u = mysqli_fetch_assoc($users)) {
            echo "<tr>
                <td>{$u['username']}</td>
                <td>{$u['full_name']}</td>
                <td>{$u['email']}</td>
                <td>{$u['role']}</td>
                <td class='actions'>
                    <a href='add_user.php?edit={$u['user_id']}'>✏️ Edit</a>
                    <a href='add_user.php?delete={$u['user_id']}' class='delete' onclick='return confirm(\"Are you sure?\")'>🗑️ Delete</a>
                </td>
            </tr>";
        }
        ?>
    </table>

    <a href="admin_dashboard.php">← Back to Dashboard</a>
</div>
</body>
</html>
