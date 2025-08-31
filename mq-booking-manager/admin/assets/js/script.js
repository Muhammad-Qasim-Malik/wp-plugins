jQuery(document).ready(function($) {
    $(".mq-off-toggle").on("change", function() {
        let day = $(this).data("day");
        let start = $('input[name="'+day+'_start"]');
        let end   = $('input[name="'+day+'_end"]');

        if ($(this).is(":checked")) {
            start.prop("disabled", true);
            end.prop("disabled", true);
        } else {
            start.prop("disabled", false);
            end.prop("disabled", false);
        }
    });

    // Run once on page load to handle saved state
    $(".mq-off-toggle").each(function() {
        $(this).trigger("change");
    });

    var calendarEl = $("#mq-calendar")[0];

    if (!calendarEl) return;
    // alert("hello");

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: mqbm_ajax.ajaxurl + '?action=mqbm_get_bookings',
        eventColor: '#0073aa',
        eventTextColor: '#fff'
    });

    calendar.render();
});