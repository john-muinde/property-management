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

// Delete room if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $room_id = $_GET['delete'];
    $sql = "DELETE FROM rooms WHERE id = $room_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Room deleted successfully";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting room: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
    }

    // Redirect to remove the query string
    header('Location: rooms.php');
    exit;
}

// Toggle availability if requested
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $room_id = $_GET['toggle'];

    // Get current availability status
    $check_sql = "SELECT is_available FROM rooms WHERE id = $room_id";
    $result = mysqli_query($conn, $check_sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $current_status = $row['is_available'];

        // Toggle status (1 to 0 or 0 to 1)
        $new_status = $current_status ? 0 : 1;

        // Update status
        $update_sql = "UPDATE rooms SET is_available = $new_status WHERE id = $room_id";

        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['message'] = "Room availability updated";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating availability: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
        }
    }

    // Redirect to remove the query string
    header('Location: rooms.php');
    exit;
}

// Get all rooms
$rooms = [];
$sql = "SELECT * FROM rooms ORDER BY id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms - Lakeside Resorts</title>
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

        .room-image {
            width: 100px;
            height: 75px;
            object-fit: cover;
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
                <a href="users.php"><i class="fas fa-users mr-2"></i> Users</a>
                <a href="rooms.php" class="active"><i class="fas fa-bed mr-2"></i> Rooms</a>
                <a href="bookings.php"><i class="fas fa-calendar-alt mr-2"></i> Bookings</a>
                <a href="spa.php"><i class="fas fa-spa mr-2"></i> Spa Services</a>
                <a href="messages.php"><i class="fas fa-envelope mr-2"></i> Messages</a>
                <hr>
                <a href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </div>

            <!-- Main content -->
            <div class="col-md-10 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Rooms</h2>
                    <a href="room_add.php" class="btn btn-primary"><i class="fas fa-plus mr-2"></i> Add New Room</a>
                </div>

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

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($rooms)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No rooms found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($rooms as $room): ?>
                                            <tr>
                                                <td><?php echo $room['id']; ?></td>
                                                <td>
                                                    <?php if (!empty($room['image']) && file_exists("../images/" . $room['image'])): ?>
                                                        <img src="../images/<?php echo $room['image']; ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="room-image">
                                                    <?php else: ?>
                                                        <img src="../images/room_placeholder.jpg" alt="No image" class="room-image">
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($room['name']); ?></td>
                                                <td>$<?php echo number_format($room['price'], 2); ?></td>
                                                <td><?php echo $room['capacity']; ?> guests</td>
                                                <td>
                                                    <?php if ($room['is_available']): ?>
                                                        <span class="badge badge-success">Available</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Unavailable</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="room_edit.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-info mr-1">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="rooms.php?toggle=<?php echo $room['id']; ?>" class="btn btn-sm btn-warning mr-1" onclick="return confirm('Are you sure you want to toggle availability?')">
                                                        <i class="fas fa-toggle-on"></i> Toggle
                                                    </a>
                                                    <a href="rooms.php?delete=<?php echo $room['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this room?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>