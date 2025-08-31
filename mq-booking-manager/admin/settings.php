<?php

if(!function_exists('mqbm_settings')){
    function mqbm_settings(){
        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $schedule = get_option('mq_weekly_schedule', []);

        if (isset($_POST['mq_save_schedule'])) {
            $new_schedule = [];
            foreach ($days as $day) {
                if (isset($_POST[$day.'_off'])) {
                    $new_schedule[$day] = ['off' => true];
                } else {
                    $new_schedule[$day] = [
                        'start' => sanitize_text_field($_POST[$day.'_start']),
                        'end'   => sanitize_text_field($_POST[$day.'_end']),
                    ];
                }
            }
            update_option('mq_weekly_schedule', $new_schedule);
            echo "<div class='updated'><p>Schedule saved!</p></div>";
        }
        ?>
        <div class="wrap">
            <h1>Weekly Availability</h1>
            <form method="post">
                <table class="form-table">
                    <?php foreach ($days as $day): 
                        $start = $schedule[$day]['start'] ?? '';
                        $end   = $schedule[$day]['end'] ?? '';
                        $off   = $schedule[$day]['off'] ?? false;
                    ?>
                    <tr>
                        <th><?php echo ucfirst($day); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" class="mq-off-toggle" 
                                    name="<?php echo $day; ?>_off" 
                                    data-day="<?php echo $day; ?>"
                                    <?php checked($off,true); ?>> Off
                            </label><br>
                            <input type="time" 
                                name="<?php echo $day; ?>_start" 
                                class="mq-time-<?php echo $day; ?>" 
                                value="<?php echo esc_attr($start); ?>" 
                                <?php disabled($off,true); ?>>
                            -
                            <input type="time" 
                                name="<?php echo $day; ?>_end" 
                                class="mq-time-<?php echo $day; ?>" 
                                value="<?php echo esc_attr($end); ?>" 
                                <?php disabled($off,true); ?>>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <button type="submit" name="mq_save_schedule" class="button button-primary">Save</button>
            </form>
        </div>
        <?php
    }
}