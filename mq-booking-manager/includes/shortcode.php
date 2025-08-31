<?php
// includes/shortcode.php

function mq_booking_shortcode() {
    if(isset($_GET['service'])){
        $service_id = $_GET['service'];

        if($service_id && $service_id == 1){
            $service = 'Free Estimate Project';
        } elseif ($service_id && $service_id == 2) {
            $service = 'Kitchen Remodeling';
        } elseif ($service_id && $service_id == 3) {
            $service = 'General Contracting';
        } elseif ($service_id && $service_id == 4) {
            $service = 'Bathroom Remodeling';
        } elseif ($service_id && $service_id == 5) {
            $service = 'Deck Building';
        } else {
            $service = 'Free Estimate Project';
        }
    } else {
        $service = 'Free Estimate Project';
    }
    $month = date('n'); 
    $year = date('Y');
    ob_start();
    ?>
    <div class="mqbm-booking">
        <div class="booking-header">
            <h2>Schedule your service</h2>
            <p>Check out our availability and book the date and time that works for you</p>
        </div>
        <div class="booking-body">
            <div id="step-1" class="step-1">
                <div id="booking-calendar-time">
                    <h3>Select a Date and Time</h3>
                    <div id="calendar-wrap">
                        <div id="mqbm-calendar-wrapper" data-month="<?php echo esc_attr($month); ?>" data-year="<?php echo esc_attr($year); ?>">
                            <?php mqbm_generate_calendar_html($month, $year); ?>
                        </div>
                        <div id="mqbm-times" >
                            <h4>Availability for <span id="selected-date"></span></h4>
                            <ul id="times-list"></ul>
                        </div>
                    </div>
                </div>
                <div id="service-detail">
                    <h3>Service Details</h3>
                    <p><strong>Service Name: </strong> <?php echo $service; ?></p>
                    <button id="next-to-form" >Next</button>
                </div>
            </div>
            <div id="mqbm-form" class="step-2" style="display: none;">
                <form id="booking-form"> 
                    <div id="main-form">
                        <h3>Booking Form</h3>
                        <button id="go-to-step-1" >Back</button>
                        <input type="hidden" name="service" value="<?php echo esc_attr($service); ?>">
                        <input type="hidden" name="date" id="form-date">
                        <input type="hidden" name="time" id="form-time">
                        <!-- <h3>Client Details</h3> -->
                        <!-- <p>Have an account? Log in</p> -->
                        <div class="form-row">
                            <label>First name *<input type="text" name="first" required></label>
                            <label>Last name *<input type="text" name="last" required></label>
                        </div>
                        <div class="form-row">
                            <label>Email *<input type="email" name="email" required></label>
                            <label>Phone<input type="tel" name="phone"></label>
                        </div>

                        <label>Add your message<textarea name="message"></textarea></label>
                    </div>
                    <div id="form-others">
                        <div id="mqbm-section-1">
                            <h3>Booking Details</h3>
                            <p><strong>Service Name: </strong> <?php echo $service; ?></p>
                            <p><strong>Booking Time: </strong> <span id="booking-datetime"></span></p>
                        </div>
                        <div id="mqbm-section-2">
                            <h3>Payment Details</h3>
                            <p>Free</p>
                        </div>
                        <button type="submit" class="mq-mera-button">Book Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('mq_booking', 'mq_booking_shortcode');