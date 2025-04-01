// Datepicker configuration for Lakeside Resort and Spa

$(document).ready(function () {
    // Initialize datepickers
    if ($.fn.datepicker) {
        // Home page booking form
        $('.online_book').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0, // Today or future dates only
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            beforeShow: function (input) {
                setTimeout(function () {
                    var buttonPane = $(input)
                        .datepicker("widget")
                        .find(".ui-datepicker-buttonpane");

                    $("<button>", {
                        text: "Clear",
                        click: function () {
                            $.datepicker._clearDate(input);
                        }
                    }).appendTo(buttonPane).addClass("ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all");
                }, 1);
            }
        });

        // Rooms page datepickers
        $('#arrival_date, #modal_arrival_date').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0,
            changeMonth: true,
            changeYear: true,
            onSelect: function (selectedDate) {
                // When arrival date is selected, set minimum date for departure
                var minDepartureDate = new Date(selectedDate);
                minDepartureDate.setDate(minDepartureDate.getDate() + 1);

                if ($(this).attr('id') === 'arrival_date') {
                    $("#departure_date").datepicker("option", "minDate", minDepartureDate);
                } else {
                    $("#modal_departure_date").datepicker("option", "minDate", minDepartureDate);
                }
            }
        });

        $('#departure_date, #modal_departure_date').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 1, // At least tomorrow
            changeMonth: true,
            changeYear: true
        });

        // Spa booking datepicker
        $('input[name="date"]').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0,
            changeMonth: true,
            changeYear: true
        });
    }

    // Notifications timeout
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 5000);
});

// Check Availability function
function checkAvailability() {
    var arrivalDate = $('#arrival_date').val();
    var departureDate = $('#departure_date').val();

    if (!arrivalDate || !departureDate) {
        showNotification('Please select both arrival and departure dates', 'warning');
        return false;
    }

    // Validate dates
    var arrival = new Date(arrivalDate);
    var departure = new Date(departureDate);
    var today = new Date();
    today.setHours(0, 0, 0, 0);

    if (arrival < today) {
        showNotification('Arrival date cannot be in the past', 'warning');
        return false;
    }

    if (departure <= arrival) {
        showNotification('Departure date must be after arrival date', 'warning');
        return false;
    }

    // Proceed with availability check
    // In a real application, this would send an AJAX request to check availability

    // For demo purposes, show available rooms
    showNotification('Rooms are available for your selected dates. Please choose a room below.', 'success');

    // Scroll to rooms section
    $('html, body').animate({
        scrollTop: $(".our_room").offset().top - 50
    }, 1000);

    return false;
}

// Show notification function
function showNotification(message, type) {
    // Remove any existing notifications
    $('.notification-container').remove();

    // Create notification container if it doesn't exist
    if ($('.notification-container').length === 0) {
        $('body').append('<div class="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 350px;"></div>');
    }

    // Create the alert
    var alertClass = 'alert-info';
    if (type === 'success') alertClass = 'alert-success';
    if (type === 'warning') alertClass = 'alert-warning';
    if (type === 'error') alertClass = 'alert-danger';

    var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
        message +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span></button></div>');

    // Add to container
    $('.notification-container').append(notification);

    // Auto remove after 5 seconds
    setTimeout(function () {
        notification.alert('close');
    }, 5000);
}