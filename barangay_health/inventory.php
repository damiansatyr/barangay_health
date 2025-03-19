<?php
session_start();

// Redirect to login if session is not set
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';  // Include the database connection

// Fetch inventory items from the database
$query = "SELECT * FROM inventory";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Animations */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes textSlideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #2A3D66; /* Updated color */
            color: white;
            position: fixed;
        }

        /* Logo Styling */
        .sidebar .logo {
            text-align: center;
            padding: 20px;
        }

        .sidebar .logo img {
            width: 80%;
            height: auto;
        }

        .sidebar .nav-item {
            margin: 10px 0;
        }

        .sidebar .nav-link {
            color: white;
            padding: 10px;
            text-decoration: none;
        }

        .sidebar .nav-link.active {
            background-color: #007bff;
        }

        .sidebar .back-to-dashboard-btn {
            background-color: #f9fcbc;
            color: #333;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            font-size: 1rem;
            text-decoration: none;  /* Remove underline */
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .sidebar .back-to-dashboard-btn:hover {
            transform: translateY(-5px);
            background-color: #f0e06b; /* Slightly darker shade for hover */
            cursor: pointer;
        }

        /* Content Styling */
        .content {
            margin-left: 260px;
            padding: 20px;
            animation: fadeIn 1.5s ease-in-out;
        }

        /* Mobile responsiveness */
        @media (max-width: 767px) {
            .container {
                padding: 0 15px;
            }

            .content {
                margin-left: 0;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .sidebar .nav-link {
                text-align: center;
            }

            .sidebar .logo {
                padding: 10px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <!-- Logo Image -->
        <img src="images/logo.jpg" alt="Logo" class="logo">
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="inventory.php">
                <i class="fas fa-box"></i> Inventory
            </a>
        </li>
        <!-- Back to Dashboard Link -->
        <li class="nav-item">
            <a class="nav-link back-to-dashboard-btn" href="dashboard.php">
                Back to Dashboard
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="content">
    <h2>Inventory</h2>

    <p><a href="add_inventory.php" class="btn btn-primary">Add New Item</a></p>

    <!-- Inventory Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Expiry Date</th>
                <th>Added At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['item_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['expiry_date']; ?></td>
                    <td><?php echo $row['added_at']; ?></td>
                    <td>
                        <a href="edit_inventory.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_inventory.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
