<?php
// Include a PDF library like FPDF or use any other way to generate PDFs
require('fpdf.php'); // Ensure you have the FPDF library installed

// Get the confirmation number from the query parameter
$confirmation_number = isset($_GET['confirmation_number']) ? $_GET['confirmation_number'] : null;

// If the confirmation number is invalid, redirect to the confirmation page
if (!$confirmation_number) {
    header("Location: appointment_confirmed.php");
    exit();
}

// Create a PDF document
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Add title
$pdf->Cell(200, 10, "Appointment Confirmation", 0, 1, 'C');

// Add the confirmation number
$pdf->Cell(200, 10, "Confirmation Number: $confirmation_number", 0, 1, 'C');

// Additional details (Patient info could be fetched from the session or database)
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(200, 10, "Thank you for scheduling your appointment with us. Please present this confirmation number at the clinic for check-up.", 0, 1, 'C');

// Output the PDF to the browser
$pdf->Output('I', "confirmation_$confirmation_number.pdf");
?>
