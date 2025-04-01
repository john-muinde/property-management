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

// Initialize variables
$username = '';
$password = '';
$confirm_password = '';
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validate form data
    if (empty($username)) {
        $errors[] = "Username is required";
    } else {
        // Check if username already exists
        $sql = "SELECT id FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $errors[] = "Username already exists";
        }
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    if (empty($errors)) {
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $_SESSION['message'] = "User added successfully";
            $_SESSION['message_type'] = "success";
            header('Location: users.php');
            exit;
        } else {
            $errors[] = "Failed to add user";
        }
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // If no errors, insert user into database
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "User added successfully";
            $_SESSION['message_type'] = "success";
            header('Location: users.php');
            exit;
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Lakeside Resorts</title>
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
                    <h2>Add New User</h2>
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
                        <form action="user_add.php" method="post">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Add User</button>
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