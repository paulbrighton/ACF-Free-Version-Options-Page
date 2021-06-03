<?php
/* 
* To add this options page, create a page called Site Settings 
* and a custom field called Site Settings.
* It will be available from the admin sidebar but won't showing 
* in the pages list.
* Also change post id on line 62 in get_post()
*/

// Remove Gutenburg from page
add_filter('use_block_editor_for_post', 'disable_gutenberg_on_settings_page', 5, 2);

function disable_gutenberg_on_settings_page($can, $post)
{
  if ($post) {
    // Replace "site-settings" with the slug of your site settings page.
    if ($post->post_name === "site-settings") {
      return false;
    }
  }
  return $can;
}

// Hide the page in pages panel
function hide_settings_page($query)
{
  if (!is_admin() && !$query->is_main_query()) {
    return;
  }
  global $typenow;
  if ($typenow === "page") {
    // Replace "site-settings" with the slug of your site settings page.
    $settings_page = get_page_by_path("site-settings", NULL, "page")->ID;
    $query->set('post__not_in', array($settings_page));
  }
  return;
}

add_action('pre_get_posts', 'hide_settings_page');

// Add the page to admin menu
function add_site_settings_to_menu()
{
  add_menu_page('Site Settings', 'Site Setttings', 'manage_options', 'post.php?post=' . get_page_by_path("site-settings", NULL, "page")->ID . '&action=edit', '', 'dashicons-edit-large', 20);
}
add_action('admin_menu', 'add_site_settings_to_menu');

// Change the active menu item
add_filter('parent_file', 'higlight_custom_settings_page');

function higlight_custom_settings_page($file)
{
  global $parent_file;
  global $pagenow;
  global $typenow, $self;

  $settings_page = get_page_by_path("site-settings", NULL, "page")->ID;

  /*
  * Change post id in get_post() to set the page for the site settings
  */
  $post = get_post( 86 );
  if ($pagenow === "post.php" && $post === $settings_page) {
    $file = "post.php?post=$settings_page&action=edit";
  }
  return $file;
}

function edit_site_settings_title()
{
  global $post, $title, $action, $current_screen;
  if (isset($current_screen->post_type) && $current_screen->post_type === 'page' && $action == 'edit' && $post->post_name === "site-settings") {
    $title = $post->post_title . ' - ' . get_bloginfo('name');
  }
  return $title;
}

add_action('admin_title', 'edit_site_settings_title');
