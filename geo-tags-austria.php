<?php
/*
   Plugin Name: Geo Tags Austria
   Plugin URI: http://www.tagesgeld.ag/
   Description: WP Geo Tags fuer Oesterreich.
   Version: 1.0
   Author: Frank Kugler
   Author URI: http://www.webarbeit.net/
*/

$key = "wpgeotags";
$meta_boxes = array(
    "region" => array(
        "name" => "region",
        "title" => "geo.region",
        "description" => "Example: <em>AT-6</em>"),
    "placename" => array(
        "name" => "placename",
        "title" => "geo.placename",
        "description" => "Example: <em>Graz</em>"),
    "position" => array(
        "name" => "position",
        "title" => "geo.position",
        "description" => "Example: <em>47.0667;15.4500</em>"),
    "ICBM" => array(
        "name" => "ICBM",
        "title" => "ICBM",
        "description" => "Example: <em>47.0667, 15.4500</em>")
    );

function create_meta_box()
{
    global $key;

    if (function_exists('add_meta_box')) {
        add_meta_box('new-meta-boxes', ucfirst($key) . ' Custom Post Options', 'display_meta_box', 'post', 'normal', 'high');
        add_meta_box('new-meta-boxes', ucfirst($key) . ' Custom Post Options', 'display_meta_box', 'page', 'normal', 'high');
    }
}

function display_meta_box()
{
    global $post, $meta_boxes, $key;

    ?>

<div class="form-wrap">

<?php
    wp_nonce_field(plugin_basename(__FILE__), $key . '_wpnonce', false, true);

    foreach($meta_boxes as $meta_box) {
        $data = get_post_meta($post->ID, $key, true);

        ?>

<div class="form-field form-required">
<label for="<?php echo $meta_box[ 'name' ]; ?>"><?php echo $meta_box[ 'title' ]; ?></label>
<input type="text" name="<?php echo $meta_box[ 'name' ]; ?>" value="<?php echo htmlspecialchars($data[ $meta_box[ 'name' ] ]); ?>" />
<p><?php echo $meta_box[ 'description' ]; ?></p>
</div>

<?php } ?>

</div>
<?php
}

function save_meta_box($post_id)
{
    global $post, $meta_boxes, $key;

    foreach($meta_boxes as $meta_box) {
        $data[ $meta_box[ 'name' ] ] = $_POST[ $meta_box[ 'name' ] ];
    }

    if (!wp_verify_nonce($_POST[ $key . '_wpnonce' ], plugin_basename(__FILE__)))
        return $post_id;

    if (!current_user_can('edit_post', $post_id))
        return $post_id;

    update_post_meta($post_id, $key, $data);
}

add_action('admin_menu', 'create_meta_box');
add_action('save_post', 'save_meta_box');

function wpgeotags_head_meta()
{
    global $wp_query;
    if (is_single() or is_page()) {
        if ($wp_query->post) {
            $post = $wp_query->post;
            $data = get_post_meta($post->ID, 'wpgeotags', true);
            if ($data[ 'region' ] != "") {
                echo '<meta name="geo.region" content="' . $data[ 'region' ] . '" />' . "\n";
            }
            if ($data[ 'placename' ] != "") {
                echo '<meta name="geo.placename" content="' . $data[ 'placename' ] . '" />' . "\n";
            }
            if ($data[ 'position' ] != "") {
                echo '<meta name="geo.position" content="' . $data[ 'position' ] . '" />' . "\n";
            }
            if ($data[ 'ICBM' ] != "") {
                echo '<meta name="ICBM" content="' . $data[ 'ICBM' ] . '" />' . "\n";
            }
        }
    }
}

add_action('wp_head', 'wpgeotags_head_meta');

?>