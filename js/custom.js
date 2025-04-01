/*---------------------------------------------------------------------
    Minimal Custom JS - Removes all plugin dependencies
---------------------------------------------------------------------*/

$(function () {
    "use strict";
    
    // Preloader
    setTimeout(function () {
        $('.loader_bg').fadeToggle();
    }, 1500);
    
    // Enable Bootstrap tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Mobile menu toggle
    $('.navbar-toggler').on('click', function() {
        $('.navbar-collapse').toggleClass('show');
    });
    
    // Simple scroll to top function
    $(window).on('scroll', function () {
        var scroll = $(window).scrollTop();
        if (scroll >= 100) {
            $("#back-to-top").addClass('b-show_scrollBut');
        } else {
            $("#back-to-top").removeClass('b-show_scrollBut');
        }
    });
    
    $("#back-to-top").on("click", function () {
        $('body,html').animate({
            scrollTop: 0
        }, 1000);
    });
    
    // Simple date validation for booking forms
    $('#availability_form').on('submit', function(e) {
        e.preventDefault();
        
        var arrivalDate = $('#arrival_date').val();
        var departureDate = $('#departure_date').val();
        
        if (!arrivalDate || !departureDate) {
            alert('Please select both arrival and departure dates');
            return false;
        }
        
        // Basic validation
        var arrival = new Date(arrivalDate);
        var departure = new Date(departureDate);
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (arrival < today) {
            alert('Arrival date cannot be in the past');
            return false;
        }
        
        if (departure <= arrival) {
            alert('Departure date must be after arrival date');
            return false;
        }
        
        // On success, scroll to rooms section
        $('html, body').animate({
            scrollTop: $(".our_room").offset().top - 50
        }, 1000);
        
        setTimeout(function() {
            alert('Rooms are available for your selected dates. Please choose a room below.');
        }, 1000);
    });
    
    // Handle room booking button clicks
    $('.book_room_btn').on('click', function() {
        const roomId = $(this).data('room-id');
        const roomName = $(this).data('room-name');
        
        $('#room_id').val(roomId);
        $('#modal_room_name').val(roomName);
        
        // Transfer dates from availability form if set
        const arrivalDate = $('#arrival_date').val();
        const departureDate = $('#departure_date').val();
        const adults = $('#adults').val();
        
        if (arrivalDate) {
            $('#modal_arrival_date').val(arrivalDate);
        }
        if (departureDate) {
            $('#modal_departure_date').val(departureDate);
        }
        if (adults) {
            $('#modal_adults').val(adults);
        }
        
        $('#bookingModal').modal('show');
    });
    
    // Handle spa service booking
    $('.spa-book-btn').on('click', function() {
        const serviceId = $(this).data('service-id');
        const serviceName = $(this).data('service-name');
        
        $('#service_id').val(serviceId);
        $('#service_name').val(serviceName);
        
        $('#serviceModal').modal('show');
    });
    
    // Submit booking forms
    $('#submit_booking').on('click', function() {
        $('#room_booking_form').submit();
    });
});