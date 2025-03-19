<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Health Personnel President') {
    header("Location: dashboard.php");
    exit();
}
include 'db_connect.php';

// Delete user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit();
}

// Update role
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_role'])) {
    $id = $_POST['user_id'];
    $new_role = $_POST['role'];
    $conn->query("UPDATE users SET role='$new_role' WHERE id=$id");
    header("Location: users.php");
    exit();
}

// Fetch users
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>User Management</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['fullname'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>
                        <form method="POST" style="display: flex;">
                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                            <select name="role" class="form-select">
                                <option value="Health Personnel President" <?= $row['role'] == 'Health Personnel President' ? 'selected' : '' ?>>Health Personnel President</option>
                                <option value="Barangay Health Personnel" <?= $row['role'] == 'Barangay Health Personnel' ? 'selected' : '' ?>>Barangay Health Personnel</option>
                            </select>
                            <button type="submit" name="update_role" class="btn btn-sm btn-primary ms-2">Update</button>
                        </form>
                    </td>
                    <td>
                        <a href="users.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</body>
</html>
