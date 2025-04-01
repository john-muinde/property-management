<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Include database connection
require_once '../includes/connect.php';

// Check if user ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "Invalid user ID";
    $_SESSION['message_type'] = "danger";
    header('Location: users.php');
    exit;
}

$user_id = $_GET['id'];

// Get user details
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['message'] = "User not found";
    $_SESSION['message_type'] = "danger";
    header('Location: users.php');
    exit;
}

$user = mysqli_fetch_assoc($result);

// Initialize variables from database
$username = $user['username'];
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Password validation (only if a new password is provided)
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $errors[] = "Password must be at least 6 characters long";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
    }

    // If no errors, update user in database
    if (empty($errors)) {
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);

            if (!mysqli_stmt_execute($stmt)) {
                $errors[] = "Error updating password: " . mysqli_error($conn);
            }
        }

        if (empty($errors)) {
            $_SESSION['message'] = "User updated successfully";
            $_SESSION['message_type'] = "success";
            header('Location: users.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Lakeside Resorts</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: #343a40;
            color: white;
            min-height: 100vh;
            padding: 20px 0;
        }

        .sidebar a {
            color: rgba(255, 255, 255, .75);
            padding: 10px 20px;
            display: block;
        }

        .sidebar a:hover {
            color: white;
            text-decoration: none;
            background-color: rgba(255, 255, 255, .1);
        }

        .sidebar .active {
            color: white;
            background-color: rgba(255, 255, 255, .1);
        }

        .content {
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h3 class="text-center mb-4">Admin Panel</h3>
                <div class="text-center mb-4">
                    Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                </div>
                <hr>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
                <a href="rooms.php"><i class="fas fa-bed mr-2"></i> Rooms</a>
                <a href="bookings.php"><i class="fas fa-calendar-alt mr-2"></i> Bookings</a>
                <a href="spa.php"><i class="fas fa-spa mr-2"></i> Spa Services</a>
                <a href="messages.php"><i class="fas fa-envelope mr-2"></i> Messages</a>
                <a href="users.php" class="active"><i class="fas fa-users mr-2"></i> Users</a>
                <hr>
                <a href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </div>

            <!-- Main content -->
            <div class="col-md-10 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Edit User: <?php echo htmlspecialchars($username); ?></h2>
                    <a href="users.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-2"></i> Back to Users</a>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form action="user_edit.php?id=<?php echo $user_id; ?>" method="post">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                                <small class="form-text text-muted">Username cannot be changed.</small>
                            </div>

                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                                <small class="form-text text-muted">Leave blank to keep current password. New password must be at least 6 characters long.</small>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>

                            <button type="submit" class="btn btn-primary">Update User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>