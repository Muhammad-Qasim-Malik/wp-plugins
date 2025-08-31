// includes/assets/js/script.js

jQuery(document).ready(function($) {
    let currentMonth = parseInt($('#mqbm-calendar-wrapper').data('month')) || new Date().getMonth() + 1; 
    let currentYear = parseInt($('#mqbm-calendar-wrapper').data('year')) || new Date().getFullYear(); 
    let selectedTime = null; // To store the selected time

    // console.log('Initial month/year: ' + currentMonth + '/' + currentYear);

    function bindDayClicks() {
        $('.day:not(.past):not(.off)').off('click').on('click', function() {
            const date = $(this).data('date');
            $.ajax({
                url: mqbm_user_ajax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'mqbm_get_times',
                    date: date,
                    nonce: mqbm_user_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#selected-date').text(date);
                        const list = $('#times-list');
                        list.empty();
                        if (response.data.times.length > 0) {
                            response.data.times.forEach(function(time) {
                                list.append('<li><button class="select-time" data-time="' + time + '">' + time + '</button></li>');
                            });
                            // $('#mqbm-times').show();
                        } else {
                            list.append('<li>No available times</li>');
                        }
                    } else {
                        alert(response.data.message || 'Error fetching times');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error (get_times): ' + status + ' - ' + error);
                    alert('Failed to load times. Check console for details.');
                }
            });
        });
    }

    function bindNavigationClicks() {
        $('#prev-month').off('click').on('click', function() {
            currentMonth--;
            if (currentMonth < 1) {
                currentMonth = 12;
                currentYear--;
            }
            getCalendar(currentMonth, currentYear);
            // console.log('Navigating to: ' + currentMonth + '/' + currentYear);
        });

        $('#next-month').off('click').on('click', function() {
            currentMonth++;
            if (currentMonth > 12) {
                currentMonth = 1;
                currentYear++;
            }
            getCalendar(currentMonth, currentYear);
            // console.log('Navigating to: ' + currentMonth + '/' + currentYear);
        });
    }

    bindNavigationClicks(); // Initial binding
    bindDayClicks(); // Initial binding

    function getCalendar(month, year) {
        $.ajax({
            url: mqbm_user_ajax.ajaxurl,
            type: 'POST',
            data: {
                action: 'mqbm_get_calendar',
                month: month,
                year: year,
                nonce: mqbm_user_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#mqbm-calendar-wrapper').html(response.data.html);
                    currentMonth = month; 
                    currentYear = year;   
                    bindNavigationClicks(); 
                    bindDayClicks(); 
                    // $('#mqbm-times').hide(); 
                    selectedTime = null; 
                    // console.log('Calendar updated to: ' + month + '/' + year);
                } else {
                    alert(response.data.message || 'Error loading calendar');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error (get_calendar): ' + status + ' - ' + error);
                alert('Failed to load calendar. Check console for details. Status: ' + status);
            }
        });
    }

    $(document).on('click', '.select-time', function() {
        selectedTime = $(this).data('time'); 
        $('#form-time').val(selectedTime); 
        $('.select-time').removeClass('selected'); 
        $(this).addClass('selected'); 
        // console.log('Time selected: ' + selectedTime);
    });

    $('#next-to-form').on('click', function() {
        const date = $('#selected-date').text();
        if (date && selectedTime) {
            $('#form-date').val(date);
            $('#booking-datetime').text(date + ' at ' + selectedTime);
            $('#step-1').hide();
            $('#mqbm-form').show();
        } else {
            alert('Please select a date and time first.');
        }
    });

    $('#booking-form').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.ajax({
            url: mqbm_user_ajax.ajaxurl,
            type: 'POST',
            data: {
                action: 'mqbm_book',
                form_data: formData,
                nonce: mqbm_user_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Booking successful!');
                    location.reload();
                } else {
                    alert('Error: ' + (response.data.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error (book): ' + status + ' - ' + error);
                alert('Failed to book. Check console for details.');
            }
        });
    });

    $('#check-next-avail').on('click', function() {
        currentMonth++;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        }
        getCalendar(currentMonth, currentYear);
        // console.log('Checking next availability: ' + currentMonth + '/' + currentYear);
    });

    $('#go-to-step-1').click(function(){
        $('#step-1').show();
        $('#mqbm-form').hide();
    })
});