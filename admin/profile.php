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

// Get user ID from session
$user_id = $_SESSION['admin_id'];

// Get user details
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['message'] = "User not found";
    $_SESSION['message_type'] = "danger";
    header('Location: dashboard.php');
    exit;
}

$user = mysqli_fetch_assoc($result);

// Initialize variables from database
$username = $user['username'];
$email = $user['email'];
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validate form data
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    } else {
        // Check if email already exists for other users
        $sql = "SELECT id FROM users WHERE email = '$email' AND id != $user_id";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $errors[] = "Email already exists";
        }
    }

    // Password validation (only if a new password is provided)
    if (!empty($new_password)) {
        // Verify current password
        if (empty($current_password)) {
            $errors[] = "Current password is required to set a new password";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect";
        }

        if (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters long";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
    }

    // If no errors, update user in database
    if (empty($errors)) {
        // Update email
        $sql = "UPDATE users SET email = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $email, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            // Update password if provided
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
                $_SESSION['message'] = "Profile updated successfully";
                $_SESSION['message_type'] = "success";

                // Refresh user data
                $sql = "SELECT * FROM users WHERE id = $user_id";
                $result = mysqli_query($conn, $sql);
                $user = mysqli_fetch_assoc($result);
                $email = $user['email'];
            }
        } else {
            $errors[] = "Error updating profile: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Lakeside Resorts</title>
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

        .profile-header {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-info {
            display: flex;
            align-items: center;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background-color: #6c757d;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 20px;
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
                <a href="users.php"><i class="fas fa-users mr-2"></i> Users</a>
                <hr>
                <a href="profile.php" class="active"><i class="fas fa-user mr-2"></i> My Profile</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </div>

            <!-- Main content -->
            <div class="col-md-10 content">
                <h2 class="mb-4">My Profile</h2>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                        <?php
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-info">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h4><?php echo htmlspecialchars($username); ?></h4>
                            <p class="text-muted mb-0"><?php echo isset($user['role']) ? ucfirst($user['role']) : 'Staff'; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Account Information</h5>

                        <form action="profile.php" method="post">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                                <small class="form-text text-muted">Username cannot be changed.</small>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>

                            <h5 class="mt-4 mb-3">Change Password</h5>

                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                                <small class="form-text text-muted">Required only if you want to change your password.</small>
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

                            <button type="submit" class="btn btn-primary">Update Profile</button>
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