<?php
/**
 * Plugin Name: MS Site Creation Query Tracker
 * Description: Create sites and track database queries
 * Author: SirLouen <sir.louen@gmail.com>
 * Version: 1.0.0
 * Network: true
 */

if (!defined('ABSPATH') || !is_multisite()) {
    exit;
}

add_action('network_admin_menu', 'mscqt_add_menu');

function mscqt_add_menu() {
    add_submenu_page(
        'sites.php',
        'Site Tracker',
        'Site Tracker',
        'manage_network',
        'site-tracker',
        'mscqt_page'
    );
}

function mscqt_page() {
    if (isset($_POST['create'])) {
        mscqt_create_site();
    }
    ?>
    <div class="wrap">
        <h1>Site Creation Tracker</h1>
        <form method="post">
            <p>
                <label>Path: <input type="text" name="path" value="/test-<?php echo time(); ?>/" required></label>
            </p>
            <p>
                <input type="submit" name="create" value="Create Site" class="button-primary">
            </p>
        </form>
    </div>
    <?php
}

function mscqt_create_site() {
    $path = sanitize_text_field($_POST['path']);
    
    $num_queries_start = get_num_queries();
    
    $start = microtime(true);
    
    $result = wp_insert_site(array(
        'domain' => get_network()->domain,
        'path' => $path,
        'title' => 'Test Site',
        'user_id' => get_current_user_id()
    ));
    
    if (is_wp_error($result)) {
        echo '<div class="error"><p>Error: ' . $result->get_error_message() . '</p></div>';
    } else {
        echo '<div class="updated"><p>';
        echo 'Site created! ID: ' . $result . '<br>';
        echo 'Queries: ' . (get_num_queries() - $num_queries_start) . '<br>';
        echo '</p></div>';
    }
}