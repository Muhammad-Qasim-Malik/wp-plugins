jQuery(document).ready(function($){

    // ---------------------------------------------------------------------------------------------------
    // ----------------------------------------Popups-----------------------------------------------------
    // ---------------------------------------------------------------------------------------------------
    // Import Leads Popup
    $('#mq-import-btn').on('click', function(){ $('#mq-import-modal').show(); });
    $('#mq-close-modal').on('click', function(){ $('#mq-import-modal').hide(); });

    // Send Email Template Popup
    $('#mq-template-btn').on('click', function(){ $('#mq-template-popup').show(); });
    $('#mq-close-template-popup').on('click', function(){ $('#mq-template-popup').hide(); });


    // ---------------------------------------------------------------------------------------------------
    // ----------------------------------Search for Leads & Templates-------------------------------------
    // ---------------------------------------------------------------------------------------------------
    $('#search-city').on('change', function(){
        var city = $(this).val();
        $('#search-country').val(cityCountryMap[city] || '');
    });

    var table = $('#mq-leads-table').DataTable({
        pageLength: parseInt($('#mq-show-entries').val()),
        lengthChange: false,
        dom: 'lrtip' 
    });


    $('#mq-show-entries').on('change', function(){
        table.page.len($(this).val()).draw();
    });

    // Column search
    $('#search-id').on('keyup change', function(){ table.column(1).search(this.value).draw(); });
    $('#search-name').on('keyup change', function(){ table.column(2).search(this.value).draw(); });
    $('#search-email').on('keyup change', function(){ table.column(3).search(this.value).draw(); });
    $('#search-phone').on('keyup change', function(){ table.column(4).search(this.value).draw(); });
    $('#search-website').on('keyup change', function(){ table.column(5).search(this.value).draw(); });
    $('#search-niche').on('change', function(){ table.column(6).search(this.value).draw(); });
    $('#search-address').on('keyup change', function(){ table.column(7).search(this.value).draw(); });
    $('#search-city').on('change', function(){ table.column(8).search(this.value).draw(); });
    $('#search-status').on('change', function(){ table.column(11).search(this.value).draw(); });
    $('#search-country').on('keyup change', function(){ table.column(9).search(this.value).draw(); });



    // ---------------------------------------------------------------------------------------------------
    // -----------------------------------------Search for Emails-----------------------------------------
    // ---------------------------------------------------------------------------------------------------
    var email_table = $('#mq-emails-table').DataTable({
        pageLength: parseInt($('#mq-show-entries').val()),
        lengthChange: false,
        dom: 'lrtip' 
    });
        // Column search
    $('#search-id').on('keyup change', function(){ email_table.column(0).search(this.value).draw(); });
    $('#search-lead-id').on('keyup change', function(){ email_table.column(1).search(this.value).draw(); });
    $('#search-template').on('keyup change', function(){ email_table.column(2).search(this.value).draw(); });
    $('#search-recipient').on('keyup change', function(){ email_table.column(3).search(this.value).draw(); });
    $('#search-status').on('change', function(){ email_table.column(4).search(this.value).draw(); });;


    $('#mq-select-all').on('change', function(){
        $('.mq-lead-checkbox').prop('checked', $(this).is(':checked'));
    });

    $('#mq-bulk-update-status').on('click', function(){
        var ids = $('.mq-lead-checkbox:checked').map(function(){return $(this).val();}).get();
        if(ids.length==0){ alert('Select leads first'); return; }
        var status = prompt("Enter new status:");
        if(status){
            $.post(ajaxurl, {action:'mq_update_status', ids:ids, status:status}, function(){
                location.reload();
            });
        }
    });

    $('#mq-bulk-delete').on('click', function(){
        var ids = $('.mq-lead-checkbox:checked').map(function(){return $(this).val();}).get();
        if(ids.length==0){ alert('Select leads first'); return; }
        if(confirm("Delete selected leads?")){
            $.post(ajaxurl, {action:'mq_delete_leads', ids:ids}, function(){ location.reload(); });
        }
    });

    $('.mq-edit-status').on('click', function(){
        var row = $(this).closest('tr');
        var id = row.data('id');
        var current = row.find('.status').text();
        var new_status = prompt("Enter new status:", current);
        if(new_status){
            $.post(ajaxurl, {action:'mq_update_status', ids:[id], status:new_status}, function(){ location.reload(); });
        }
    });

    $('.mq-delete-lead').on('click', function(){
        if(!confirm("Delete this lead?")) return;
        var row = $(this).closest('tr');
        var id = row.data('id');
        $.post(ajaxurl, {action:'mq_delete_leads', ids:[id]}, function(){ location.reload(); });
    });

    $('#search-city').on('change', function(){
        var city = $(this).val();
        $('#search-country').val(cityCountryMap[city] || '');
    });


    $('#mq-select-all-templates').on('click', function() {
        $('.mq-template-checkbox').prop('checked', this.checked);
    });

    $('#mq-select-all-email').on('click', function() {
        $('.mq-send-checkbox').prop('checked', this.checked);
    });

    // Bulk delete
     $('#mq-bulk-delete-templates').on('click', function(){
        var ids = $('.mq-template-checkbox:checked').map(function(){return $(this).val();}).get();
        if(ids.length==0){ alert('Select Templates first'); return; }
        if(confirm("Delete selected templates?")){
            $.post(ajaxurl, {action:'mq_bulk_delete_templates', temp_ids:ids}, function(){ location.reload(); });
        }
    });

    $("#mq-send-email-btn").on("click", function (e) {
        e.preventDefault();

        let templateId = $("#mq-template-select").val();
        if (!templateId) {
            alert("Please select a template.");
            return;
        }

        let leadIds = [];
        $(".mq-send-checkbox:checked").each(function () {
            leadIds.push($(this).val());
        });

        if (leadIds.length === 0) {
            alert("Please select at least one lead.");
            return;
        }
        $(this).prop("disabled", true).text("Sending...");

        $.ajax({
            url: ajaxurl, 
            type: "POST",
            dataType: "json",
            data: {
                action: "mq_send_emails",
                template_id: templateId,
                lead_ids: leadIds
            },
            success: function (response) {
                if (response.success) {
                    window.location.href = window.location.href.split('?')[0] + "?page=mq-send-emails&sent=" + response.data.message;
                } else {
                    alert("Error: " + response.data.message);
                }
            },
            error: function () {
                alert("Something went wrong. Please try again.");
            },
            complete: function () {
                $("#mq-send-email-btn").prop("disabled", false).text("Send Emails");
            }
        });
    });

    
});
