<?php
use PhpOffice\PhpSpreadsheet\IOFactory;

function backstage_upload_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['submit_xlsx']) && isset($_FILES['xlsx_file'])) {
        if ($_FILES['xlsx_file']['error'] == 0) {
            $file = $_FILES['xlsx_file']['tmp_name'];

            try {
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                
                $data = [];
                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $rowData = [];
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    if (count($rowData) == 2) {
                        $data[] = $rowData;
                    }
                }

                foreach ($data as $row) {
                    $slug = sanitize_title_with_dashes($row[0]);
                    $image_url = esc_url_raw($row[1]);
                    $post = get_page_by_path($slug, OBJECT, 'post');
                    if ($post) {
                        update_post_meta($post->ID, '_image_url', $image_url);
                    }
                }

                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('File processed and image URLs saved!', 'backstage') . '</p></div>';
            } catch (Exception $e) {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Error loading the XLSX file: ', 'backstage') . esc_html($e->getMessage()) . '</p></div>';
            }
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('File upload error: ', 'backstage') . esc_html($_FILES['xlsx_file']['error']) . '</p></div>';
        }
    }
    ?>
    <div class="wrap backstage-wrap">
        <h1><?php esc_html_e('Backstage Upload Settings', 'backstage'); ?></h1>
        <form action="" method="post" enctype="multipart/form-data" class="backstage-form">
            <div class="backstage-form-field">
                <p><?php esc_html_e('Upload XLSX file containing slugs and image URLs:', 'backstage'); ?></p>
                <input type="file" name="xlsx_file" id="xlsx_file" accept=".xlsx" required />
            </div>
            <input type="submit" name="submit_xlsx" value="<?php esc_attr_e('Upload and Process', 'backstage'); ?>" class="button button-primary backstage-submit" />
        </form>
    </div>
    <?php
}
?>