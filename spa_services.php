<?php
require_once 'includes/connect.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Get all spa services
$services = get_spa_services();
?>

<div class="back_re">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h2>Spa Services</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- spa services -->
<div class="our_room">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="titlepage">
                    <p>Rejuvenate your body, mind, and spirit with our luxurious spa treatments</p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($services as $service): ?>
                <div class="col-md-6 mb-4">
                    <div id="service-<?php echo $service['id']; ?>" class="room">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="room_img">
                                    <figure><img src="images/<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" /></figure>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bed_room">
                                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                                    <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                                    <div class="service_details">
                                        <p><strong>Duration:</strong> <?php echo $service['duration']; ?> minutes</p>
                                        <p><strong>Price:</strong> <?php echo format_price($service['price']); ?></p>
                                    </div>
                                    <a href="#" class="read_more" onclick="bookService(<?php echo $service['id']; ?>, '<?php echo htmlspecialchars($service['name']); ?>')">Book Treatment</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- end spa services -->

<!-- Booking Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalLabel">Book Spa Treatment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="spa_booking_form" action="api/book_spa.php" method="post">
                    <input type="hidden" name="token" value="<?php echo generate_token(); ?>">
                    <input type="hidden" name="service_id" id="service_id">

                    <div class="form-group">
                        <label>Treatment:</label>
                        <input type="text" class="form-control" id="service_name" readonly>
                    </div>

                    <div class="form-group">
                        <label>Full Name:</label>
                        <input type="text" class="form-control" name="guest_name" required>
                    </div>

                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" class="form-control" name="date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Time:</label>
                        <select class="form-control" name="time" required>
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="13:00">1:00 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Special Requests:</label>
                        <textarea class="form-control" name="requests" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('spa_booking_form').submit();">Book Now</button>
            </div>
        </div>
    </div>
</div>

<script>
    function bookService(id, name) {
        document.getElementById('service_id').value = id;
        document.getElementById('service_name').value = name;
        $('#serviceModal').modal('show');
        return false;
    }
</script>

<?php include 'includes/footer.php'; ?>