<?php
include 'db_connect.php';

// Fetch pending appointments
$sql = "SELECT a.id, p.fullname AS patient_name, a.doctor, a.appointment_date, a.appointment_type, a.status 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        WHERE a.status = 'Pending'";

$result = $conn->query($sql);

// Handle approval/rejection
if (isset($_GET['approve'])) {
    $appointment_id = $_GET['approve'];
    $sql = "UPDATE appointments SET status = 'Approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    if ($stmt->execute()) {
        echo "Appointment Approved!";
        header("Location: pending_appointments.php");
        exit();
    } else {
        echo "Error approving appointment.";
    }
    $stmt->close();
}

if (isset($_GET['reject'])) {
    $appointment_id = $_GET['reject'];
    $sql = "UPDATE appointments SET status = 'Rejected' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    if ($stmt->execute()) {
        echo "Appointment Rejected!";
        header("Location: pending_appointments.php");
        exit();
    } else {
        echo "Error rejecting appointment.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Appointments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Pending Appointments</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Doctor</th>
                    <th>Appointment Date</th>
                    <th>Appointment Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['patient_name'] ?></td>
                    <td><?= $row['doctor'] ?></td>
                    <td><?= $row['appointment_date'] ?></td>
                    <td><?= $row['appointment_type'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <a href="pending_appointments.php?approve=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                        <a href="pending_appointments.php?reject=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
