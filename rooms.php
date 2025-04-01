<?php
require_once 'includes/connect.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Get all rooms from database
$rooms = get_rooms();
?>

<style>
    /* Add styles for error handling and form validation */
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: .25rem;
        font-size: 80%;
        color: #dc3545;
    }

    /* Make required fields more obvious */
    label.required::after {
        content: ' *';
        color: #dc3545;
    }

    /* Add a bit of spacing in the form groups */
    .form-group {
        margin-bottom: 1rem;
    }

    /* Styles for available rooms after availability check */
    .available-room {
        border: 2px solid #28a745;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
    }

    /* Highlight the booking form section */
    .highlight-section {
        animation: highlight 2s ease-in-out;
    }

    @keyframes highlight {
        0% {
            background-color: rgba(255, 193, 7, 0.2);
        }

        100% {
            background-color: transparent;
        }
    }
</style>

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

<?php
// Display specific errors if any exist
if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
    echo '<div class="container mt-3"><div class="alert alert-danger">';
    echo '<strong>Please fix these errors:</strong>';
    echo '<ul>';
    foreach ($_SESSION['errors'] as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul></div></div>';
    // Clear the errors after displaying
    unset($_SESSION['errors']);
}
?>

<!-- Room Booking Form -->
<div class="booking_section">
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="booking_form">
                    <h3>Check Availability</h3>
                    <form action="api/check_availability.php" method="post" id="availability_form" onsubmit="return checkAvailability();">
                        <input type="hidden" name="token" value="<?php echo generate_token(); ?>">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="arrival_date" class="required">Arrival Date</label>
                                    <input type="date" id="arrival_date" name="arrival_date" class="form-control" required
                                        min="<?php echo date('Y-m-d'); ?>"
                                        value="<?php echo isset($_SESSION['check_arrival']) ? $_SESSION['check_arrival'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="departure_date" class="required">Departure Date</label>
                                    <input type="date" id="departure_date" name="departure_date" class="form-control" required
                                        min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                        value="<?php echo isset($_SESSION['check_departure']) ? $_SESSION['check_departure'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="adults">Guests</label>
                                    <select id="adults" name="adults" class="form-control" required>
                                        <option value="1" <?php echo (isset($_SESSION['check_adults']) && $_SESSION['check_adults'] == 1) ? 'selected' : ''; ?>>1</option>
                                        <option value="2" <?php echo (!isset($_SESSION['check_adults']) || $_SESSION['check_adults'] == 2) ? 'selected' : ''; ?>>2</option>
                                        <option value="3" <?php echo (isset($_SESSION['check_adults']) && $_SESSION['check_adults'] == 3) ? 'selected' : ''; ?>>3</option>
                                        <option value="4" <?php echo (isset($_SESSION['check_adults']) && $_SESSION['check_adults'] == 4) ? 'selected' : ''; ?>>4</option>
                                        <option value="5" <?php echo (isset($_SESSION['check_adults']) && $_SESSION['check_adults'] == 5) ? 'selected' : ''; ?>>5+</option>
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
            <?php
            // Get available room IDs from session if set
            $available_rooms = isset($_SESSION['available_rooms']) ? $_SESSION['available_rooms'] : [];

            foreach ($rooms as $room):
                $isAvailable = empty($available_rooms) || in_array($room['id'], $available_rooms);
                $roomClass = $isAvailable ? ($available_rooms ? 'available-room' : '') : 'opacity-50';
            ?>
                <div class="col-md-6 mb-4">
                    <div id="room-<?php echo $room['id']; ?>" class="room detailed_room <?php echo $roomClass; ?>">
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
                                    <?php if ($isAvailable): ?>
                                        <button class="read_more book_room_btn" data-room-id="<?php echo $room['id']; ?>" data-room-name="<?php echo htmlspecialchars($room['name']); ?>">Book Now</button>
                                    <?php else: ?>
                                        <button class="read_more" disabled>Not Available</button>
                                    <?php endif; ?>
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
                <!-- Alert for required fields explanation -->
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Fields marked with <span class="text-danger">*</span> are required.
                </div>

                <!-- Use novalidate to disable browser's native validation and handle it completely with JS -->
                <form id="room_booking_form" action="api/book_room.php" method="post" novalidate onsubmit="return validateBookingForm();">
                    <input type="hidden" name="token" value="<?php echo generate_token(); ?>">
                    <input type="hidden" name="room_id" id="room_id" value="">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="guest_name" class="required">Full Name</label>
                                <input type="text" class="form-control" id="guest_name" name="guest_name" required
                                    placeholder="Enter your full name">
                                <div class="invalid-feedback" id="guest_name_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="required">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="Enter your email address">
                                <div class="invalid-feedback" id="email_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="required">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" required
                                    placeholder="Enter your phone number">
                                <div class="invalid-feedback" id="phone_error"></div>
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
                                <label for="modal_arrival_date" class="required">Arrival Date</label>
                                <input type="date" class="form-control" id="modal_arrival_date" name="arrival_date" required
                                    min="<?php echo date('Y-m-d'); ?>">
                                <small class="form-text text-muted">Select your check-in date</small>
                                <div class="invalid-feedback" id="arrival_date_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_departure_date" class="required">Departure Date</label>
                                <input type="date" class="form-control" id="modal_departure_date" name="departure_date" required
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                <small class="form-text text-muted">Select your check-out date</small>
                                <div class="invalid-feedback" id="departure_date_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_adults" class="required">Adults</label>
                                <select class="form-control" id="modal_adults" name="adults" required>
                                    <option value="1">1</option>
                                    <option value="2" selected>2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                                <div class="invalid-feedback" id="adults_error"></div>
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
                                <textarea class="form-control" id="special_requests" name="special_requests" rows="3"
                                    placeholder="Enter any special requests or requirements"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <!-- This is now explicitly type="button" to prevent auto-submission -->
                <button type="button" class="btn btn-primary" id="submit_booking">Complete Booking</button>
            </div>
        </div>
    </div>
</div>
<!-- End Booking Modal -->

<script>
    // Function to check availability before opening booking modal
    function checkAvailability() {
        const arrivalDate = document.getElementById('arrival_date').value;
        const departureDate = document.getElementById('departure_date').value;

        if (!arrivalDate || !departureDate) {
            alert("Please select both arrival and departure dates");
            return false;
        }

        if (new Date(departureDate) <= new Date(arrivalDate)) {
            alert("Departure date must be after arrival date");
            return false;
        }

        return true;
    }

    // Main form validation function
    function validateBookingForm() {
        // Always return false here - we'll submit the form manually if validation passes
        return false;
    }

    // Book Now buttons and modal handling
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - setting up event handlers');

        // Fix modal date fields to be type="date"
        const modalArrivalDate = document.getElementById('modal_arrival_date');
        const modalDepartureDate = document.getElementById('modal_departure_date');

        if (modalArrivalDate) {
            modalArrivalDate.type = 'date';
            modalArrivalDate.min = new Date().toISOString().split('T')[0];
        }

        if (modalDepartureDate) {
            modalDepartureDate.type = 'date';
            modalDepartureDate.min = new Date().toISOString().split('T')[0];
        }

        // Populate dates from session if available
        <?php if (isset($_SESSION['check_arrival'])): ?>
            document.getElementById('arrival_date').value = '<?php echo $_SESSION['check_arrival']; ?>';
            if (modalArrivalDate) {
                modalArrivalDate.value = '<?php echo $_SESSION['check_arrival']; ?>';
            }
        <?php endif; ?>

        <?php if (isset($_SESSION['check_departure'])): ?>
            document.getElementById('departure_date').value = '<?php echo $_SESSION['check_departure']; ?>';
            if (modalDepartureDate) {
                modalDepartureDate.value = '<?php echo $_SESSION['check_departure']; ?>';
            }
        <?php endif; ?>

        <?php if (isset($_SESSION['check_adults'])): ?>
            document.getElementById('adults').value = '<?php echo $_SESSION['check_adults']; ?>';
            if (document.getElementById('modal_adults')) {
                document.getElementById('modal_adults').value = '<?php echo $_SESSION['check_adults']; ?>';
            }
        <?php endif; ?>

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

                // Reset any previous validation styling
                resetValidation();

                $('#bookingModal').modal('show');
            });
        });

        // Improved validation function for the booking form
        document.getElementById('submit_booking').addEventListener('click', function(e) {
            console.log('Submit button clicked');
            e.preventDefault();

            // Start with clean slate
            resetValidation();

            const form = document.getElementById('room_booking_form');
            let hasErrors = false;

            // Validate required fields with specific error messages for each
            if (!validateRequired('guest_name', 'Please enter your full name')) hasErrors = true;
            if (!validateRequired('email', 'Please enter your email address')) hasErrors = true;
            if (!validateRequired('phone', 'Please enter your phone number')) hasErrors = true;
            if (!validateRequired('modal_arrival_date', 'Please select an arrival date')) hasErrors = true;
            if (!validateRequired('modal_departure_date', 'Please select a departure date')) hasErrors = true;

            // Validate email format
            const emailField = document.getElementById('email');
            if (emailField.value.trim() && !isValidEmail(emailField.value.trim())) {
                hasErrors = true;
                showError(emailField, 'Please enter a valid email address');
            }

            // Validate date range
            const arrivalDate = new Date(document.getElementById('modal_arrival_date').value);
            const departureDate = new Date(document.getElementById('modal_departure_date').value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (!isNaN(arrivalDate.getTime()) && arrivalDate < today) {
                hasErrors = true;
                showError(document.getElementById('modal_arrival_date'), 'Arrival date cannot be in the past');
            }

            if (!isNaN(arrivalDate.getTime()) && !isNaN(departureDate.getTime()) && departureDate <= arrivalDate) {
                hasErrors = true;
                showError(document.getElementById('modal_departure_date'), 'Departure date must be after arrival date');
            }

            console.log('Validation complete. Has errors: ' + hasErrors);

            // If no errors, submit the form
            if (!hasErrors) {
                console.log('Form is valid, submitting...');
                form.submit();
            } else {
                console.log('Form has errors, not submitting');
                // Focus the first error field
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.focus();
                }
                return false;
            }
        });

        // Helper functions
        function resetValidation() {
            // Remove all validation styling
            const formFields = document.querySelectorAll('#room_booking_form input, #room_booking_form select, #room_booking_form textarea');
            formFields.forEach(field => {
                field.classList.remove('is-invalid');

                // Clear error messages
                const errorId = field.id + '_error';
                const errorElement = document.getElementById(errorId);
                if (errorElement) {
                    errorElement.textContent = '';
                }
            });
        }

        function validateRequired(fieldId, message) {
            const field = document.getElementById(fieldId);
            if (!field) return true; // Skip if field doesn't exist

            if (!field.value.trim()) {
                showError(field, message);
                return false;
            }
            return true;
        }

        function showError(field, message) {
            field.classList.add('is-invalid');

            // Show error in dedicated error element if it exists
            const errorId = field.id + '_error';
            const errorElement = document.getElementById(errorId);

            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
        }

        function isValidEmail(email) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }

        // Highlight section if coming from availability check
        if (window.location.search.includes('check=availability')) {
            document.querySelector('.our_room').classList.add('highlight-section');
        }
    });
</script>

<?php
// Clear availability data after displaying
if (isset($_SESSION['available_rooms'])) {
    unset($_SESSION['available_rooms']);
}

include 'includes/footer.php';
?>