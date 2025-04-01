<?php
require_once 'includes/connect.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Get all rooms from database
$rooms = get_rooms();
?>

<div class="back_re">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h2>Our Accommodations</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Room Booking Form -->
<div class="booking_section">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="booking_form">
                    <h3>Check Availability</h3>
                    <form action="#" method="post" id="availability_form" onsubmit="return checkAvailability();">
                        <input type="hidden" name="token" value="<?php echo generate_token(); ?>">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="arrival_date">Arrival Date</label>
                                    <input type="date" id="arrival_date" name="arrival_date" class="form-control" required placeholder="Select arrival date">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="departure_date">Departure Date</label>
                                    <input type="date" id="departure_date" name="departure_date" class="form-control" required placeholder="Select departure date">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="adults">Guests</label>
                                    <select id="adults" name="adults" class="form-control" required>
                                        <option value="1">1</option>
                                        <option value="2" selected>2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5+</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 text-center mt-4">
                                <button type="submit" class="read_more">Check Availability</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Room Booking Form -->

<!-- our rooms -->
<div class="our_room">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="titlepage">
                    <h2>Luxurious Accommodations</h2>
                    <p>Discover our selection of luxurious accommodations designed for your comfort</p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($rooms as $room): ?>
                <div class="col-md-6 mb-4">
                    <div id="room-<?php echo $room['id']; ?>" class="room detailed_room">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="room_img">
                                    <figure><img src="images/<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" /></figure>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bed_room">
                                    <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                                    <p><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                                    <div class="room_details">
                                        <p><strong>Price:</strong> <?php echo format_price($room['price']); ?> per night</p>
                                        <p><strong>Capacity:</strong> <?php echo $room['capacity']; ?> guests</p>
                                        <p><strong>Amenities:</strong> <?php echo htmlspecialchars($room['amenities']); ?></p>
                                    </div>
                                    <button class="read_more book_room_btn" data-room-id="<?php echo $room['id']; ?>" data-room-name="<?php echo htmlspecialchars($room['name']); ?>">Book Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- end our rooms -->

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Book a Room</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="room_booking_form" action="api/book_room.php" method="post">
                    <input type="hidden" name="token" value="<?php echo generate_token(); ?>">
                    <input type="hidden" name="room_id" id="room_id" value="">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="guest_name">Full Name</label>
                                <input type="text" class="form-control" id="guest_name" name="guest_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_room_name">Selected Room</label>
                                <input type="text" class="form-control" id="modal_room_name" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_arrival_date">Arrival Date</label>
                                <input type="text" class="form-control" id="modal_arrival_date" name="arrival_date" required placeholder="Select arrival date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_departure_date">Departure Date</label>
                                <input type="text" class="form-control" id="modal_departure_date" name="departure_date" required placeholder="Select departure date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_adults">Adults</label>
                                <select class="form-control" id="modal_adults" name="adults" required>
                                    <option value="1">1</option>
                                    <option value="2" selected>2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_children">Children</label>
                                <select class="form-control" id="modal_children" name="children">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="special_requests">Special Requests</label>
                                <textarea class="form-control" id="special_requests" name="special_requests" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submit_booking">Complete Booking</button>
            </div>
        </div>
    </div>
</div>
<!-- End Booking Modal -->

<script>
    // Book Now buttons
    document.addEventListener('DOMContentLoaded', function() {
        const bookButtons = document.querySelectorAll('.book_room_btn');
        bookButtons.forEach(button => {
            button.addEventListener('click', function() {
                const roomId = this.getAttribute('data-room-id');
                const roomName = this.getAttribute('data-room-name');

                document.getElementById('room_id').value = roomId;
                document.getElementById('modal_room_name').value = roomName;

                // If dates were selected in the availability form, use them
                const arrivalDate = document.getElementById('arrival_date').value;
                const departureDate = document.getElementById('departure_date').value;
                const adults = document.getElementById('adults').value;

                if (arrivalDate) {
                    document.getElementById('modal_arrival_date').value = arrivalDate;
                }
                if (departureDate) {
                    document.getElementById('modal_departure_date').value = departureDate;
                }
                if (adults) {
                    document.getElementById('modal_adults').value = adults;
                }

                $('#bookingModal').modal('show');
            });
        });

        // Submit booking form when the complete booking button is clicked
        document.getElementById('submit_booking').addEventListener('click', function() {
            // Validate form
            const form = document.getElementById('room_booking_form');
            if (form.checkValidity()) {
                form.submit();
            } else {
                // Trigger browser's native validation
                form.reportValidity();
            }
        });

        // Highlight section if coming from availability check
        if (window.location.search.includes('check=availability')) {
            document.querySelector('.our_room').classList.add('highlight-section');
            showNotification('Rooms are available for your selected dates. Choose a room to book.', 'success');
        }
    });
</script>

<?php include 'includes/footer.php'; ?>