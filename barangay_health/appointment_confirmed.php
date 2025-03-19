<?php
session_start();

// Simulating appointment confirmation from health personnel
$confirmed = true; // Set this to `true` when appointment is confirmed by health personnel

// Simulate a random number for the "number in line"
$line_number = rand(1, 10);

// If the appointment is not yet confirmed, show a pending message
if (!$confirmed) {
    $line_number = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmed</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: Arial, sans-serif;
        }

        .container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            margin-top: 50px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .waiting-message {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            margin-top: 20px;
            display: none;
            opacity: 0;
            animation: fadeIn 1s forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* New button design with #F0E999 background and black text */
        .btn-primary {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            color: black;  /* Text color */
            background-color: #F0E999; /* Light yellow background */
            border: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #D4C400; /* Darker yellow on hover */
        }

        .text-center a {
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Your Appointment Request Has Been Submitted</h2>
        <p class="text-center">Thank you for submitting your appointment request. The health personnel will review it shortly.</p>

        <!-- If appointment is confirmed, show a message after 10 seconds -->
        <?php if ($confirmed): ?>
            <div id="waitingMessage" class="waiting-message">
                Your number in line is <?= $line_number ?>. Please be on time.
            </div>
        <?php else: ?>
            <p class="text-center">Your appointment is still pending approval from health personnel.</p>
        <?php endif; ?>

        <!-- Go Back to Homepage button -->
        <div class="text-center">
            <a href="login.php" class="btn btn-primary">Go Back to Homepage</a>
        </div>
    </div>

    <script>
        // Show the waiting message after 10 seconds
        setTimeout(function() {
            var waitingMessage = document.getElementById('waitingMessage');
            if (waitingMessage) {
                waitingMessage.style.display = 'block';
            }
        }, 10000); // 10000 milliseconds = 10 seconds
    </script>

</body>
</html>
