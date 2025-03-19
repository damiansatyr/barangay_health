<?php
// Start session
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'barangay_health');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $fullname = $_POST['fullname'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $age = $_POST['age']; // Capture age
    $doctor = $_POST['doctor'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_type = $_POST['appointment_type'];

    // If 'Others' is selected for appointment type, use the value from the other_appointment_type field
    if ($appointment_type == 'Others') {
        $appointment_type = $_POST['other_appointment_type'];
    }

    // Check if the patient already exists in the 'patients' table based on their fullname, contact, and address
    $sql = "SELECT id FROM patients WHERE fullname = ? AND contact = ? AND address = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $fullname, $contact, $address);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the patient exists, use their patient_id
    if ($result->num_rows > 0) {
        // Fetch the patient_id
        $patient = $result->fetch_assoc();
        $patient_id = $patient['id'];
    } else {
        // If the patient doesn't exist, insert them into the 'patients' table
        $sql = "INSERT INTO patients (fullname, contact, address, age) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $fullname, $contact, $address, $age); // Include age in the query

        if ($stmt->execute()) {
            // Fetch the id of the newly inserted patient
            $patient_id = $stmt->insert_id;
        } else {
            die("Error inserting patient: " . $stmt->error);
        }
    }

    // Now, insert the appointment request for this patient
    if (isset($patient_id) && $patient_id) {
        $status = "Pending";  // Status will be set to Pending until health personnel approves
        $sql = "INSERT INTO appointments (patient_id, doctor, appointment_date, appointment_type, status) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $patient_id, $doctor, $appointment_date, $appointment_type, $status);

        if ($stmt->execute()) {
            // Redirect to the confirmation page after successful submission
            header("Location: appointment_confirmed.php");
            exit();
        } else {
            echo "Error submitting appointment: " . $stmt->error;
        }
    } else {
        echo "Error: No valid patient found.";
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
</head>
<body>

    <div class="container">
        <h2>Request an Appointment</h2>
        <form method="POST">
            
            <!-- Patient Information -->
            <div class="form-group">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" name="fullname" id="fullname" class="form-control" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="contact" class="form-label">Contact Number</label>
                <input type="tel" name="contact" id="contact" class="form-control" placeholder="Enter your contact number" required>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" id="address" class="form-control" placeholder="Enter your address" required>
            </div>

            <!-- Age Field -->
            <div class="form-group">
                <label for="age" class="form-label">Age</label>
                <input type="number" name="age" id="age" class="form-control" placeholder="Enter your age" required>
            </div>

            <!-- Doctor Selection -->
            <div class="form-group">
                <label for="doctor" class="form-label">Select Doctor</label>
                <select name="doctor" id="doctor" class="form-control" required>
                    <option value="">Select Doctor</option>
                    <option value="Doctor Tanggol Montenegro">Doctor Tanggol Montenegro</option>
                    <option value="Doctor Rigor Dimaguiba">Doctor Rigor Dimaguiba</option>
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
            <div class="form-group" id="otherTypeField" style="display:none;">
                <label for="other_appointment_type" class="form-label">Specify Other Appointment Type</label>
                <input type="text" name="other_appointment_type" id="other_appointment_type" class="form-control" placeholder="Enter other appointment type">
            </div>

            <button type="submit" class="btn btn-primary">Submit Appointment Request</button>
        </form>
    </div>

    <script>
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
