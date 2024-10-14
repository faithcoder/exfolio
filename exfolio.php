<?php
/**
 * Plugin Name: ExFolio
 * Description: A plugin to manage and display experience sections with jQuery Collapse using shortcodes.
 * Version: 1.0
 * Author: M Arif
 * Text Domain: exfolio
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue styles and scripts
function exfolio_enqueue_assets() {
    // Enqueue CSS
    wp_enqueue_style('exfolio-style', plugin_dir_url(__FILE__) . 'css/exfolio-style.css');

    // Enqueue jQuery (comes with WordPress)
    wp_enqueue_script('jquery');

    // Enqueue JS
    wp_enqueue_script('exfolio-script', plugin_dir_url(__FILE__) . 'js/exfolio-script.js', ['jquery'], false, true);
}
add_action('wp_enqueue_scripts', 'exfolio_enqueue_assets');

// Register Custom Post Type for Experience
function exfolio_register_experience_post_type() {
    $labels = [
        'name'               => 'Experiences',
        'singular_name'      => 'Experience',
        'menu_name'          => 'Experiences',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Experience',
        'edit_item'          => 'Edit Experience',
        'new_item'           => 'New Experience',
        'view_item'          => 'View Experience',
        'all_items'          => 'All Experiences',
        'search_items'       => 'Search Experiences',
        'not_found'          => 'No experiences found.',
        'not_found_in_trash' => 'No experiences found in Trash.',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'show_in_menu'       => true,
        'supports'           => ['title', 'editor', 'thumbnail'],
        'menu_icon'          => 'dashicons-portfolio',
    ];

    register_post_type('exfolio_experience', $args);
}
add_action('init', 'exfolio_register_experience_post_type');

// Add Custom Meta Boxes for Experience
function exfolio_add_experience_meta_boxes() {
    add_meta_box('exfolio_experience_details', 'Experience Details', 'exfolio_experience_meta_box_callback', 'exfolio_experience', 'normal', 'high');
}
add_action('add_meta_boxes', 'exfolio_add_experience_meta_boxes');

function exfolio_experience_meta_box_callback($post) {
    wp_nonce_field('exfolio_save_experience_details', 'exfolio_experience_nonce');

    $company_name = get_post_meta($post->ID, '_exfolio_company_name', true);
    $duration = get_post_meta($post->ID, '_exfolio_duration', true);
    $exfolio_experience_id = get_post_meta($post->ID, '_exfolio_experience_id', true);
   

    echo '<label for="exfolio_company_name">Company Name:</label>';
    echo '<input type="text" id="exfolio_company_name" name="exfolio_company_name" value="' . esc_attr($company_name) . '" class="widefat">';

    echo '<label for="exfolio_duration">Duration:</label>';
    echo '<input type="text" id="exfolio_duration" name="exfolio_duration" value="' . esc_attr($duration) . '" class="widefat">';
    
    echo '<label for="exfolio_experience_id">Experience Section Id:</label>';
    echo '<input type="text" id="exfolio_experience_id" name="exfolio_experience_id" value="' . esc_attr($exfolio_experience_id) . '" class="widefat">';

}

// Save Meta Box Data
function exfolio_save_experience_meta($post_id) {
    if (!isset($_POST['exfolio_experience_nonce']) || !wp_verify_nonce($_POST['exfolio_experience_nonce'], 'exfolio_save_experience_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    update_post_meta($post_id, '_exfolio_company_name', sanitize_text_field($_POST['exfolio_company_name']));
    update_post_meta($post_id, '_exfolio_duration', sanitize_text_field($_POST['exfolio_duration']));
    update_post_meta($post_id, '_exfolio_experience_id', sanitize_text_field($_POST['exfolio_experience_id']));
    
}
add_action('save_post', 'exfolio_save_experience_meta');

// Shortcode to Display Experiences
function exfolio_display_experiences() {
    ob_start();

    $args = [
        'post_type' => 'exfolio_experience',
        'posts_per_page' => -1,
    ];
    $query = new WP_Query($args);

    // Define the URL of the image
    $collapse_icon_down = plugin_dir_url(__FILE__) . 'img/arrow-down.png';
    $collapse_icon_up = plugin_dir_url(__FILE__) . 'img/arrow-up.png';

    if ($query->have_posts()): ?>
        <div class="exfolio-experience-list">
            <?php while ($query->have_posts()): $query->the_post(); ?>
                <?php
                $company_name = get_post_meta(get_the_ID(), '_exfolio_company_name', true);
                $duration = get_post_meta(get_the_ID(), '_exfolio_duration', true);
                $exfolio_experience_id = get_post_meta(get_the_ID(), '_exfolio_experience_id', true);
                
                ?>
                <div class="exfolio-experience-item" 
                    <?php echo isset($exfolio_experience_id) && !empty($exfolio_experience_id) ? 'id="' . esc_attr($exfolio_experience_id) . '"' : ''; ?>>
                    
                    <h3 class="exfolio-experience-title"><?php the_title(); ?></h3>
                    
                    <div class="exfolio-experience-meta-info">
                        <div class="exfolio-company-info">
                            <div class="exfolio-company-logo">
                                <?php if (get_the_post_thumbnail_url()): ?>
                                    <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="">
                                <?php endif; ?>
                            </div>
                            <div class="exfolio-company-name">
                                <?php if ($company_name): ?>
                                    <h4><?php echo esc_html($company_name); ?></h4>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="exfolio-company-duration">
                            <?php if ($duration): ?>
                                <p><?php echo esc_html($duration); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="exfolio_collapse_toggle">
                            <img class="collapse-icon collapse-icon-down" src="<?php echo esc_url($collapse_icon_down); ?>" alt="arrow-down" style="display: none;">
                            <img class="collapse-icon collapse-icon-up" src="<?php echo esc_url($collapse_icon_up); ?>" alt="arrow-up">
                        </div>
                    </div>
                    
                    <div class="exfolio-collapse-content">
                        <p class="exfolio-responsibilities">Responsibilities</p>
                        <p class="exfolio-description"><?php the_content(); ?></p>
                    </div>
                </div>


            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No experiences found.</p>
    <?php endif;

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('exfolio_experiences', 'exfolio_display_experiences');
