<?php

add_action( 'add_meta_boxes', 'contentgate_add_metabox' );
function contentgate_add_metabox() {
    add_meta_box(
        'contentgate_access_rules',
        'ContentGate – Access Rules',
        'contentgate_render_metabox',
        ['post', 'page'],
        'side',
        'default'
    );
}

function contentgate_render_metabox( $post ) {

    wp_nonce_field( 'contentgate_save_rules', 'contentgate_nonce' );

    $rules = get_post_meta( $post->ID, 'contentgate_rules', true );
    $rules = is_array( $rules ) ? $rules : [];

    $enabled      = $rules['enabled'] ?? '';
    $condition    = $rules['condition'] ?? '';
    $roles        = $rules['roles'] ?? [];
    $days         = $rules['days'] ?? [];
    $message      = $rules['message'] ?? '';
    $display_mode = $rules['display_mode'] ?? 'replace';

    $wp_roles = wp_roles()->roles;
    ?>

    <p>
        <label>
            <input type="checkbox" name="contentgate_rules[enabled]" value="1" <?php checked( $enabled, 1 ); ?>>
            Enable content restriction
        </label>
    </p>

    <p>
        <label for="contentgate_condition">Who can see this content?</label>
        <select name="contentgate_rules[condition]" id="contentgate_condition" style="width:100%;">
            <option value="">Select condition</option>
            <option value="logged_in" <?php selected( $condition, 'logged_in' ); ?>>Logged-in users only</option>
            <option value="logged_out" <?php selected( $condition, 'logged_out' ); ?>>Logged-out users only</option>
            <option value="user_role" <?php selected( $condition, 'user_role' ); ?>>Specific user roles</option>
            <option value="day_of_week" <?php selected( $condition, 'day_of_week' ); ?>>Specific days</option>
        </select>
    </p>

    <!-- Roles -->
    <div class="contentgate-field contentgate-roles">
        <strong>Allowed user roles</strong>
        <?php foreach ( $wp_roles as $key => $role ) : ?>
            <p>
                <label>
                    <input type="checkbox" name="contentgate_rules[roles][]" value="<?php echo esc_attr( $key ); ?>"
                        <?php checked( in_array( $key, $roles, true ) ); ?>>
                    <?php echo esc_html( $role['name'] ); ?>
                </label>
            </p>
        <?php endforeach; ?>
    </div>

    <!-- Days -->
    <div class="contentgate-field contentgate-days">
        <strong>Visible on these days</strong>
        <?php
        $week_days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        foreach ( $week_days as $day ) :
        ?>
            <p>
                <label>
                    <input type="checkbox" name="contentgate_rules[days][]" value="<?php echo esc_attr( $day ); ?>"
                        <?php checked( in_array( $day, $days, true ) ); ?>>
                    <?php echo esc_html( ucfirst( $day ) ); ?>
                </label>
            </p>
        <?php endforeach; ?>
    </div>

    <p>
        <label for="contentgate_message">Restriction message</label>
        <textarea name="contentgate_rules[message]" id="contentgate_message" rows="4" style="width:100%;"><?php
            echo esc_textarea( $message );
        ?></textarea>
    </p>

    <p>
        <strong>Display behavior</strong><br>
        <label>
            <input type="radio" name="contentgate_rules[display_mode]" value="replace"
                <?php checked( $display_mode, 'replace' ); ?>>
            Replace content
        </label><br>
        <label>
            <input type="radio" name="contentgate_rules[display_mode]" value="append"
                <?php checked( $display_mode, 'append' ); ?>>
            Show message above content
        </label>
    </p>

    <?php
}

add_action( 'save_post', 'contentgate_save_metabox' );

function contentgate_save_metabox( $post_id ) {

    // 1. Check nonce
    if ( ! isset( $_POST['contentgate_nonce'] ) || ! wp_verify_nonce( $_POST['contentgate_nonce'], 'contentgate_save_rules' ) ) {
        return;
    }

    // 2. Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // 3. Check user permission
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // 4. Check if our data exists
    if ( ! isset( $_POST['contentgate_rules'] ) || ! is_array( $_POST['contentgate_rules'] ) ) {
        return;
    }

    $data = $_POST['contentgate_rules'];

    // 5. Sanitize fields
    $rules = [];

    // Enabled
    $rules['enabled'] = isset( $data['enabled'] ) && $data['enabled'] ? 1 : 0;

    // Condition
    $allowed_conditions = ['logged_in','logged_out','user_role','day_of_week'];
    $rules['condition'] = in_array( $data['condition'] ?? '', $allowed_conditions, true ) ? $data['condition'] : '';

    // Roles
    if ( isset( $data['roles'] ) && is_array( $data['roles'] ) ) {
        $rules['roles'] = array_map( 'sanitize_text_field', $data['roles'] );
    } else {
        $rules['roles'] = [];
    }

    // Days
    $week_days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
    if ( isset( $data['days'] ) && is_array( $data['days'] ) ) {
        $rules['days'] = array_intersect( $data['days'], $week_days );
    } else {
        $rules['days'] = [];
    }

    // Message
    $rules['message'] = isset( $data['message'] ) ? sanitize_textarea_field( $data['message'] ) : '';

    // Display mode
    $rules['display_mode'] = ( isset( $data['display_mode'] ) && in_array( $data['display_mode'], ['replace','append'], true ) )
        ? $data['display_mode']
        : 'replace';

    // 6. Update post meta
    update_post_meta( $post_id, 'contentgate_rules', $rules );
}


