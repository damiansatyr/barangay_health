<?php
session_start();

// Redirect to login if session is not set
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';  // Include the database connection

// Check if ID is passed for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
} else {
    // If ID is not set, redirect to inventory page
    header("Location: inventory.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and update inventory
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];

    $update_query = "UPDATE inventory SET item_name = ?, quantity = ?, expiry_date = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sisi", $item_name, $quantity, $expiry_date, $id);
    $update_stmt->execute();

    header("Location: inventory.php");  // Redirect back to inventory page after update
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventory</title>
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

        /* Cancel Button Styling */
        .cancel-btn {
            background-color: #dc3545; /* Bootstrap's danger red */
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            font-size: 1rem;
            animation: fadeIn 1s ease-in-out;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .cancel-btn:hover {
            transform: translateY(-5px);
            background-color: #c82333; /* Darker shade of red */
            cursor: pointer;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            animation: fadeIn 1.5s ease-in-out;
        }

        /* Mobile responsiveness */
        @media (max-width: 767px) {
            .form-container {
                padding: 0 15px;
            }

            .cancel-btn {
                width: 100%;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container mt-5 form-container">
    <h2 class="mb-4">Edit Inventory Item</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="item_name" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="item_name" name="item_name" value="<?php echo $item['item_name']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $item['quantity']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="expiry_date" class="form-label">Expiry Date</label>
            <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?php echo $item['expiry_date']; ?>" required>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="inventory.php" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
