<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];

    // Update the appointment status to Approved
    $sql = "UPDATE appointments SET status = 'Approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        header("Location: appointments.php");
        exit();
    } else {
        echo "Error approving appointment.";
    }

    $stmt->close();
}
?>
