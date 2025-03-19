<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// Count total appointments
$totalAppointments = $conn->query("SELECT COUNT(*) AS total FROM appointments")->fetch_assoc()['total'];

// Count approved, pending, and rejected appointments
$approvedAppointments = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE status='Approved'")->fetch_assoc()['total'];
$pendingAppointments = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE status='Pending'")->fetch_assoc()['total'];
$rejectedAppointments = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE status='Rejected'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Statistics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
            animation: fadeIn 0.5s ease-out;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.5s ease-out;
        }

        .btn {
            transition: background-color 0.3s, transform 0.3s;
            border-radius: 25px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
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
            background-color: #F0E999;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #E0D26B;
            transform: scale(1.05);
        }

        .card-body {
            padding: 20px;
            text-align: center;
        }

        .card-title {
            font-weight: bold;
            font-size: 18px;
        }

        .card-text {
            font-size: 30px;
        }

        .row {
            margin-bottom: 30px;
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

        .btn-back-dashboard {
            background-color: #F0E999; /* Light Yellow */
            border: none;
        }

        .btn-back-dashboard:hover {
            background-color: #E0D26B;
            transform: scale(1.05);
        }

        .bg-primary-custom {
            background-color: #4e73df;
            color: white;
        }

        .bg-success-custom {
            background-color: #28a745;
            color: white;
        }

        .bg-warning-custom {
            background-color: #f0ad4e;
            color: white;
        }

        .bg-danger-custom {
            background-color: #e74a3b;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4" style="animation: fadeIn 0.5s ease-out;">Reports & Statistics</h2>

        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary-custom mb-3" style="animation: slideUp 0.5s ease-out;">
                    <div class="card-body">
                        <h5 class="card-title">Total Appointments</h5>
                        <p class="card-text"><?= $totalAppointments ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success-custom mb-3" style="animation: slideUp 0.5s ease-out;">
                    <div class="card-body">
                        <h5 class="card-title">Approved Appointments</h5>
                        <p class="card-text"><?= $approvedAppointments ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning-custom mb-3" style="animation: slideUp 0.5s ease-out;">
                    <div class="card-body">
                        <h5 class="card-title">Pending Appointments</h5>
                        <p class="card-text"><?= $pendingAppointments ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger-custom mb-3" style="animation: slideUp 0.5s ease-out;">
                    <div class="card-body">
                        <h5 class="card-title">Rejected Appointments</h5>
                        <p class="card-text"><?= $rejectedAppointments ?></p>
                    </div>
                </div>
            </div>
        </div>

        <a href="dashboard.php" class="btn btn-back-dashboard w-100">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
