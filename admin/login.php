<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once '../includes/connect.php';

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Initialize error variable
$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // For development/testing - hardcoded credentials
        // In production, you would check against database records
        if ($username === 'admin' && $password === 'admin123') {
            // Login successful - set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_username'] = $username;

            // Redirect to dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            // Try checking against database
            $query = "SELECT * FROM users WHERE username = '$username'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);

                // First try PHP's password_verify (for hashed passwords)
                if (function_exists('password_verify') && password_verify($password, $user['password'])) {
                    // Password is correct
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];

                    // Redirect to dashboard
                    header('Location: dashboard.php');
                    exit;
                }
                // Try direct comparison (not recommended for production)
                elseif ($password === $user['password']) {
                    // Plain text password matched (NOT SECURE)
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];

                    // Redirect to dashboard
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = "Invalid username or password";
                }
            } else {
                $error = "Invalid username or password";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lakeside Resorts</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }

        .login-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-header img {
            max-width: 150px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <?php if (file_exists('../images/logo-2.svg')): ?>
                    <img src="../images/logo-2.svg" alt="Lakeside Resorts and Spa">
                <?php endif; ?>
                <h2>Admin Login</h2>
                <p>Lakeside Resorts and Spa</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="text-center mt-3">
                <a href="../index.php">Back to Website</a>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">Default login: admin / admin123</small>
            </div>
        </div>
    </div>
</body>

</html>