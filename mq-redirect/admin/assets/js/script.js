jQuery(document).ready(function($){
    // alert("Hello");
    // console.log(mq_redirect_admin_script.ajax_url);


    $("#add-new").click(function(){
        $('.mq-redirect-add-new-overlay').css('display', 'block');
    })
    $("#upload-csv").click(function(){
        $('.mq-redirect-upload-overlay').css('display', 'block');
    })
     $(".close-btn").click(function(){
        $('.mq-overlay').css('display', 'none');
    })

    $('#delete-btn').click(function(e){
        e.preventDefault();

        var confirmation = confirm("Will you really want to delete?");
        if(confirmation){
            window.location.href = $(this).attr('href');
        }
    })

    $('#mq-form-submit').click(function(e){
        e.preventDefault();

        var old_url = $('#old-url').val();
        var new_url = $('#new-url').val();

        // console.log("Sending " + id + old_url+ new_url);
        $.post(
            mq_redirect_admin_script.ajaxurl,
            {
                'action': 'mq_redirect_add_ajax',
                'old_url' : old_url, 
                'new_url' : new_url
            },
            function(response) {
                // console.log(response);
                // console.log(response.success); 
                // console.log(response.data);    
                if(response.success){
                    alert(response.data);
                    
                    window.location.reload();
                }
            }
        )
    })

    $('#mq-edit-form-submit').click(function(e){
        e.preventDefault();

        var id = $('#edit-id').val();
        var old_url = $('#edit-old-url').val();
        var new_url = $('#edit-new-url').val();

        // console.log("Sending " + id + old_url+ new_url);
        $.post(
            mq_redirect_admin_script.ajaxurl,
            {
                'action': 'mq_redirect_update_ajax',
                'id' : id,
                'old_url' : old_url, 
                'new_url' : new_url
            },
            function(response) {
                // console.log(response);
                // console.log(response.success); 
                // console.log(response.data);    
                if(response.success){
                    alert(response.data);
                    let url = new URL(window.location.href);
                    url.searchParams.delete('edit');
                    window.location.href = url.toString();
                }
            }
        )
    })

    $('#mq-upload-submit').click(function(e){
        e.preventDefault();

        var file = $('#csv-file')[0].files[0];
        if (!file) {  
            alert('Please select a file.');
            return;
        }

        var formData = new FormData();
        formData.append('action', 'mq_redirects_upload_ajax'); 
        formData.append('file', file);   

        $.ajax({
            url: mq_redirect_admin_script.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,     
            processData: false, 
            success: function(response) {
                if (response.success) {
                    alert('Success: ' + response.data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred.');
            }
        })        
    })

    $('#mq-redirect-table').DataTable({
        'pageLength': 10,
        'order': [[0, 'asc']]
    });
})