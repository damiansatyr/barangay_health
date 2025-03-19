<?php
session_start();  // Start the session

// Redirect to login if session is not set
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';  // Include the database connection

// Fetch user ID from session
$user_id = $_SESSION['user_id'];

// Initialize notification counts
$pending_appointments_count = 0;
$pending_reports_count = 0;
$unread_messages_count = 0;

// Fetch counts for unread notifications
$pending_appointments_query = "SELECT COUNT(*) AS pending_appointments FROM appointments WHERE user_id = $user_id AND notification_status = 'unread'";
$pending_appointments_result = $conn->query($pending_appointments_query);
if ($pending_appointments_result) {
    $pending_appointments_count = $pending_appointments_result->fetch_assoc()['pending_appointments'];
}

$pending_reports_query = "SELECT COUNT(*) AS pending_reports FROM reports WHERE user_id = $user_id AND notification_status = 'unread'";
$pending_reports_result = $conn->query($pending_reports_query);
if ($pending_reports_result) {
    $pending_reports_count = $pending_reports_result->fetch_assoc()['pending_reports'];
}

$unread_messages_query = "SELECT COUNT(*) AS unread_messages FROM messages WHERE user_id = $user_id AND notification_status = 'unread'";
$unread_messages_result = $conn->query($unread_messages_query);
if ($unread_messages_result) {
    $unread_messages_count = $unread_messages_result->fetch_assoc()['unread_messages'];
}

// Fetch other data like appointments and patients count
$appointments_count_query = "SELECT COUNT(*) AS total_appointments FROM appointments";
$appointments_result = $conn->query($appointments_count_query);
$appointments_count = $appointments_result->fetch_assoc()['total_appointments'];

$patients_count_query = "SELECT COUNT(*) AS total_patients FROM patients";
$patients_result = $conn->query($patients_count_query);
$patients_count = $patients_result->fetch_assoc()['total_patients'];

// Fetch appointment status data
$status_query = "SELECT status, COUNT(*) AS count FROM appointments GROUP BY status";
$status_result = $conn->query($status_query);
$status_data = [];
while ($row = $status_result->fetch_assoc()) {
    $status_data[$row['status']] = $row['count'];
}

// Check if notification ID is passed and update the notification status to 'read'
if (isset($_GET['notification_id'])) {
    $notification_id = $_GET['notification_id'];

    // Update the notification status for appointments
    if ($notification_id === 'appointments') {
        $update_query = "UPDATE appointments SET notification_status = 'read' WHERE user_id = $user_id AND notification_status = 'unread'";
        $conn->query($update_query);
        header("Location: appointments.php"); // Redirect to the appointments page
        exit();
    }
    // Similarly, update reports and messages if needed...
    else if ($notification_id === 'reports') {
        $update_query = "UPDATE reports SET notification_status = 'read' WHERE user_id = $user_id AND notification_status = 'unread'";
        $conn->query($update_query);
        header("Location: reports.php"); // Redirect to the reports page
        exit();
    }
    else if ($notification_id === 'messages') {
        $update_query = "UPDATE messages SET notification_status = 'read' WHERE user_id = $user_id AND notification_status = 'unread'";
        $conn->query($update_query);
        header("Location: messages.php"); // Redirect to the messages page
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Page Background */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            animation: fadeIn 1.2s ease-in-out;
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #2A3D66;
            padding-top: 30px;
            padding-left: 20px;
            color: white;
            animation: slideUp 0.8s ease-in-out;
            z-index: 999;
            box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar .logo img {
            width: 80%;
            max-width: 150px;
            animation: fadeIn 1s ease-in-out;
        }

        .sidebar .nav-link {
            color: white;
            font-size: 18px;
            margin: 15px 0;
            padding: 10px 15px;
            transition: background-color 0.3s;
            position: relative;
            border-radius: 5px;
        }

        .sidebar .nav-link:hover {
            background-color: #1D2A45;
            cursor: pointer;
        }

        /* Notification badges */
        .notification-badge {
            background-color: #f44336;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 0.75rem;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        /* Content styles */
        .content {
            margin-left: 270px;
            padding: 20px;
            animation: slideUp 1s ease-in-out;
        }

        /* Card Styling */
        .report-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: slideUp 1.2s ease-in-out;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .report-card h5 {
            font-size: 1.25rem;
            margin-bottom: 10px;
            color: #2A3D66;
            animation: textSlideIn 1.5s ease-in-out;
        }

        .report-card .count {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
        }

        /* Chart Section */
        .report-box {
            margin-top: 40px;
            animation: slideUp 1.2s ease-in-out;
        }

        canvas {
            max-width: 100%;
            height: 300px;
            opacity: 0;
            animation: slideUp 1s forwards;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 210px;
            }

            .report-card {
                margin-bottom: 10px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 180px;
            }

            .content {
                margin-left: 190px;
            }

            .report-card {
                margin-bottom: 5px;
            }

            canvas {
                height: 250px;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="images/logo.jpg" alt="Logo" class="logo">
    </div>
    <ul class="nav flex-column">
        <!-- View Appointments -->
        <li class="nav-item">
            <a class="nav-link" href="appointments.php">
                <i class="fas fa-calendar-check"></i> Appointments
                <?php if ($pending_appointments_count > 0): ?>
                    <span class="notification-badge"><?php echo $pending_appointments_count; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <!-- Reports -->
        <li class="nav-item">
            <a class="nav-link" href="reports.php">
                <i class="fas fa-file-alt"></i> Reports
                <?php if ($pending_reports_count > 0): ?>
                    <span class="notification-badge"><?php echo $pending_reports_count; ?></span>
                <?php endif; ?>
            </a>
        </li>


        <!-- Messages -->
        <li class="nav-item">
            <a class="nav-link" href="messages.php">
                <i class="fas fa-comment-alt"></i> Messages
                <?php if ($unread_messages_count > 0): ?>
                    <span class="notification-badge"><?php echo $unread_messages_count; ?></span>
                <?php endif; ?>
            </a>

   <!-- Patients -->
        <li class="nav-item">
            <a class="nav-link" href="patients.php">
                <i class="fas fa-users"></i> Patients
            </a>
        </li>

              <!-- Inventory -->
        <li class="nav-item">
            <a class="nav-link" href="inventory.php">
                <i class="fas fa-box"></i> Inventory
            </a>
        </li>
        </li>


        <!-- Logout -->
        <li class="nav-item">
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="content">
    <p>Welcome, <?php echo $_SESSION['fullname']; ?>!</p>
    <p>Role: <?php echo $_SESSION['role']; ?></p>

    <!-- Reports Section -->
    <div class="row">
        <!-- Total Appointments Card -->
        <div class="col-md-3">
            <div class="report-card">
                <h5>Total Appointments</h5>
                <div class="count"><?php echo $appointments_count; ?></div>
            </div>
        </div>
        <!-- Total Patients Card -->
        <div class="col-md-3">
            <div class="report-card">
                <h5>Total Patients</h5>
                <div class="count"><?php echo $patients_count; ?></div>
            </div>
        </div>
        <!-- Pending Appointments Card -->
        <div class="col-md-3">
            <div class="report-card">
                <h5>Pending Appointments</h5>
                <div class="count"><?php echo isset($status_data['Pending']) ? $status_data['Pending'] : 0; ?></div>
            </div>
        </div>
        <!-- Approved Appointments Card -->
        <div class="col-md-3">
            <div class="report-card">
                <h5>Approved Appointments</h5>
                <div class="count"><?php echo isset($status_data['Approved']) ? $status_data['Approved'] : 0; ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Rejected Appointments Card -->
        <div class="col-md-3">
            <div class="report-card">
                <h5>Rejected Appointments</h5>
                <div class="count"><?php echo isset($status_data['Rejected']) ? $status_data['Rejected'] : 0; ?></div>
            </div>
        </div>
        <!-- Done Appointments Card -->
        <div class="col-md-3">
            <div class="report-card">
                <h5>Done Appointments</h5>
                <div class="count"><?php echo isset($status_data['Done Appointment']) ? $status_data['Done Appointment'] : 0; ?></div>
            </div>
        </div>
    </div>

    <!-- Appointment Status Bar Chart -->
    <div class="report-box">
        <h4>Appointment Status</h4>
        <canvas id="statusChart"></canvas>
    </div>
</div>

<!-- Chart.js -->
<script>
    var ctx = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Approved', 'Rejected', 'Done Appointment'],
            datasets: [{
                label: 'Number of Appointments',
                data: [
                    <?php echo isset($status_data['Pending']) ? $status_data['Pending'] : 0; ?>,
                    <?php echo isset($status_data['Approved']) ? $status_data['Approved'] : 0; ?>,
                    <?php echo isset($status_data['Rejected']) ? $status_data['Rejected'] : 0; ?>,
                    <?php echo isset($status_data['Done Appointment']) ? $status_data['Done Appointment'] : 0; ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
