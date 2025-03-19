<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// Add Patient
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_patient'])) {
    $fullname = $_POST['fullname']; 
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];

    $sql = "INSERT INTO patients (fullname, age, gender, contact) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $fullname, $age, $gender, $contact);
    $stmt->execute();
    $stmt->close();
    header("Location: patients.php");
    exit();
}

// Delete Patient
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM patients WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: patients.php");
    exit();
}

// Fetch Patients
$patients = $conn->query("SELECT * FROM patients");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
            animation: fadeIn 0.5s ease-out; /* Faster fade-in */
        }

        .container {
            margin-top: 50px;
        }

        .table {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.5s ease-out; /* Faster slide-up */
        }

        .btn {
            transition: background-color 0.3s, transform 0.3s;
            border-radius: 25px;
        }

        /* Custom Blue Button for Add Patient */
        .btn-primary {
            background-color: #007bff; /* Blue */
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker Blue */
            transform: scale(1.05);
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .btn-secondary {
            background-color: #F0E999; /* Light Yellow */
            border: none;
        }

        .btn-secondary:hover {
            background-color: #E0D26B;
            transform: scale(1.05);
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }

        .form-control, .btn {
            border-radius: 8px;
        }

        .form-label {
            font-weight: bold;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        .form-control {
            margin-bottom: 15px;
        }

        /* Responsive adjustments */
        @media (max-width: 767px) {
            .container {
                margin-top: 20px;
            }
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

        /* Custom Yellow Button for Back to Dashboard */
        .btn-back-dashboard {
            background-color: #F0E999; /* Light Yellow */
            border: none;
        }

        .btn-back-dashboard:hover {
            background-color: #E0D26B; /* Darker Yellow */
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4" style="animation: fadeIn 0.5s ease-out;">Patient Records</h2>
        
        <!-- Add Patient Form -->
        <form method="POST" class="mb-4" style="animation: fadeIn 0.5s ease-out;">
            <div class="mb-3">
                <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="mb-3">
                <input type="number" name="age" class="form-control" placeholder="Age" required>
            </div>
            <div class="mb-3">
                <select name="gender" class="form-control" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-3">
                <input type="text" name="contact" class="form-control" placeholder="Contact" required>
            </div>
            <button type="submit" name="add_patient" class="btn btn-primary w-100">Add Patient</button>
        </form>

        <!-- Patient List -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" style="animation: slideUp 0.5s ease-out;">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $patients->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['fullname'] ?></td>
                        <td><?= $row['age'] ?></td>
                        <td><?= $row['gender'] ?></td>
                        <td><?= $row['contact'] ?></td>
                        <td>
                            <a href="patients.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Back to Dashboard Button -->
        <a href="dashboard.php" class="btn btn-back-dashboard w-100">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
