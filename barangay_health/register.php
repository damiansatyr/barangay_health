<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fullname, $email, $password, $role);
    
    if ($stmt->execute()) {
        header("Location: login.php"); // Redirect to login page after successful registration
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
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
    <title>User Registration - Barangay Healthcare System Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4; /* Light gray background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Arial', sans-serif;
            color: #333; /* Dark gray text */
            overflow: hidden;
        }

        .registration-container {
            max-width: 400px;
            width: 100%;
            opacity: 0;
            animation: fadeIn 1.5s forwards;
        }

        .registration-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            background: white;
            text-align: center;
        }

        /* Logo and clinic name animation */
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
            color: #2A3D66; /* Dark Blue */
            opacity: 0;
            animation: textSlideIn 1.5s 1s forwards;
        }

        .clinic-subtitle {
            font-size: 18px;
            color: #F0E999; /* Light yellow */
            font-weight: bold;
            margin-top: -5px;
            opacity: 0;
            animation: textSlideIn 1.5s 1.3s forwards;
        }

        .form-label {
            font-weight: bold;
            color: #2A3D66; /* Dark Blue */
        }

        .btn {
            transition: background-color 0.3s, transform 0.3s;
            border-radius: 10px; /* Rounded corners */
            padding: 10px 20px;
        }

        .btn-primary {
            background-color: #2A3D66; /* Dark Blue */
            border: none;
            color: #fff; /* White text */
        }

        .btn-primary:hover {
            background-color: #1f2a44; /* Darker blue */
            transform: scale(1.05);
        }

        .alert-danger {
            margin-bottom: 20px;
            background-color: #fff; /* White background */
            color: #D9534F; /* Red color for error */
            border: 1px solid #D9534F;
        }

        .back-to-login {
            font-size: 14px;
            color: #007bff; /* Blue color for the link */
            text-decoration: none;
            margin-top: 10px;
        }

        .back-to-login:hover {
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

<div class="registration-container">
    <div class="registration-card">
    <img src="images/logo.jpg" alt="Logo" class="logo">
        <div class="clinic-name">Barangay Healthcare System Management</div>
        
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
      
        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label">Full Name</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="Health Personnel President">Health Personnel President</option>
                    <option value="Barangay Health Personnel">Barangay Health Personnel</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <a href="login.php" class="back-to-login">Already have an account? Login here.</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
