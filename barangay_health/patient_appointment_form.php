<?php
// Assuming a session is started and patient information is available
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the list of available doctors
$doctors = [
    "Doctor Tanggol Montenegro",
    "Doctor Rigor Dimaguiba"
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_appointment'])) {
    $patient_id = $_SESSION['user_id'];  // Get patient ID from session
    $doctor = $_POST['doctor'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_type = $_POST['appointment_type'];
    $name = $_POST['name'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    // If 'Others' is selected for appointment type, use the value from the other_appointment_type field
    if ($appointment_type == 'Others') {
        $appointment_type = $_POST['other_appointment_type'];
    }

    $status = "Pending";  // Status will be set to Pending until health personnel approves

    // Insert the appointment request into the database
    $sql = "INSERT INTO appointments (patient_id, doctor, appointment_date, appointment_type, status, name, contact_number, address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $patient_id, $doctor, $appointment_date, $appointment_type, $status, $name, $contact_number, $address);

    if ($stmt->execute()) {
        // After successful submission, redirect to confirmation page
        header("Location: appointment_confirmed.php");
        exit();
    } else {
        echo "Error submitting appointment.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Request Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            transition: all 0.3s ease;
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 28px;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .form-control {
            border-radius: 10px;
            height: 40px;
            box-shadow: none;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-text {
            font-size: 0.9rem;
            color: #6c757d;
        }

        #otherTypeField {
            display: none;
        }

        .select2-container {
            width: 100% !important;
        }

        /* Styling for the custom 'Other' input */
        #otherTypeField input {
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Request an Appointment</h2>
        <form method="POST">
            
            <!-- Patient Information -->
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="tel" name="contact_number" id="contact_number" class="form-control" placeholder="Enter your contact number" required>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" id="address" class="form-control" placeholder="Enter your address" required>
            </div>

            <!-- Doctor Selection -->
            <div class="form-group">
                <label for="doctor" class="form-label">Select Doctor</label>
                <select name="doctor" id="doctor" class="form-control" required>
                    <option value="">Select Doctor</option>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= $doctor ?>"><?= $doctor ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Appointment Date -->
            <div class="form-group">
                <label for="appointment_date" class="form-label">Appointment Date</label>
                <input type="datetime-local" name="appointment_date" id="appointment_date" class="form-control" required>
            </div>

            <!-- Appointment Type -->
            <div class="form-group">
                <label for="appointment_type" class="form-label">Appointment Type</label>
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

            <!-- Other Appointment Type -->
            <div class="form-group" id="otherTypeField">
                <label for="other_appointment_type" class="form-label">Specify Other Appointment Type</label>
                <input type="text" name="other_appointment_type" id="other_appointment_type" class="form-control" placeholder="Enter other appointment type">
            </div>

            <button type="submit" name="submit_appointment" class="btn btn-primary">Submit Appointment Request</button>
        </form>
<!-- Go to Homepage button -->
<div class="text-center mt-3">
<a href="app/login.php" class="btn btn-secondary">Go to Homepage (Login)</a>

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
