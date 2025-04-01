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

// Update booking status if requested
if (isset($_GET['id']) && isset($_GET['status']) && in_array($_GET['status'], ['pending', 'confirmed', 'cancelled'])) {
    $booking_id = $_GET['id'];
    $new_status = $_GET['status'];

    $sql = "UPDATE room_bookings SET status = '$new_status' WHERE id = $booking_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Booking status updated to " . ucfirst($new_status);
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating booking status: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
    }

    // Redirect to remove the query string
    header('Location: bookings.php');
    exit;
}

// Delete booking if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $booking_id = $_GET['delete'];
    $sql = "DELETE FROM room_bookings WHERE id = $booking_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Booking deleted successfully";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting booking: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
    }

    // Redirect to remove the query string
    header('Location: bookings.php');
    exit;
}

// Get filter parameters
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';

// Build the query based on filters
$sql = "SELECT rb.*, r.name as room_name 
        FROM room_bookings rb
        LEFT JOIN rooms r ON rb.room_id = r.id";

$where_clauses = [];

if (!empty($status_filter)) {
    $where_clauses[] = "rb.status = '$status_filter'";
}

if (!empty($date_filter)) {
    $date = date('Y-m-d', strtotime($date_filter));
    $where_clauses[] = "(rb.arrival_date <= '$date' AND rb.departure_date >= '$date')";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql .= " ORDER BY rb.arrival_date DESC";

// Get all bookings
$bookings = [];
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
    <title>Manage Bookings - Lakeside Resorts</title>
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
                <a href="users.php"><i class="fas fa-users mr-2"></i> Users</a>
                <a href="rooms.php"><i class="fas fa-bed mr-2"></i> Rooms</a>
                <a href="bookings.php" class="active"><i class="fas fa-calendar-alt mr-2"></i> Bookings</a>
                <a href="spa.php"><i class="fas fa-spa mr-2"></i> Spa Services</a>
                <a href="messages.php"><i class="fas fa-envelope mr-2"></i> Messages</a>
                <hr>
                <a href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </div>

            <!-- Main content -->
            <div class="col-md-10 content">
                <h2 class="mb-4">Manage Bookings</h2>

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

                <!-- Filter options -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="bookings.php" method="get" class="form-inline">
                            <div class="form-group mr-3">
                                <label for="status_filter" class="mr-2">Status:</label>
                                <select class="form-control" id="status_filter" name="status_filter">
                                    <option value="">All</option>
                                    <option value="pending" <?php echo ($status_filter === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo ($status_filter === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="cancelled" <?php echo ($status_filter === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>

                            <div class="form-group mr-3">
                                <label for="date_filter" class="mr-2">Date:</label>
                                <input type="date" class="form-control" id="date_filter" name="date_filter" value="<?php echo $date_filter; ?>">
                            </div>

                            <button type="submit" class="btn btn-primary">Filter</button>

                            <?php if (!empty($status_filter) || !empty($date_filter)): ?>
                                <a href="bookings.php" class="btn btn-secondary ml-2">Clear Filters</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Guest</th>
                                        <th>Room</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Guests</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($bookings)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No bookings found</td>
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
                                                <td><?php echo htmlspecialchars($booking['room_name'] ?? 'Unknown Room'); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($booking['arrival_date'])); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($booking['departure_date'])); ?></td>
                                                <td>
                                                    <?php echo $booking['adults']; ?> adults<br>
                                                    <?php echo $booking['children']; ?> children
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
                                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#viewBookingModal" data-id="<?php echo $booking['id']; ?>">
                                                                <i class="fas fa-eye"></i> View Details
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <h6 class="dropdown-header">Change Status</h6>
                                                            <a class="dropdown-item" href="bookings.php?id=<?php echo $booking['id']; ?>&status=pending">
                                                                <i class="fas fa-clock"></i> Set as Pending
                                                            </a>
                                                            <a class="dropdown-item" href="bookings.php?id=<?php echo $booking['id']; ?>&status=confirmed">
                                                                <i class="fas fa-check"></i> Set as Confirmed
                                                            </a>
                                                            <a class="dropdown-item" href="bookings.php?id=<?php echo $booking['id']; ?>&status=cancelled">
                                                                <i class="fas fa-times"></i> Set as Cancelled
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item text-danger" href="bookings.php?delete=<?php echo $booking['id']; ?>" onclick="return confirm('Are you sure you want to delete this booking?')">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal fade" id="viewBookingModal" tabindex="-1" role="dialog" aria-labelledby="viewBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewBookingModalLabel">Booking Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="bookingDetailsContent">
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
        // View booking details in modal
        $('#viewBookingModal').on('show.bs.modal', function(event) {
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

                var nights = Math.ceil((new Date(booking.departure_date) - new Date(booking.arrival_date)) / (1000 * 60 * 60 * 24));

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
                            <p><strong>Room:</strong> ${booking.room_name || 'Unknown Room'}</p>
                            <p><strong>Status:</strong> ${statusBadge}</p>
                            <p><strong>Created:</strong> ${new Date(booking.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Stay Information</h6>
                            <p><strong>Check-in:</strong> ${new Date(booking.arrival_date).toLocaleDateString()}</p>
                            <p><strong>Check-out:</strong> ${new Date(booking.departure_date).toLocaleDateString()}</p>
                            <p><strong>Length of Stay:</strong> ${nights} night(s)</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Guest Count</h6>
                            <p><strong>Adults:</strong> ${booking.adults}</p>
                            <p><strong>Children:</strong> ${booking.children}</p>
                        </div>
                    </div>
                `;

                // Add special requests if any
                if (booking.special_requests) {
                    content += `
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Special Requests</h6>
                                <p>${booking.special_requests}</p>
                            </div>
                        </div>
                    `;
                }

                $('#bookingDetailsContent').html(content);
            } else {
                $('#bookingDetailsContent').html('<div class="alert alert-warning">Booking details not found.</div>');
            }
        });
    </script>
</body>

</html>