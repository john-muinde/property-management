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

// Check if service ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "Invalid service ID";
    $_SESSION['message_type'] = "danger";
    header('Location: spa.php');
    exit;
}

$service_id = $_GET['id'];

// Get service details
$sql = "SELECT * FROM spa_services WHERE id = $service_id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['message'] = "Service not found";
    $_SESSION['message_type'] = "danger";
    header('Location: spa.php');
    exit;
}

$service = mysqli_fetch_assoc($result);

// Initialize variables from database
$name = $service['name'];
$description = $service['description'];
$price = $service['price'];
$duration = $service['duration'];
$is_available = $service['is_available'];
$current_image = $service['image'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $duration = isset($_POST['duration']) ? $_POST['duration'] : 60;
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    // Validate required fields
    $errors = [];

    if (empty($name)) {
        $errors[] = "Service name is required";
    }

    if (empty($price) || !is_numeric($price) || $price <= 0) {
        $errors[] = "Please enter a valid price";
    }

    if (!is_numeric($duration) || $duration <= 0) {
        $errors[] = "Please enter a valid duration";
    }

    // Handle image upload if a new image is provided
    $image_name = $current_image; // Default to current image

    if ($_FILES['image']['name']) {
        $target_dir = "../images/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is valid
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $valid_extensions)) {
            $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed";
        } elseif ($_FILES["image"]["size"] > 5000000) { // 5MB max
            $errors[] = "Sorry, your file is too large";
        } else {
            // Upload file
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $errors[] = "Sorry, there was an error uploading your file";
            }
        }
    }

    // If no errors, update data in database
    if (empty($errors)) {
        $sql = "UPDATE spa_services SET 
                name = ?, 
                description = ?, 
                price = ?, 
                duration = ?, 
                image = ?, 
                is_available = ? 
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        // Fix: Changed parameter binding for image_name from i to s (string instead of integer)
        mysqli_stmt_bind_param($stmt, "ssdssii", $name, $description, $price, $duration, $image_name, $is_available, $service_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Service updated successfully";
            $_SESSION['message_type'] = "success";
            header('Location: spa.php');
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
    <title>Edit Spa Service - Lakeside Resorts</title>
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

        .current-image {
            max-width: 200px;
            max-height: 150px;
            margin-bottom: 10px;
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
                <a href="spa.php" class="active"><i class="fas fa-spa mr-2"></i> Spa Services</a>
                <a href="messages.php"><i class="fas fa-envelope mr-2"></i> Messages</a>
                <hr>
                <a href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </div>

            <!-- Main content -->
            <div class="col-md-10 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Edit Spa Service</h2>
                    <a href="spa.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-2"></i> Back to Spa Services</a>
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
                        <form action="spa_edit.php?id=<?php echo $service_id; ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Service Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price">Price ($)</label>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($price); ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="duration">Duration (minutes)</label>
                                        <input type="number" class="form-control" id="duration" name="duration" min="15" step="15" value="<?php echo htmlspecialchars($duration); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Current Image</label>
                                <div>
                                    <?php if (!empty($current_image) && file_exists("../images/" . $current_image)): ?>
                                        <img src="../images/<?php echo $current_image; ?>" alt="<?php echo htmlspecialchars($name); ?>" class="current-image">
                                    <?php else: ?>
                                        <p>No image currently set</p>
                                    <?php endif; ?>
                                </div>

                                <label for="image">Upload New Image</label>
                                <input type="file" class="form-control-file" id="image" name="image">
                                <small class="form-text text-muted">Leave empty to keep current image. Recommended size: 800x600 pixels. Max file size: 5MB</small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="is_available" name="is_available" <?php echo $is_available ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="is_available">Available for Booking</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Service</button>
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