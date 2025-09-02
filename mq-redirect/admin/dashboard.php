<?php 

function mq_redirect_dashboard(){
    ?>
    

    <div class="wrap">
        <h1>MQ Redirects</h1>

        <?php 
            if(isset($_GET['edit'])){
                $id = $_GET['edit'];
                $result = mq_redirect_get_redirects_by_id('redirects', $id);

                $result = get_object_vars($result[0]);
                ?>
                <div class="mq-redirect-add-new-overlay mq-overlay">
                    <div class="mq-edit-form mq-form">
                        <h2>Edit Redirect</h2>
                        <button class="close-btn">X</button>
                        <input type="hidden" name="edit_id" value="<?php echo $result['id'] ?>" id="edit-id">
                        <input type="url" name="edit_old_url" id="edit-old-url" value="<?php echo $result['old_url'] ?>">
                        <input type="url" name="edit_new_url" id="edit-new-url" value="<?php echo $result['new_url'] ?>">
                        <input type="submit" value="Submit" name="mq_edit_form_submit" id="mq-edit-form-submit" class="button button-primary">
                    </div>
                </div>
                
                <?php
            }
        ?>
        <div class="mq-redirect-filters">
            <div class="filters"></div>
            <button id="add-new" class="button button-primary">Add New</button>
            <button id="upload-csv" class="button button-primary">Upload CSV</button>
        </div>

        <div class="mq-redirect-add-new-overlay mq-overlay" style="display: none;">
           <div class="mq-add-new-form mq-form">
            <h2>Add new Redirect</h2>
                <input type="url" name="old_url" id="old-url" placeholder="oldurl.com">
                <input type="url" name="new_url" id="new-url" placeholder="newurl.com">
                <input type="submit" value="Submit" name="mq_form_submit" id="mq-form-submit" class="button button-primary">
                <button class="close-btn">X</button>
            </div>
            
        </div>
        <div class="mq-redirect-upload-overlay mq-overlay" style="display: none;">
            <div class="wrap mq-form">
                <h2>Import Redirects from CSV</h2>
                <button class="close-btn">X</button>
                <input type="file" name="redirects_csv" accept=".csv" id="csv-file" required>
                <input type="submit" name="mq_import_submit" class="button button-primary" id="mq-upload-submit" value="Upload & Import">
            </div>
        </div>
         
        

        <table id="mq-redirect-table" class="widefat striped" style="width:100%; ">

            <thead>
                <th>Source URL</th>
                <th>Target URL</th>
                <th >Actions</th>
            </thead>
            <tbody>

                <?php 
                    $results = mq_redirect_get_redirects('redirects');
                    foreach($results as $result){
                        $result = get_object_vars($result);
                ?>
                <tr>
                    <td><?php echo $result['old_url']; ?></td>
                    <td><?php echo $result['new_url']; ?></td>
                    <td>
                        <a href="<?php echo MQ_REDIRECT_SITE_URL . '/wp-admin/admin.php?page=mq-redirect&edit='. $result['id'] ?>"class="button button-primary" id="edit-btn">Edit</a>
                        <a href="<?php echo MQ_REDIRECT_SITE_URL . '/wp-admin/admin.php?page=mq-redirect&delete='. $result['id'] ?>" class="button button-secondary" id="delete-btn">Delete</a>
                    </td>
                </tr>
                
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
}