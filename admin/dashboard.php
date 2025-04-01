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

// Initialize stats array with defaults
$stats = [
    'rooms' => 0,
    'bookings' => 0,
    'services' => 0,
    'messages' => 0
];

// Get counts for dashboard (with error handling)
// Count rooms
$result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM rooms");
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $stats['rooms'] = $row['count'];
}

// Count bookings
$result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM room_bookings");
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $stats['bookings'] = $row['count'];
}

// Count spa services
$result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM spa_services");
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $stats['services'] = $row['count'];
}

// Count contact submissions
$result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM contact_submissions WHERE is_read = 0");
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $stats['messages'] = $row['count'];
}

// Get recent bookings
$recent_bookings = [];
$sql = "SELECT rb.*, r.name as room_name 
       FROM room_bookings rb
       LEFT JOIN rooms r ON rb.room_id = r.id
       ORDER BY rb.created_at DESC LIMIT 5";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_bookings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Lakeside Resorts</title>
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

        .stats-card {
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: white;
        }

        .bg-primary {
            background-color: #007bff;
        }

        .bg-success {
            background-color: #28a745;
        }

        .bg-info {
            background-color: #17a2b8;
        }

        .bg-warning {
            background-color: #ffc107;
            color: #212529;
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
                <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
                <a href="users.php"><i class="fas fa-users mr-2"></i> Users</a>
                <a href="rooms.php"><i class="fas fa-bed mr-2"></i> Rooms</a>
                <a href="bookings.php"><i class="fas fa-calendar-alt mr-2"></i> Bookings</a>
                <a href="spa.php"><i class="fas fa-spa mr-2"></i> Spa Services</a>
                <a href="messages.php"><i class="fas fa-envelope mr-2"></i> Messages</a>
                <hr>
                <a href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </div>

            <!-- Main content -->
            <div class="col-md-10 content">
                <h2 class="mb-4">Dashboard</h2>

                <!-- Stats cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-card bg-primary">
                            <h5>Rooms</h5>
                            <h2><?php echo $stats['rooms']; ?></h2>
                            <p>Total rooms available</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-success">
                            <h5>Bookings</h5>
                            <h2><?php echo $stats['bookings']; ?></h2>
                            <p>Total room bookings</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-info">
                            <h5>Spa Services</h5>
                            <h2><?php echo $stats['services']; ?></h2>
                            <p>Available treatments</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-warning">
                            <h5>Messages</h5>
                            <h2><?php echo $stats['messages']; ?></h2>
                            <p>Unread messages</p>
                        </div>
                    </div>
                </div>

                <!-- Recent bookings -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Recent Bookings</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_bookings)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No bookings found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_bookings as $booking): ?>
                                        <tr>
                                            <td><?php echo $booking['id']; ?></td>
                                            <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['room_name'] ?? 'Unknown Room'); ?></td>
                                            <td><?php echo $booking['arrival_date']; ?></td>
                                            <td><?php echo $booking['departure_date']; ?></td>
                                            <td>
                                                <?php
                                                $status = isset($booking['status']) ? $booking['status'] : 'pending';
                                                $badgeClass = 'badge-warning';

                                                if ($status === 'confirmed') {
                                                    $badgeClass = 'badge-success';
                                                } elseif ($status === 'cancelled') {
                                                    $badgeClass = 'badge-danger';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <a href="bookings.php" class="btn btn-primary btn-sm">View All Bookings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>