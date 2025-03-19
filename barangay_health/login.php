<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, fullname, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barangay Healthcare System Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4; /* Light gray background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Arial', sans-serif;
            color: #333; /* Dark Gray Text */
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            opacity: 0;
            animation: fadeIn 1.5s forwards;
        }

        .login-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            background: #fff; /* White background for card */
            text-align: center;
        }

        .logo {
            width: 80px;
            display: block;
            margin: 0 auto 20px;
            opacity: 0;
            animation: logoFadeIn 1.5s 0.5s forwards;
        }

        .clinic-name {
            font-size: 28px;
            font-weight: bold;
            color: #2A3D66; /* Dark Blue for clinic name */
            opacity: 0;
            animation: textSlideIn 1.5s 1s forwards;
        }

        .form-label {
            font-weight: bold;
            color: #2A3D66; /* Dark Blue for form labels */
        }

        .input-group-text {
            background-color: #fff;
            border-left: 0;
            cursor: pointer;
        }

        .btn {
            transition: background-color 0.3s, transform 0.3s;
            padding: 10px 20px;
        }

        .btn-appointment, .btn-primary {
            border-radius: 10px; /* Rounded corners */
            padding: 10px 20px; /* Padding for a more rectangular shape */
        }

        .btn-appointment {
            background-color: #F0E999; /* Light yellow color */
            border: none;
            color: #333; /* Dark text for contrast */
        }

        .btn-appointment:hover {
            background-color: #E0D26B; /* Slightly darker yellow on hover */
            transform: scale(1.05);
        }

        .btn-primary {
            background-color: #2A3D66; /* Dark Blue */
            border: none;
            color: #fff; /* White text */
        }

        .btn-primary:hover {
            background-color: #1f2a44; /* Darker blue on hover */
            transform: scale(1.05);
        }

        .alert-danger {
            margin-bottom: 20px;
            background-color: #fff; /* White background */
            color: #D9534F; /* Red color for error */
            border: 1px solid #D9534F;
        }

        .forgot-password {
            font-size: 14px;
            color: #007BFF; /* Blue color for the link */
            text-decoration: none;
            margin-top: 10px;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes logoFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
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

    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card card">
        <img src="images/logo.jpg" alt="Logo" class="logo">
        <div class="clinic-name">Barangay Health Center Monitoring System</div>
    
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control" required>
                    <span class="input-group-text" onclick="togglePassword()">
                        <i id="eyeIcon" class="bi bi-eye-slash"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <!-- Changed Register button to Appointment button -->
            <a href="http://localhost/barangay_health/appointment_form.php" class="btn btn-appointment w-100 mt-2">Make Appointment</a>
        </form>

    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<script>
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var eyeIcon = document.getElementById("eyeIcon");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.replace("bi-eye-slash", "bi-eye");
        } else {
            passwordField.type = "password";
            eyeIcon.classList.replace("bi-eye", "bi-eye-slash");
        }
    }
</script>

</body>
</html>
