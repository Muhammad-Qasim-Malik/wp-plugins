jQuery(document).ready(function($) {
    // Handle the form submission for creating a sitemap
    $('form#create-sitemap-form').on('submit', function(e) {
        e.preventDefault(); 
        
        var formData = new FormData(this);
        formData.append('security', sitemap_gen.security_nonce); 
        formData.append('action', 'create_sitemap'); 
        console.log(sitemap_gen.ajax_url);  
        console.log(sitemap_gen.security_nonce); 

        $.ajax({
            type: 'POST',
            url: sitemap_gen.ajax_url,
            data: formData,
            processData: false, 
            contentType: false,
            success: function(response) {
                // console.log("Response:", response);  
                // if (response.success) {
                //     alert(response.message);
                //     location.reload(); 
                // } else {
                //     alert('Error: ' + response.message);
                // }
                // console.log(response.data.message);
                location.reload(); 
            },
            error: function() {
                alert('Error submitting the form.');
            }
        });
    });

    $('#sitemap-edit-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            type: 'POST',
            url: sitemap_gen.ajax_url,
            data: formData + '&action=edit_sitemap_link' + '&security=' + sitemap_gen.security_nonce,
            success: function(response) {
                console.log("Response:", response); 
                if (response.success) {
                    alert(response.data.message); 

                    $('#sitemap-modal').fadeOut();
                    location.reload(); 
                } else {
                    alert(response.data.message); 
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error:", textStatus, errorThrown);
                alert('Error occurred while updating sitemap');
            }
        });
    });
    $('.edit-sitemap-btn').on('click', function() {
        const linkId = $(this).data('link-id');
        const currentSitemap = $(this).data('current-sitemap');
        const linkUrl = $(this).data('link-url'); 
        
        $('#link_id').val(linkId);
        $('#new_sitemap_name').val(currentSitemap);
        $('#link_url').val(linkUrl); 

        // Show the modal
        $('#sitemap-modal').fadeIn();
    });
    
    // Close the modal
    $('#close-modal').on('click', function() {
        $('#sitemap-modal').fadeOut(); 
    });
});


