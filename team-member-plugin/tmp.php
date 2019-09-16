<?php
/*
Plugin Name: createIT Recruitment plugin
Plugin URI:
Description: Team member Plugin
Author: Gabriel Koziestański
Version: 1.1
 */

add_action('init', 'tmp_team_member_init');

function tmp_team_member_init()
{
    $labels = array(
        'name' => _x('Team members', 'post type general name', 'team_member'),
        'singular_name' => _x('Team member', 'post type singular name', 'team_member'),
        'menu_name' => _x('Team members', 'admin menu', 'team_member'),
        'name_admin_bar' => _x('Team member', 'add new on admin bar', 'team_member'),
        'add_new' => _x('Add New', 'team member', 'team_member'),
        'add_new_item' => __('Add new team member', 'team_member'),
        'new_item' => __('New team member', 'team_member'),
        'edit_item' => __('Edit team member', 'team_member'),
        'view_item' => __('View team member', 'team_member'),
        'all_items' => __('All team members', 'team_member'),
        'search_items' => __('Search team members', 'team_member'),
        'parent_item_colon' => __('Parent team members:', 'team_member'),
        'not_found' => __('No team members found.', 'team_member'),
        'not_found_in_trash' => __('No team members found in Trash.', 'team_member'),
    );

    $args = array(
        'labels' => $labels,
        'description' => __('Description.', 'team_member'),
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'team_member'),
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'thumbnail'),
    );

    register_post_type('team_member', $args);
}

function tmp_shortcode($atts = [])
{
    $atts = array_change_key_case((array) $atts, CASE_LOWER);
    $size = 'post-thumbnail';
    $args = array(
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'post_type' => 'team_member',
    );

    if (isset($atts['order']) && in_array($atts['order'], ['ASC', 'DESC'])) {
        $args['order'] = $atts['order'];
    }

    if (isset($atts['limit'])) {
        $limit = (int) $atts['limit'];
        if ($limit > 0) {
            $args['posts_per_page'] = $limit;
        }
    }

    if (isset($atts['size'])) {
        global $_wp_additional_image_sizes;
        if (array_key_exists($atts['size'], $_wp_additional_image_sizes)) {
            $size = $atts['size'];
        }
    }

    $the_query = new WP_Query($args);

    $result = "";

    if ($the_query->have_posts()) {
        $result .= '<ul class="tmp">';
        while ($the_query->have_posts()) {
            $the_query->the_post();

            $thumbnile = has_post_thumbnail(get_the_ID());

            $result .= '<li class="tmp__element"><div class="tmp__element__name ' . ($thumbnile ?: "tmp__element__name--no-image") . '">' . get_the_title() . '</div>';
            if ($thumbnile) {
                $result .= '<figure class="tmp__element__figure"><img class="tmp__element__figure__img" src="' . get_the_post_thumbnail_url(get_the_ID(), $size) . '" alt="' . get_the_title() . '"></figure>';
            }

        }
        $result .= '</ul>';
    }
    wp_reset_postdata();
    return $result;
}

function tmp_shortcodes_init()
{
    add_shortcode('ct_team', 'tmp_shortcode');
}

add_action('init', 'tmp_shortcodes_init');

function tmp_scripts()
{
    wp_register_style('tmp', plugins_url('/css/style.css', __FILE__), false, null);
    wp_enqueue_style('tmp');
}

add_action('wp_enqueue_scripts', 'tmp_scripts');