<?php
session_start();

// Redirect to login if session is not set
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';  // Include the database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];

    // Prepare and execute the SQL query to insert data
    $query = "INSERT INTO inventory (item_name, quantity, expiry_date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sis", $item_name, $quantity, $expiry_date);  // 's' for string, 'i' for integer
    $stmt->execute();

    // Redirect back to the inventory page after successful addition
    header("Location: inventory.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Inventory Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Add your custom styles here */
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            animation: fadeIn 1.5s ease-in-out;
        }

        .cancel-btn {
            background-color: #dc3545; /* Bootstrap's danger red */
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            font-size: 1rem;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .cancel-btn:hover {
            transform: translateY(-5px);
            background-color: #c82333; /* Darker shade of red */
            cursor: pointer;
        }

        /* Animations */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</head>
<body>

<div class="container mt-5 form-container">
    <h2 class="mb-4">Add New Inventory Item</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="item_name" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="item_name" name="item_name" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
        <div class="mb-3">
            <label for="expiry_date" class="form-label">Expiry Date</label>
            <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Add Item</button>
            <a href="inventory.php" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
