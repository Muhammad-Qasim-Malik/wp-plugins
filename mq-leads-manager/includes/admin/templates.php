<?php

function mq_templates_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mq_email_templates';

    $mode = isset($_GET['mode']) ? sanitize_text_field($_GET['mode']) : 'list';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Available lead variables
    $available_vars = [
        '{business_name}' => 'Business Name',
        '{email}'        => 'Email',
        '{phone}'        => 'Phone',
        '{website}'      => 'Website',
        '{niche}'        => 'Niche',
        '{address}'      => 'Address',
        '{city}'         => 'City',
        '{country}'      => 'Country',
        '{status}'       => 'Status',
        '{date_added}'   => 'Date Added'
    ];

    // ----------------- Handle Delete -----------------
    if (isset($_GET['delete']) && intval($_GET['delete'])) {
        $wpdb->delete($table_name, ['id' => intval($_GET['delete'])]);
        echo '<div class="notice notice-success is-dismissible"><p>Template deleted successfully!</p></div>';
    }

    // ----------------- Display List -----------------
    if ($mode === 'list') {
        $templates = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A);
        ?>
       <div class="wrap">
            <h1>Email Templates</h1>
            <a href="<?php echo admin_url('admin.php?page=mq-templates&mode=add'); ?>" class="button button-primary">Add New Template</a>
            <button id="mq-bulk-delete-templates" class="button button-secondary">Delete Selected</button>

            <table id="mq-templates-table" class="widefat striped" style="margin-top:15px; width:100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="mq-select-all-templates"></th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($templates): foreach ($templates as $t): ?>
                        <tr>
                            <td><input type="checkbox" class="mq-template-checkbox" value="<?php echo $t['id']; ?>"></td>
                            <td><?php echo $t['id']; ?></td>
                            <td><?php echo esc_html($t['template_name']); ?></td>
                            <td><?php echo esc_html($t['subject']); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=mq-templates&mode=edit&id='.$t['id']); ?>" class="button button-small">Edit</a>
                                <a href="<?php echo admin_url('admin.php?page=mq-templates&delete='.$t['id']); ?>" class="button button-small" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="5">No templates found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
    }

    // ----------------- Display Add / Edit Form -----------------
    if ($mode === 'add' || $mode === 'edit') {
        $template = null;
        if ($mode === 'edit' && $id > 0) {
            $template = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id=%d", $id), ARRAY_A);
        }
        ?>
        <div class="wrap">
            <h1><?php echo $mode === 'add' ? 'Add New Template' : 'Edit Template'; ?></h1>

            <?php
            if (isset($_GET['added'])) {
                echo '<div class="notice notice-success is-dismissible"><p>Template added successfully!</p></div>';
            }
            if (isset($_GET['updated'])) {
                echo '<div class="notice notice-success is-dismissible"><p>Template updated successfully!</p></div>';
            }
            if (isset($_GET['test_sent'])) {
                echo '<div class="notice notice-success is-dismissible"><p>Test email sent successfully!</p></div>';
            }
            if (isset($_GET['test_fail'])) {
                echo '<div class="notice notice-error is-dismissible"><p>There is an error while sending email. Please try again in a while!</p></div>';
            }
            ?>

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('mq_save_template', 'mq_template_nonce'); ?>
                <input type="hidden" name="action" value="mq_save_template">
                <?php if ($template) : ?>
                    <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
                <?php endif; ?>

                <table class="form-table">
                    <tr>
                        <th>Title</th>
                        <td><input type="text" name="template_title" value="<?php echo $template ? esc_attr($template['template_name']) : ''; ?>" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td><input type="text" name="template_subject" value="<?php echo $template ? esc_attr($template['subject']) : ''; ?>" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th>Body</th>
                        <td>
                            <?php
                            $content = $template ? $template['body'] : '';
                            wp_editor($content, 'template_body', ['textarea_name' => 'template_body', 'textarea_rows' => 10]);
                            ?>
                        </td>
                    </tr>
                </table>

                <h2>Available Variables</h2>
                <ul>
                    <?php foreach ($available_vars as $var => $desc): ?>
                        <li><strong><?php echo esc_html($var); ?></strong> — <?php echo esc_html($desc); ?></li>
                    <?php endforeach; ?>
                </ul>

                <p><input type="submit" class="button button-primary" value="<?php echo $template ? 'Update Template' : 'Add Template'; ?>"></p>
            </form>

            <?php if ($template): ?>
                <h2>Send Test Email</h2>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <input type="hidden" name="action" value="mq_send_test_email">
                    <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
                    <input type="email" name="test_email" placeholder="Enter test email" required>
                    <input type="submit" class="button button-secondary" value="Send Test Email">
                </form>
            <?php endif; ?>
        </div>
        <?php
    }
}
