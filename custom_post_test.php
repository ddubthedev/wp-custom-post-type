<?php
/*
Plugin Name: Custom Post Type Test
Description: This is a test to see if I can make custom post types into a plugin.
Version: 1.1
Author: Daniel Warner
Author URI: http://danielwarner.ca
License: GPLv2
*/

// Initialize custom post type on load
add_action( 'init', 'create_custom_post' );

// Register custom post type
function create_custom_post() {
    register_post_type( 'featured_performer',
        array(
            'labels' => array(
                'name' => 'Performers',
                'singular_name' => 'Performer',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Performer',
                'edit' => 'Edit',
                'edit_item' => 'Edit Performer',
                'new_item' => 'New Performer',
                'view' => 'View',
                'view_item' => 'View Performer',
                'search_items' => 'Search Performers',
                'not_found' => 'No Performers found',
                'not_found_in_trash' => 'No Performers found in Trash',
                'parent' => 'Parent Performer'
            ),
 
            'public' => true,
            'menu_position' => 5,
            'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),
            'taxonomies' => array( '' ),
            'menu_icon' => 'dashicons-smiley',
            'has_archive' => true
        )
    );
}

// Creating Meta Box / Registering Custom Function
add_action( 'admin_init', 'my_admin' );

// Implement Custom Function
function my_admin() {
    add_meta_box( 'featured_performer_meta_box',
        'Performer Details',
        'display_featured_performer_meta_box',
        'featured_performer', 'normal', 'high'
    );
}

// Implement display_featured_performer_meta_box Function
function display_featured_performer_meta_box( $performer ) {
    // Retrieve current name of the Director and Movie Rating based on review ID
    $performer_manager = esc_html( get_post_meta( $performer->ID, 'performer_manager', true ) );
    $performer_rating = intval( get_post_meta( $performer->ID, 'performer_rating', true ) );
    ?>
    <table>
        <tr>
            <td style="width: 100%">Performer Manager</td>
            <td><input type="text" size="80" name="performer_manager_name" value="<?php echo $performer_manager; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 150px">Performer Rating</td>
            <td>
                <select style="width: 100px" name="performer_rating">
                <?php
                // Generate all items of drop-down list
                for ( $rating = 5; $rating >= 1; $rating -- ) {
                ?>
                    <option value="<?php echo $rating; ?>" <?php echo selected( $rating, $performer_rating ); ?>>
                    <?php echo $rating; ?> stars <?php } ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

// Registering a Save Post Function
add_action( 'save_post', 'add_featured_performer_fields', 10, 2 );

// Implementation of the add_featured_performer_fields Function
function add_featured_performer_fields( $featured_performer_id, $featured_performer ) {
    // Check post type for movie reviews
    if ( $featured_performer->post_type == 'featured_performer' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['performer_manager_name'] ) && $_POST['performer_manager_name'] != '' ) {
            update_post_meta( $featured_performer_id, 'performer_manager', $_POST['performer_manager_name'] );
        }
        if ( isset( $_POST['performer_rating'] ) && $_POST['performer_rating'] != '' ) {
            update_post_meta( $featured_performer_id, 'performer_rating', $_POST['performer_rating'] );
        }
    }
}

// Add Shortcode
add_shortcode('custom_post', 'custPosts');

// Select Custom Posts
function custPosts() {
	$args = array( 'post_type' => 'featured_performer', 'posts_per_page' => 10 );
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post();
		echo '<h2>'; the_title(); echo '</h2>';
		echo '<div class="entry-content">';
		echo '<strong>Manager: </strong>';
		echo esc_html( get_post_meta( get_the_ID(), 'performer_manager', true ) );
		the_content();
		echo '</div>';
	endwhile;
}
?>