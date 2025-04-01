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
                <!-- Alert for required fields explanation -->
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Fields marked with <span class="text-danger">*</span> are required.
                </div>

                <form id="spa_booking_form" action="api/book_spa.php" method="post" novalidate onsubmit="return validateSpaForm();">
                    <input type="hidden" name="token" value="<?php echo generate_token(); ?>">
                    <input type="hidden" name="service_id" id="service_id">

                    <div class="form-group">
                        <label>Treatment:</label>
                        <input type="text" class="form-control" id="service_name" name="service_name" readonly>
                    </div>

                    <div class="form-group">
                        <label class="required">Full Name:</label>
                        <input type="text" class="form-control" name="guest_name" id="spa_guest_name" required
                            placeholder="Enter your full name">
                        <div class="invalid-feedback" id="spa_guest_name_error"></div>
                    </div>

                    <div class="form-group">
                        <label class="required">Email:</label>
                        <input type="email" class="form-control" name="email" id="spa_email" required
                            placeholder="Enter your email address">
                        <div class="invalid-feedback" id="spa_email_error"></div>
                    </div>

                    <div class="form-group">
                        <label class="required">Phone:</label>
                        <input type="text" class="form-control" name="phone" id="spa_phone" required
                            placeholder="Enter your phone number">
                        <div class="invalid-feedback" id="spa_phone_error"></div>
                    </div>

                    <div class="form-group">
                        <label class="required">Date:</label>
                        <input type="date" class="form-control" name="date" id="spa_date" required
                            min="<?php echo date('Y-m-d'); ?>">
                        <div class="invalid-feedback" id="spa_date_error"></div>
                    </div>

                    <div class="form-group">
                        <label class="required">Time:</label>
                        <select class="form-control" name="time" id="spa_time" required>
                            <option value="">Select a time</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="13:00">1:00 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                        </select>
                        <div class="invalid-feedback" id="spa_time_error"></div>
                    </div>

                    <div class="form-group">
                        <label>Special Requests:</label>
                        <textarea class="form-control" name="requests" id="spa_requests" rows="3"
                            placeholder="Enter any special requests or requirements"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submit_spa_booking">Book Now</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this CSS to spa_services.php in the head section -->
<style>
    /* Styles for form validation */
    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: .25rem;
        font-size: 80%;
        color: #dc3545;
    }

    .is-invalid {
        border-color: #dc3545 !important;
        padding-right: calc(1.5em + 0.75rem) !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right calc(0.375em + 0.1875rem) center !important;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
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
</style>


<script>
    // Function to prevent automatic form submission
    function validateSpaForm() {
        // Always return false - we'll submit manually if validation passes
        return false;
    }

    // Helper functions
    function resetSpaValidation() {
        // Remove all validation styling
        const formFields = document.querySelectorAll('#spa_booking_form input, #spa_booking_form select, #spa_booking_form textarea');
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

    function validateSpaRequired(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return true; // Skip if field doesn't exist

        if (!field.value.trim()) {
            showSpaError(field, message);
            return false;
        }
        return true;
    }

    function showSpaError(field, message) {
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

    // Function to book a spa service
    function bookService(id, name) {
        document.getElementById('service_id').value = id;
        document.getElementById('service_name').value = name;

        // Reset any previous validation styling
        resetSpaValidation();

        $('#serviceModal').modal('show');
        return false;
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - setting up spa booking handlers');

        // Set up event handler for spa booking submission
        const submitButton = document.getElementById('submit_spa_booking');
        if (submitButton) {
            submitButton.addEventListener('click', function(e) {
                console.log('Spa booking submit button clicked');
                e.preventDefault();

                // Start with clean slate
                resetSpaValidation();

                const form = document.getElementById('spa_booking_form');
                let hasErrors = false;

                // Validate required fields with specific error messages for each
                if (!validateSpaRequired('spa_guest_name', 'Please enter your full name')) hasErrors = true;
                if (!validateSpaRequired('spa_email', 'Please enter your email address')) hasErrors = true;
                if (!validateSpaRequired('spa_phone', 'Please enter your phone number')) hasErrors = true;
                if (!validateSpaRequired('spa_date', 'Please select a date')) hasErrors = true;
                if (!validateSpaRequired('spa_time', 'Please select a time')) hasErrors = true;

                // Validate email format
                const emailField = document.getElementById('spa_email');
                if (emailField.value.trim() && !isValidEmail(emailField.value.trim())) {
                    hasErrors = true;
                    showSpaError(emailField, 'Please enter a valid email address');
                }

                // Validate date (must be in the future)
                const dateField = document.getElementById('spa_date');
                if (dateField.value) {
                    const selectedDate = new Date(dateField.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0); // Reset time component for comparison

                    if (selectedDate < today) {
                        hasErrors = true;
                        showSpaError(dateField, 'Appointment date cannot be in the past');
                    }
                }

                console.log('Spa form validation complete. Has errors: ' + hasErrors);

                // If no errors, submit the form
                if (!hasErrors) {
                    console.log('Spa form is valid, submitting...');
                    form.submit();
                } else {
                    console.log('Spa form has errors, not submitting');
                    // Focus the first error field
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.focus();
                    }
                    return false;
                }
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>