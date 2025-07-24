<?php

function backstage_admin_page_html() {
    
    if (isset($_POST['submit'])) {
        if (!empty($_FILES['background_image']['name'])) {
            $uploaded_file = $_FILES['background_image'];
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($uploaded_file, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                update_option('backstage_bg_image', $movefile['url']);
                echo '<div class="updated"><p>Background image uploaded successfully!</p></div>';
            } else {
                echo '<div class="error"><p>Error uploading file: ' . $movefile['error'] . '</p></div>';
            }
        }
    }

    $background_image = get_option('backstage_bg_image');
    ?>
    <div class="wrap">
        <h1>Backstage Settings</h1>
        <p>Upload a background image that will be used in your shortcodes.</p>
        
        <form method="post" enctype="multipart/form-data" class="backstage-form">
            <label for="background_image">Choose Background Image:</label><br>
            <input type="file" name="background_image" accept="image/*" required><br><br>
            <input type="submit" name="submit" value="Upload" class="button-primary">
        </form>

        <?php
        if ($background_image) {
            echo '<h2>Current Background Image:</h2>';
            echo '<img src="' . esc_url($background_image) . '" alt="Background Image" style="max-width: 100%; height: auto;">';
        }
        ?>
    </div>

    <style>
        .backstage-form {
            margin-top: 20px;
        }
        .backstage-form input[type="file"] {
            margin-bottom: 10px;
        }
    </style>
</div>
    <?php
}