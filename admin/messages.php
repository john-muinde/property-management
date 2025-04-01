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

// Mark message as read
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $message_id = $_GET['read'];
    $sql = "UPDATE contact_submissions SET is_read = 1 WHERE id = $message_id";
    mysqli_query($conn, $sql);

    // Redirect to remove the query string
    header('Location: messages.php');
    exit;
}

// Delete message
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $message_id = $_GET['delete'];
    $sql = "DELETE FROM contact_submissions WHERE id = $message_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Message deleted successfully";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting message: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
    }

    // Redirect to remove the query string
    header('Location: messages.php');
    exit;
}

// Get filter parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build the query based on filters
$sql = "SELECT * FROM contact_submissions";

if ($filter === 'unread') {
    $sql .= " WHERE is_read = 0";
}

$sql .= " ORDER BY created_at DESC";

// Get messages
$messages = [];
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
}

// Count unread messages
$unread_count = 0;
$sql = "SELECT COUNT(*) as count FROM contact_submissions WHERE is_read = 0";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $unread_count = $row['count'];
}

// Count total messages
$total_count = 0;
$sql = "SELECT COUNT(*) as count FROM contact_submissions";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $total_count = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Lakeside Resorts</title>
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

        .message-row {
            cursor: pointer;
        }

        .message-row:hover {
            background-color: #f5f5f5;
        }

        .unread {
            font-weight: bold;
            background-color: #f0f7ff;
        }

        .message-preview {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
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
                <a href="spa.php"><i class="fas fa-spa mr-2"></i> Spa Services</a>
                <a href="messages.php" class="active"><i class="fas fa-envelope mr-2"></i> Messages</a>
                <hr>
                <a href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </div>

            <!-- Main content -->
            <div class="col-md-10 content">
                <h2 class="mb-4">Messages</h2>

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

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="messages.php?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-outline-primary'; ?> mr-2">
                                            All Messages <span class="badge badge-light"><?php echo $total_count; ?></span>
                                        </a>
                                        <a href="messages.php?filter=unread" class="btn <?php echo $filter === 'unread' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                            Unread <span class="badge badge-light"><?php echo $unread_count; ?></span>
                                        </a>
                                    </div>
                                    <div>
                                        <?php if ($unread_count > 0): ?>
                                            <a href="#" class="btn btn-success" id="markAllRead">
                                                <i class="fas fa-check-double mr-1"></i> Mark All as Read
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>From</th>
                                        <th>Message</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($messages)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No messages found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($messages as $message): ?>
                                            <tr class="message-row <?php echo $message['is_read'] ? '' : 'unread'; ?>" data-id="<?php echo $message['id']; ?>">
                                                <td><?php echo date('M d, Y - h:i A', strtotime($message['created_at'])); ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($message['name']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($message['email']); ?></small><br>
                                                    <?php if (!empty($message['phone'])): ?>
                                                        <small><?php echo htmlspecialchars($message['phone']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="message-preview"><?php echo htmlspecialchars(substr($message['message'], 0, 100)) . (strlen($message['message']) > 100 ? '...' : ''); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info view-message" data-id="<?php echo $message['id']; ?>" data-toggle="modal" data-target="#messageModal">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>

                                                    <?php if (!$message['is_read']): ?>
                                                        <a href="messages.php?read=<?php echo $message['id']; ?>" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i> Mark Read
                                                        </a>
                                                    <?php endif; ?>

                                                    <a href="messages.php?delete=<?php echo $message['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this message?')">
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

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Message Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="messageDetails">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>Loading message details...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" id="replyBtn" class="btn btn-primary">
                        <i class="fas fa-reply"></i> Reply by Email
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // View message details
            $('.view-message').on('click', function() {
                var messageId = $(this).data('id');
                var messages = <?php echo json_encode($messages); ?>;

                // Find the message with matching ID
                var message = messages.find(function(m) {
                    return m.id == messageId;
                });

                if (message) {
                    var date = new Date(message.created_at).toLocaleString();

                    var html = `
                        <div class="mb-4">
                            <h6>From</h6>
                            <p>${message.name}</p>
                        </div>
                        <div class="mb-4">
                            <h6>Email</h6>
                            <p><a href="mailto:${message.email}">${message.email}</a></p>
                        </div>
                    `;

                    if (message.phone) {
                        html += `
                            <div class="mb-4">
                                <h6>Phone</h6>
                                <p>${message.phone}</p>
                            </div>
                        `;
                    }

                    html += `
                        <div class="mb-4">
                            <h6>Date</h6>
                            <p>${date}</p>
                        </div>
                        <div class="mb-4">
                            <h6>Message</h6>
                            <p>${message.message}</p>
                        </div>
                    `;

                    $('#messageDetails').html(html);
                    $('#replyBtn').attr('href', 'mailto:' + message.email + '?subject=Re: Your Message to Lakeside Resorts');

                    // Mark message as read
                    if (!message.is_read) {
                        $.get('messages.php?read=' + messageId, function() {
                            // Update UI to show message as read
                            $('tr[data-id="' + messageId + '"]').removeClass('unread');
                        });
                    }
                }
            });

            // Mark all messages as read
            $('#markAllRead').on('click', function(e) {
                e.preventDefault();

                if (confirm('Are you sure you want to mark all messages as read?')) {
                    $.ajax({
                        url: 'mark_all_read.php',
                        method: 'POST',
                        success: function() {
                            // Refresh the page
                            window.location.reload();
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>