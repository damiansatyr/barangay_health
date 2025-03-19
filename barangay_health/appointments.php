<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];

// Fetch patients for dropdown
$patients = $conn->query("SELECT id, fullname FROM patients");

// Add Appointment (Barangay Health Personnel Only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_appointment'])) {
    $patient_id = $_POST['patient_id'];
    $doctor = $_POST['doctor'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_type = $_POST['appointment_type'];

    // If 'Others' is selected, use the value from the 'other_appointment_type' field
    if ($appointment_type == 'Others') {
        $appointment_type = $_POST['other_appointment_type'];
    }

    $status = "Pending";

    $sql = "INSERT INTO appointments (patient_id, doctor, appointment_date, appointment_type, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $patient_id, $doctor, $appointment_date, $appointment_type, $status);
    if ($stmt->execute()) {
        echo "Appointment Added Successfully!";
    } else {
        echo "Error Adding Appointment.";
    }
    $stmt->close();
}
if (isset($_GET['approve'])) {
    $appointment_id = $_GET['approve'];
    $sql = "UPDATE appointments SET status = 'Approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    if ($stmt->execute()) {
        // Fetch the appointment details to pass to the confirmation page
        $appointment_sql = "SELECT * FROM appointments WHERE id = ?";
        $appointment_stmt = $conn->prepare($appointment_sql);
        $appointment_stmt->bind_param("i", $appointment_id);
        $appointment_stmt->execute();
        $result = $appointment_stmt->get_result();
        $appointment = $result->fetch_assoc();
        
      
    } else {
        echo "Error approving appointment.";
    }
    $stmt->close();
}

if (isset($_GET['done'])) {
    $appointment_id = $_GET['done'];
    $sql = "UPDATE appointments SET status = 'Done Appointment', completed = 'Yes' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    if ($stmt->execute()) {
        header("Location: appointments.php");
        exit();
    } else {
        echo "Error updating appointment status.";
    }
    $stmt->close();
}

// Fetch appointments
$appointments = $conn->query("SELECT a.id, p.fullname AS patient_name, a.doctor, a.appointment_date, a.appointment_type, a.status, a.completed FROM appointments a JOIN patients p ON a.patient_id = p.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Scheduling</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
            animation: fadeIn 1s ease-out;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 50px;
        }

        h2 {
            color: #2A3D66; /* Dark Blue */
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            opacity: 0;
            animation: textSlideIn 1s forwards;
        }

        .table {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: slideUp 1s ease-out;
        }

        .btn {
            transition: background-color 0.3s, transform 0.3s;
            border-radius: 25px;
        }

        /* Custom Blue Button for Health Personnel Schedule */
        .btn-schedule {
            background-color: #007bff; /* Blue */
            border: none;
            color: white;
        }

        .btn-schedule:hover {
            background-color: #0056b3; /* Darker Blue */
            transform: scale(1.05);
        }

        .btn-danger {
            background-color: #dc3545; /* Red */
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

        .btn-success {
            background-color: #28a745; /* Green */
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: scale(1.05);
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

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }

        .form-control, .btn {
            border-radius: 8px;
        }

        .form-label {
            font-weight: bold;
            color: #2A3D66; /* Dark Blue for form labels */
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

        @media (max-width: 767px) {
            .container {
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Appointment Scheduling</h2>

        <?php if ($user_role == "Barangay Health Personnel"): ?>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <select name="patient_id" class="form-control" required>
                    <option value="">Select Patient</option>
                    <?php while ($row = $patients->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['fullname'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <select name="doctor" class="form-control" required>
                    <option value="">Select Doctor</option>
                    <option value="Doctor Tanggol Montenegro">Doctor Tanggol Montenegro</option>
                    <option value="Doctor Rigor Dimaguiba">Doctor Rigor Dimaguiba</option>
                </select>
            </div>
            <div class="mb-3">
                <input type="datetime-local" name="appointment_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <select name="appointment_type" id="appointment_type" class="form-control" required>
                    <option value="">Select Appointment Type</option>
                    <option value="Dental">Dental</option>
                    <option value="Health">Health</option>
                    <option value="Mental">Mental</option>
                    <option value="Pediatric">Pediatric</option>
                    <option value="General Checkup">General Checkup</option>
                    <option value="Vaccination">Vaccination</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="mb-3" id="otherTypeField" style="display: none;">
                <input type="text" name="other_appointment_type" class="form-control" placeholder="Specify Other Appointment Type">
            </div>
            <button type="submit" name="add_appointment" class="btn btn-schedule">Schedule Appointment</button>
        </form>
        <?php endif; ?>

        <div class="appointment-table-wrapper">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Doctor</th>
                        <th>Appointment Date</th>
                        <th>Appointment Type</th>
                        <th>Status</th>
                        <th>Completed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['patient_name'] ?></td>
                        <td><?= $row['doctor'] ?></td>
                        <td><?= $row['appointment_date'] ?></td>
                        <td><?= $row['appointment_type'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td><?= $row['completed'] ?></td>
                        <td>
                            <?php if ($row['status'] == "Done Appointment"): ?>
                                <span class="badge bg-success">Done Appointment</span>
                            <?php else: ?>
                                <?php if ($user_role == "Health Personnel President" && $row['status'] == "Pending"): ?>
                                    <a href="appointments.php?approve=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                                    <a href="appointments.php?reject=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                                <?php elseif ($user_role == "Health Personnel President" && $row['status'] == "Approved"): ?>
                                    <a href="appointments.php?done=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Mark as Done</a>
                                <?php endif; ?>
                                <?php if ($user_role == "Admin"): ?>
                                    <a href="appointments.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Back to Dashboard Button -->
        <a href="dashboard.php" class="btn btn-back-dashboard">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show or hide the 'Others' input field based on the selection
        document.getElementById('appointment_type').addEventListener('change', function() {
            const otherTypeField = document.getElementById('otherTypeField');
            if (this.value === 'Others') {
                otherTypeField.style.display = 'block';
            } else {
                otherTypeField.style.display = 'none';
            }
        });
    </script>
</body>
</html>
