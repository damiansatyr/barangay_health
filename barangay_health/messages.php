<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// Handle sending messages (including replies)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];
    $reply_to_message_id = isset($_POST['reply_to_message_id']) ? $_POST['reply_to_message_id'] : null;

    // Check if the receiver exists in the users table
    $check_receiver = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $check_receiver->bind_param("i", $receiver_id);
    $check_receiver->execute();
    $check_receiver_result = $check_receiver->get_result();

    if ($check_receiver_result->num_rows == 0) {
        // If receiver doesn't exist, show an error message
        $_SESSION['message_status'] = "Error: The selected receiver does not exist.";
    } else {
        // Proceed with inserting the message if receiver exists
        if (!empty($message)) {
            $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, reply_to_message_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iisi", $sender_id, $receiver_id, $message, $reply_to_message_id);
            $stmt->execute();
            $stmt->close();

            // Set a success message
            $_SESSION['message_status'] = 'Your message has been sent successfully!';
        }
    }
}

// Handle deleting messages
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];
    $user_id = $_SESSION['user_id'];

    // Delete only if the logged-in user is the sender
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ? AND sender_id = ?");
    $stmt->bind_param("ii", $message_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch users for messaging (Exclude current user)
$users_result = $conn->query("SELECT * FROM users WHERE id != " . $_SESSION['user_id']);

// Check if the query was successful
if (!$users_result) {
    die('Error: ' . $conn->error);  // This will stop execution if there's an issue with the query
}

$users = $users_result;

// Fetch received messages for a specific conversation
if (isset($_GET['conversation_with'])) {
    $conversation_with = $_GET['conversation_with'];

    // Ensure sender_id is defined
    $sender_id = $_SESSION['user_id'];

    // Fetch messages between the sender and receiver
    $messages_result = $conn->query("SELECT messages.*, users.fullname AS sender_name 
                                    FROM messages 
                                    JOIN users ON messages.sender_id = users.id 
                                    WHERE (messages.sender_id = $sender_id AND messages.receiver_id = $conversation_with)
                                    OR (messages.sender_id = $conversation_with AND messages.receiver_id = $sender_id)
                                    ORDER BY messages.created_at ASC");

    // Check if the query was successful
    if (!$messages_result) {
        die('Error: ' . $conn->error);
    }
    $messages = $messages_result;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Container for messages */
        .message-container {
            max-height: 60vh;  /* Set max height to ensure scrolling */
            overflow-y: auto;  /* Enable vertical scrolling */
            margin-bottom: 80px;  /* Leave space for message input area */
            padding-right: 5px; /* Ensure no overlap with the scrollbar */
            margin-top: 10px;
        }

        /* Styling for the message bubbles */
        .message-bubble {
            padding: 10px 15px;
            border-radius: 15px;
            margin: 10px 0;
            position: relative; /* Add this to allow positioning of the delete icon */
            transition: all 0.3s ease;
            max-width: 75%; /* Adjust to fit screen better */
        }

        /* Styling for sent messages */
        .sent {
            background-color: #007bff;
            color: white;
            align-self: flex-end;
            animation: slideInRight 0.5s ease;
        }

        /* Styling for received messages */
        .received {
            background-color: #f1f1f1;
            color: black;
            align-self: flex-start;
            animation: slideInLeft 0.5s ease;
        }

        /* Delete button icon only */
        .message-actions {
            position: absolute;
            bottom: 5px;  /* Position at the bottom of the bubble */
            right: 5px;   /* Position at the right of the bubble */
        }

        .message-actions button {
            background: none;
            border: none;
            padding: 0;
        }

        .message-actions i {
            font-size: 18px;
            color: #ff5733;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .message-actions i:hover {
            transform: scale(1.3);
        }

        /* Timestamp styling */
        .message-timestamp {
            font-size: 0.8em;
            color: #ffff;
        }

        /* Message input container */
        .message-input-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            padding: 15px;
            border-top: 1px solid #ddd;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        /* User list styling */
        .list-group-item {
            cursor: pointer;
        }

        .list-group-item:hover {
            background-color: #f1f1f1;
        }

        /* Animations */
        @keyframes slideInRight {
            0% { transform: translateX(100%); }
            100% { transform: translateX(0); }
        }

        @keyframes slideInLeft {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(0); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .message-bubble {
                max-width: 90%; /* Reduce width on mobile */
            }
            .message-input-container {
                padding: 10px;
            }
            .list-group-item {
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            .message-input-container {
                padding: 5px;
            }
        }

        /* Styling for buttons container */
        .button-container {
            display: flex;
            gap: 10px;  /* Adds space between buttons */
        }

        .button-container .btn {
            flex-shrink: 0;  /* Prevents the buttons from shrinking */
        }

    </style>
</head>
<body>

<div class="container my-4">
    <div class="header text-center mb-4">
        <h2>Messaging System</h2>
    </div>

    <!-- Display success message if exists -->
    <?php if (isset($_SESSION['message_status'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message_status']; ?></div>
        <?php unset($_SESSION['message_status']); ?>
    <?php endif; ?>

    <!-- Display the list of users to start a conversation -->
    <div class="list-group mb-4">
        <?php while ($row = $users->fetch_assoc()): ?>
            <a href="?conversation_with=<?= $row['id'] ?>" class="list-group-item">
                <?= $row['fullname'] ?> (<?= $row['role'] ?>)
            </a>
        <?php endwhile; ?>
    </div>

    <?php if (isset($messages)): ?>
        <div class="message-container d-flex flex-column">
            <!-- Display messages in a threaded view -->
            <?php while ($msg = $messages->fetch_assoc()): ?>
                <div class="message-bubble <?= $msg['sender_id'] == $_SESSION['user_id'] ? 'sent' : 'received' ?>">
                    <strong><?= $msg['sender_name'] ?></strong>
                    <p><?= $msg['message'] ?></p>
                    <div class="message-timestamp"><?= $msg['created_at'] ?></div>

                    <?php if ($msg['sender_id'] == $_SESSION['user_id']): ?>
                        <div class="message-actions">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                <button type="submit" name="delete_message" style="border: none; background: none;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Reply form -->
        <div class="message-input-container">
            <form method="POST">
                <input type="hidden" name="receiver_id" value="<?= $conversation_with ?>">
                <textarea name="message" class="form-control" rows="3" placeholder="Type your message here..." required></textarea>
                <div class="button-container d-flex mt-2">
                    <button type="submit" name="send_message" class="btn btn-primary flex-grow-1">Send</button>
                    <a href="dashboard.php" class="btn btn-secondary ml-2">Back to Dashboard</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
