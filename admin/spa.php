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

// Delete service if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $service_id = $_GET['delete'];
    $sql = "DELETE FROM spa_services WHERE id = $service_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Service deleted successfully";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting service: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
    }

    // Redirect to remove the query string
    header('Location: spa.php');
    exit;
}

// Toggle availability if requested
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $service_id = $_GET['toggle'];

    // Get current availability status
    $check_sql = "SELECT is_available FROM spa_services WHERE id = $service_id";
    $result = mysqli_query($conn, $check_sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $current_status = $row['is_available'];

        // Toggle status (1 to 0 or 0 to 1)
        $new_status = $current_status ? 0 : 1;

        // Update status
        $update_sql = "UPDATE spa_services SET is_available = $new_status WHERE id = $service_id";

        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['message'] = "Service availability updated";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating availability: " . mysqli_error($conn);
            $_SESSION['message_type'] = "danger";
        }
    }

    // Redirect to remove the query string
    header('Location: spa.php');
    exit;
}

// Get all spa services
$services = [];
$sql = "SELECT * FROM spa_services ORDER BY id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $services[] = $row;
    }
}

// Get all spa bookings
$bookings = [];
$sql = "SELECT sb.*, ss.name as service_name 
        FROM spa_bookings sb
        LEFT JOIN spa_services ss ON sb.service_id = ss.id
        ORDER BY sb.booking_date DESC
        LIMIT 10";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Spa Services - Lakeside Resorts</title>
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

        .service-image {
            width: 100px;
            height: 75px;
            object-fit: cover;
        }

        .nav-tabs .nav-link {
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
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
                    <h2>Manage Spa Services</h2>
                    <a href="spa_add.php" class="btn btn-primary"><i class="fas fa-plus mr-2"></i> Add New Service</a>
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

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="spaTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="services-tab" data-toggle="tab" href="#services" role="tab" aria-controls="services" aria-selected="true">
                            <i class="fas fa-list mr-2"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="bookings-tab" data-toggle="tab" href="#bookings" role="tab" aria-controls="bookings" aria-selected="false">
                            <i class="fas fa-calendar-check mr-2"></i> Recent Bookings
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="spaTabContent">
                    <!-- Services Tab -->
                    <div class="tab-pane fade show active" id="services" role="tabpanel" aria-labelledby="services-tab">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Duration</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($services)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No spa services found</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($services as $service): ?>
                                                    <tr>
                                                        <td><?php echo $service['id']; ?></td>
                                                        <td>
                                                            <?php if (!empty($service['image']) && file_exists("../images/" . $service['image'])): ?>
                                                                <img src="../images/<?php echo $service['image']; ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" class="service-image">
                                                            <?php else: ?>
                                                                <img src="../images/spa_placeholder.jpg" alt="No image" class="service-image">
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($service['name']); ?></td>
                                                        <td><?php echo $service['duration']; ?> minutes</td>
                                                        <td>$<?php echo number_format($service['price'], 2); ?></td>
                                                        <td>
                                                            <?php if ($service['is_available']): ?>
                                                                <span class="badge badge-success">Available</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger">Unavailable</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <a href="spa_edit.php?id=<?php echo $service['id']; ?>" class="btn btn-sm btn-info mr-1">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                            <a href="spa.php?toggle=<?php echo $service['id']; ?>" class="btn btn-sm btn-warning mr-1" onclick="return confirm('Are you sure you want to toggle availability?')">
                                                                <i class="fas fa-toggle-on"></i> Toggle
                                                            </a>
                                                            <a href="spa.php?delete=<?php echo $service['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">
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

                    <!-- Bookings Tab -->
                    <div class="tab-pane fade" id="bookings" role="tabpanel" aria-labelledby="bookings-tab">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Guest</th>
                                                <th>Service</th>
                                                <th>Date & Time</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($bookings)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No spa bookings found</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($bookings as $booking): ?>
                                                    <tr>
                                                        <td><?php echo $booking['id']; ?></td>
                                                        <td>
                                                            <?php echo htmlspecialchars($booking['guest_name']); ?><br>
                                                            <small><?php echo htmlspecialchars($booking['email']); ?></small><br>
                                                            <small><?php echo htmlspecialchars($booking['phone']); ?></small>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($booking['service_name'] ?? 'Unknown Service'); ?></td>
                                                        <td>
                                                            <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?><br>
                                                            <?php echo date('h:i A', strtotime($booking['booking_time'])); ?>
                                                        </td>
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
                                                        <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                                                                    Actions
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#viewSpaBookingModal" data-id="<?php echo $booking['id']; ?>">
                                                                        <i class="fas fa-eye"></i> View Details
                                                                    </a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <h6 class="dropdown-header">Change Status</h6>
                                                                    <a class="dropdown-item" href="spa_booking_status.php?id=<?php echo $booking['id']; ?>&status=pending">
                                                                        <i class="fas fa-clock"></i> Set as Pending
                                                                    </a>
                                                                    <a class="dropdown-item" href="spa_booking_status.php?id=<?php echo $booking['id']; ?>&status=confirmed">
                                                                        <i class="fas fa-check"></i> Set as Confirmed
                                                                    </a>
                                                                    <a class="dropdown-item" href="spa_booking_status.php?id=<?php echo $booking['id']; ?>&status=cancelled">
                                                                        <i class="fas fa-times"></i> Set as Cancelled
                                                                    </a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item text-danger" href="spa_booking_delete.php?id=<?php echo $booking['id']; ?>" onclick="return confirm('Are you sure you want to delete this booking?')">
                                                                        <i class="fas fa-trash"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="spa_bookings.php" class="btn btn-primary mt-3">View All Spa Bookings</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Spa Booking Details Modal -->
    <div class="modal fade" id="viewSpaBookingModal" tabindex="-1" role="dialog" aria-labelledby="viewSpaBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewSpaBookingModalLabel">Spa Booking Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="spaBookingDetailsContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Loading booking details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
        // View spa booking details in modal
        $('#viewSpaBookingModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var bookingId = button.data('id');
            var modal = $(this);

            // Here you would typically fetch booking details via AJAX
            // For simplicity, we'll just display static info from the already loaded bookings
            var bookingDetails = <?php echo json_encode($bookings); ?>;

            var booking = bookingDetails.find(function(b) {
                return b.id == bookingId;
            });

            if (booking) {
                var statusBadge = '';

                if (booking.status === 'confirmed') {
                    statusBadge = '<span class="badge badge-success">Confirmed</span>';
                } else if (booking.status === 'cancelled') {
                    statusBadge = '<span class="badge badge-danger">Cancelled</span>';
                } else {
                    statusBadge = '<span class="badge badge-warning">Pending</span>';
                }

                var content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Guest Information</h6>
                            <p><strong>Name:</strong> ${booking.guest_name}</p>
                            <p><strong>Email:</strong> ${booking.email}</p>
                            <p><strong>Phone:</strong> ${booking.phone}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Booking Information</h6>
                            <p><strong>Service:</strong> ${booking.service_name || 'Unknown Service'}</p>
                            <p><strong>Status:</strong> ${statusBadge}</p>
                            <p><strong>Created:</strong> ${new Date(booking.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Appointment</h6>
                            <p><strong>Date:</strong> ${new Date(booking.booking_date).toLocaleDateString()}</p>
                            <p><strong>Time:</strong> ${booking.booking_time}</p>
                        </div>
                    </div>
                `;

                // Add special requests if any
                if (booking.requests) {
                    content += `
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Special Requests</h6>
                                <p>${booking.requests}</p>
                            </div>
                        </div>
                    `;
                }

                $('#spaBookingDetailsContent').html(content);
            } else {
                $('#spaBookingDetailsContent').html('<div class="alert alert-warning">Booking details not found.</div>');
            }
        });
    </script>
</body>

</html>