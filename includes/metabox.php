<?php
/**
 * Build our custom meta boxes here.  This is dirty right now but it works.
 */
$sn_prefix = 'sn_';

$sn_meta_box = array(
    'id' => 'sn_meta_box',
    'title' => __('Notice Configuration', 'simple-notices'),
    'context' => 'side',
    'priority' => 'low',
    'fields' => array(
        array(
		'name' => __('Color', 'rcp'),
        	'id' => '_notice_color',
        	'type' => 'select',
        	'desc' => __('Choose the notice color', 'simple-notices'),
    		'options' => array('Blue', 'Red', 'Orange', 'Green', 'Gray')
     	),
        array(
        	'name' => __('Logged In Users', 'rcp'),
		'id' => '_notice_for_logged_in_only',
		'type' => 'checkbox',
		'desc' => __('Show Always When Logged In<br><span style="font-size:70%;">(For testing)</span>', 'simple-notices')
        ),
        array(
            	'name' => __('Use Shortcode', 'rcp'),
            	'id' => '_display_using_shortcode_only',
            	'type' => 'checkbox',
            	'desc' => __('Display Using Shortcode Only<br><span style="font-size:70%;">[simple-notice-two]</span>', 'simple-notices')
        ),
        array(
            	'name' => __('Cookie Expiration', 'rcp'),
            	'id' => '_cookie_expiration_minutes',
            	'type' => 'number',
            	'desc' => __('Cookie Expiration in Minutes<br><span style="font-size:70%;">(\'0\' for end of session)</span>', 'simple-notices')
        ),
    )
);

// Add meta box
function sn_add_meta_boxes() {
    global $sn_meta_box;
	add_meta_box($sn_meta_box['id'], $sn_meta_box['title'], 'sn_render_meta_box', 'notices', $sn_meta_box['context'], $sn_meta_box['priority']);
}
add_action('admin_menu', 'sn_add_meta_boxes');

// Callback function to show fields in meta box
function sn_render_meta_box() {
    global $sn_meta_box, $post;
    
    // Use nonce for verification
    echo '<input type="hidden" name="sn_meta_box" value="', wp_create_nonce(basename(__FILE__)), '" />';
    echo '<table class="form-table">';
    foreach ($sn_meta_box['fields'] as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);
        echo '<tr>';
		echo '<td style="width: 70%;">', $field['desc'], '</td>';
        	echo '<td>';
			switch ($field['type']) {
				case 'select':
					echo '<select name="', $field['id'], '" id="', $field['id'], '">';
					foreach ($field['options'] as $option) {
						echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
					}
					echo '</select>';
					break;
				case 'checkbox':
					echo '<input type="checkbox" value="1" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
					break;
				case 'number':
					echo '<input style="width:100%;" type="number" onkeypress="return isNumberKey(event)" value="'.$meta.'" name="', $field['id'], '" id="', $field['id'], '" />';
					break;
			}
		echo '</td>';
        echo '</tr>';
    }
    echo '</table>';

    $expires = get_post_meta( $post->ID, '_pw_spe_expiration', true );
    $label = ! empty( $expires ) ? date_i18n( 'Y-n-d', strtotime( $expires ) ) : __( 'never', 'pw-spe' );
    $date  = ! empty( $expires ) ? date_i18n( 'Y-n-d', strtotime( $expires ) ) : '';
    $date = esc_attr( $date );

    echo <<<EOT
    <div id="pw-spe-expiration-wrap" class="misc-pub-section">
        <span>
            <span class="wp-media-buttons-icon dashicons dashicons-calendar"></span>&nbsp;Expires:
            <b id="pw-spe-expiration-label">{$label}</b>
        </span>
        <a href="#" id="pw-spe-edit-expiration" class="pw-spe-edit-expiration hide-if-no-js">
            <span aria-hidden="true">Edit</span>&nbsp;
            <span class="screen-reader-text">Edit date and time</span>
        </a>
        <div id="pw-spe-expiration-field" class="hide-if-js">
            <p>
               <input type="text" name="pw-spe-expiration" id="pw-spe-expiration" value="{$date}" placeholder="yyyy-mm-dd"/>
            </p>
            <p>
                <a href="#" class="pw-spe-hide-expiration button secondary">OK</a>
                <a href="#" class="pw-spe-hide-expiration cancel">Cancel</a>
            </p>
        </div>
    </div>
EOT;
}

/**
 * Save data from meta box
 * 
 * @param  int      $post_id
 * @return void
 */
function sn_save_meta_data($post_id) {
    global $sn_meta_box;
    
    // verify nonce
    if (!isset($_POST['sn_meta_box']) || !wp_verify_nonce($_POST['sn_meta_box'], basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    
    foreach ($sn_meta_box['fields'] as $field) {
		if(isset($_POST[$field['id']])) {
			
			$old = get_post_meta($post_id, $field['id'], true);
			$data = $_POST[$field['id']];
			
			if (($data || $data == 0) && $data != $old) {
				update_post_meta($post_id, $field['id'], $data);
			} elseif ('' == $data && $old) {
				delete_post_meta($post_id, $field['id'], $old);
			}
		} else {
			delete_post_meta($post_id, $field['id']);
		}
    }

    $postedDate = $_POST['pw-spe-expiration'];
    $result = update_post_meta($post_id, '_pw_spe_expiration', $postedDate);
   
}
add_action('save_post', 'sn_save_meta_data');
