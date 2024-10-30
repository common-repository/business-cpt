<?php
/**
* Plugin Name: Business CPT
* Plugin URI: https://www.inforte.no
* Description: Adds custom post types to extend the core functionality of WordPress. Created for themes by Inforte AS.
* Version: 1.4
* Author: Torbjørn Kristensen (Inforte AS)
* Author URI: https://www.inforte.no/#tf-team
* License: GPL2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: wporg
* Domain Path: /languages
*/

/*
{business_cpt} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

{business_cpt} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with {business_cpt}. If not, see {gpl-2}.
*/

// Blocking direct access to our plugin
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* Hook into the 'init' action so that the functions
* Containing our post types registrations is not
* unnecessarily executed.
*/
add_action( 'init', 'ib_setup_employee' );
add_action( 'init', 'ib_setup_service' );
add_action( 'init', 'ib_setup_product' );
add_action( 'init', 'ib_setup_project' );
add_action( 'init', 'ib_setup_shipyard' );
add_action( 'init', 'ib_setup_vessel' );

// Hook in alle mataboxene
add_action( 'add_meta_boxes', 'employee_information_box' );
add_action( 'save_post', 'employee_information_box_save' );
add_action( 'add_meta_boxes', 'services_information_box' );
add_action( 'save_post', 'services_information_box_save' );
add_action( 'add_meta_boxes', 'products_information_box' );
add_action( 'save_post', 'products_information_box_save' );

/* Hook inn på 'changetitletext' filteret, så vi kan bytte title teksten. */
add_filter('gettext', 'employees_title_text');
add_filter('gettext', 'services_title_text');
add_filter('gettext', 'product_title_text');
add_filter('gettext', 'project_title_text');
add_filter('gettext', 'shipyard_title_text');
add_filter('gettext', 'vessel_title_text');

// Registering activation/deactivation hooks

function ib_install() {
  // trigger our function that registers the custom post type
  ib_setup_employee();
  ib_setup_product();
  ib_setup_service();
  ib_setup_project();
  ib_setup_shipyard();
  ib_setup_vessel();

  // clear the permalinks after the post type has been registered
  flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ib_install' );

function ib_deactivation() {
  // our post type will be automatically removed, so no need to unregister it

  // clear the permalinks to remove our post type's rules
  flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ib_deactivation' );

/*
* Function for creating our custom post types for employees
*/

if ( ! function_exists('ib_setup_employee') ) {

  // Register Custom Post Type
  function ib_setup_employee() {

    $labels = array(
      'name'                  => _x( 'Employees', 'Post Type General Name', 'text_domain' ),
      'singular_name'         => _x( 'Employee', 'Post Type Singular Name', 'text_domain' ),
      'menu_name'             => __( 'Employees', 'text_domain' ),
      'name_admin_bar'        => __( 'Employee', 'text_domain' ),
      'archives'              => __( 'Employee Archives', 'text_domain' ),
      'parent_item_colon'     => __( 'Parent Employee:', 'text_domain' ),
      'all_items'             => __( 'All Employees', 'text_domain' ),
      'add_new_item'          => __( 'Add New Employee', 'text_domain' ),
      'add_new'               => __( 'Add New', 'text_domain' ),
      'new_item'              => __( 'New Employee', 'text_domain' ),
      'edit_item'             => __( 'Edit Employee', 'text_domain' ),
      'update_item'           => __( 'Update Employee', 'text_domain' ),
      'view_item'             => __( 'View Employee', 'text_domain' ),
      'search_items'          => __( 'Search Employee', 'text_domain' ),
      'not_found'             => __( 'Not found', 'text_domain' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
      'featured_image'        => __( 'Featured Image', 'text_domain' ),
      'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
      'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
      'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
      'insert_into_item'      => __( 'Insert into employee', 'text_domain' ),
      'uploaded_to_this_item' => __( 'Uploaded to this employee', 'text_domain' ),
      'items_list'            => __( 'Employees list', 'text_domain' ),
      'items_list_navigation' => __( 'Employees list navigation', 'text_domain' ),
      'filter_items_list'     => __( 'Filter employees list', 'text_domain' ),
    );
    $rewrite = array(
      'slug'                  => 'employee',
      'with_front'            => true,
      'pages'                 => true,
      'feeds'                 => true,
    );
    $args = array(
      'label'                 => __( 'Employee', 'text_domain' ),
      'description'           => __( 'Custom Post Type - Employee', 'text_domain' ),
      'labels'                => $labels,
      'supports'              => array( 'title', 'editor', 'thumbnail', ),
      'taxonomies'            => array( 'category', 'post_tag' ),
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 5,
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => true,
      'exclude_from_search'   => false,
      'publicly_queryable'    => true,
      'rewrite'               => $rewrite,
      'capability_type'       => 'page',
    );
    register_post_type( 'ib_employee', $args );

  }
  add_action( 'init', 'ib_setup_employee', 0 );

}

function employee_information_box() {
  add_meta_box(
    'employee_information_box',
    __( 'Employee Information', 'myplugin_textdomain' ),
    'employee_information_box_content',
    'ib_employee',
    'normal',
    'high'
  );
}

function employee_information_box_content( $post ) {

  wp_nonce_field( plugin_basename( __FILE__ ), 'employee_information_box_content_nonce' );

  //so, dont ned to use esc_attr in front of get_post_meta
  $valueeee2=  get_post_meta($_GET['post'], 'SMTH_METANAME_VALUE' , true ) ;
  wp_editor( htmlspecialchars_decode($valueeee2), 'mettaabox_ID_stylee', $settings = array('textarea_name'=>'MyInputNAME') );

  echo '<h3>Role</h3>';
  echo '<label for="employee_role"></label>';
  echo '<input type="text" id="employee_role" name="employee_role" placeholder="enter a role"';
  echo 'value="';
  echo get_post_meta( get_the_ID(), 'employee_role', true );
  echo '" />';

  echo '<h3>Mail</h3>';
  echo '<label for="employee_mail"></label>';
  echo '<input type="text" id="employee_mail" name="employee_mail" placeholder="enter a mail"';
  echo 'value="';
  echo get_post_meta( get_the_ID(), 'employee_mail', true );
  echo '" />';

  echo '<h3>Phone Number</h3>';
  echo '<label for="employee_phone"></label>';
  echo '<input type="text" id="employee_phone" name="employee_phone" placeholder="enter a phone-number"';
  echo 'value="';
  echo get_post_meta( get_the_ID(), 'employee_phone', true );
  echo '" />';

  echo '<h3>Work</h3>';
  echo '<label for="employee_work"></label>';
  echo '<input type="text" id="employee_work" name="employee_work" placeholder="enter a work"';
  echo 'value="';
  echo get_post_meta( get_the_ID(), 'employee_work', true );
  echo '" />';
}


function employee_information_box_save( $post_id ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
  return;

  if ( !wp_verify_nonce( $_POST['employee_information_box_content_nonce'], plugin_basename( __FILE__ ) ) )
  return;

  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ) )
    return;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ) )
    return;
  }
  $employee_role = $_POST['employee_role'];
  update_post_meta( $post_id, 'employee_role', $employee_role );
  $employee_mail = $_POST['employee_mail'];
  update_post_meta( $post_id, 'employee_mail', $employee_mail );
  $employee_phone = $_POST['employee_phone'];
  update_post_meta( $post_id, 'employee_phone', $employee_phone );
  $product_work = $_POST['employee_work'];
  update_post_meta( $post_id, 'employee_work', $product_work );
}

function employees_title_text( $input ) {

  global $post_type;

  if( is_admin() && 'Enter title here' == $input && 'ib_employee' == $post_type )
  return 'Enter employee name';

  return $input;
}

/*
* Function for creating our custom post types for services
*/

if ( ! function_exists('ib_setup_service') ) {

  // Register Custom Post Type
  function ib_setup_service() {

    $labels = array(
      'name'                  => _x( 'Services', 'Post Type General Name', 'text_domain' ),
      'singular_name'         => _x( 'Service', 'Post Type Singular Name', 'text_domain' ),
      'menu_name'             => __( 'Services', 'text_domain' ),
      'name_admin_bar'        => __( 'Service', 'text_domain' ),
      'archives'              => __( 'Service Archives', 'text_domain' ),
      'parent_item_colon'     => __( 'Parent Service:', 'text_domain' ),
      'all_items'             => __( 'All Services', 'text_domain' ),
      'add_new_item'          => __( 'Add New Service', 'text_domain' ),
      'add_new'               => __( 'Add New', 'text_domain' ),
      'new_item'              => __( 'New Service', 'text_domain' ),
      'edit_item'             => __( 'Edit Service', 'text_domain' ),
      'update_item'           => __( 'Update Service', 'text_domain' ),
      'view_item'             => __( 'View Service', 'text_domain' ),
      'search_items'          => __( 'Search Service', 'text_domain' ),
      'not_found'             => __( 'Not found', 'text_domain' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
      'featured_image'        => __( 'Featured Image', 'text_domain' ),
      'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
      'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
      'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
      'insert_into_item'      => __( 'Insert into service', 'text_domain' ),
      'uploaded_to_this_item' => __( 'Uploaded to this service', 'text_domain' ),
      'items_list'            => __( 'Services list', 'text_domain' ),
      'items_list_navigation' => __( 'Services list navigation', 'text_domain' ),
      'filter_items_list'     => __( 'Filter services list', 'text_domain' ),
    );
    $rewrite = array(
      'slug'                  => 'service',
      'with_front'            => true,
      'pages'                 => true,
      'feeds'                 => true,
    );
    $args = array(
      'label'                 => __( 'Service', 'text_domain' ),
      'description'           => __( 'Custom Post Type - Service', 'text_domain' ),
      'labels'                => $labels,
      'supports'              => array( 'title', 'editor', 'thumbnail', ),
      'taxonomies'            => array( 'category', 'post_tag' ),
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 5,
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => true,
      'exclude_from_search'   => false,
      'publicly_queryable'    => true,
      'rewrite'               => $rewrite,
      'capability_type'       => 'page',
    );
    register_post_type( 'ib_service', $args );

  }
  add_action( 'init', 'ib_setup_service', 0 );

}

function services_information_box() {
  add_meta_box(
    'services_information_box',
    __( 'Services Information', 'myplugin_textdomain' ),
    'services_information_box_content',
    'ib_service',
    'normal',
    'high'
  );
}

function services_information_box_content( $post ) {
  wp_nonce_field( plugin_basename( __FILE__ ), 'services_information_box_content_nonce' );

  echo '<h3>Service Description</h3>';
  echo '<label for="service_description"></label>';
  echo '<input type="text" id="service_description" name="service_description" placeholder="enter a description for your service"';
  echo 'value="';
  echo get_post_meta( get_the_ID(), 'service_description', true );
  echo '" />';

  echo '<h3>Service Featured</h3>';
  echo '<label for="service_featured"></label>';
  echo '<input type="text" id="service_featured" name="service_featured" placeholder="enter yes/no"';
  echo 'value="';
  echo get_post_meta( get_the_ID(), 'service_featured', true );
  echo '" />';
}

function services_information_box_save( $post_id ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
  return;

  if ( !wp_verify_nonce( $_POST['services_information_box_content_nonce'], plugin_basename( __FILE__ ) ) )
  return;

  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ) )
    return;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ) )
    return;
  }
  $service_description = $_POST['service_description'];
  update_post_meta( $post_id, 'service_description', $service_description );
  $service_featured = $_POST['service_featured'];
  update_post_meta( $post_id, 'service_featured', $service_featured );
}

function services_title_text( $input ) {

  global $post_type;

  if( is_admin() && 'Enter title here' == $input && 'ib_service' == $post_type )
  return 'Enter service name';

  return $input;
}

/*
* Function for creating our products CPT
*/

if ( ! function_exists('ib_setup_product') ) {

  // Register Custom Post Type
  function ib_setup_product() {

    $labels = array(
      'name'                  => _x( 'Products', 'Post Type General Name', 'text_domain' ),
      'singular_name'         => _x( 'Product', 'Post Type Singular Name', 'text_domain' ),
      'menu_name'             => __( 'Products', 'text_domain' ),
      'name_admin_bar'        => __( 'Product', 'text_domain' ),
      'archives'              => __( 'Product Archives', 'text_domain' ),
      'parent_item_colon'     => __( 'Parent Product:', 'text_domain' ),
      'all_items'             => __( 'All Products', 'text_domain' ),
      'add_new_item'          => __( 'Add New Product', 'text_domain' ),
      'add_new'               => __( 'Add New', 'text_domain' ),
      'new_item'              => __( 'New Product', 'text_domain' ),
      'edit_item'             => __( 'Edit Product', 'text_domain' ),
      'update_item'           => __( 'Update Product', 'text_domain' ),
      'view_item'             => __( 'View Product', 'text_domain' ),
      'search_items'          => __( 'Search Product', 'text_domain' ),
      'not_found'             => __( 'Not found', 'text_domain' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
      'featured_image'        => __( 'Featured Image', 'text_domain' ),
      'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
      'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
      'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
      'insert_into_item'      => __( 'Insert into product', 'text_domain' ),
      'uploaded_to_this_item' => __( 'Uploaded to this product', 'text_domain' ),
      'items_list'            => __( 'Products list', 'text_domain' ),
      'items_list_navigation' => __( 'Products list navigation', 'text_domain' ),
      'filter_items_list'     => __( 'Filter products list', 'text_domain' ),
    );
    $rewrite = array(
      'slug'                  => 'product',
      'with_front'            => true,
      'pages'                 => true,
      'feeds'                 => true,
    );
    $args = array(
      'label'                 => __( 'Product', 'text_domain' ),
      'description'           => __( 'Custom Post Type - Product', 'text_domain' ),
      'labels'                => $labels,
      'supports'              => array( 'title', 'editor', 'thumbnail', ),
      'taxonomies'            => array( 'category', 'post_tag' ),
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 5,
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => true,
      'exclude_from_search'   => false,
      'publicly_queryable'    => true,
      'rewrite'               => $rewrite,
      'capability_type'       => 'page',
    );
    register_post_type( 'ib_product', $args );

  }
  add_action( 'init', 'ib_setup_product', 0 );

}

function products_information_box() {
  add_meta_box(
    'products_information_box',
    __( 'Product Information', 'myplugin_textdomain' ),
    'products_information_box_content',
    'ib_product',
    'normal',
    'high'
  );
}

function products_information_box_content( $post ) {
  wp_nonce_field( plugin_basename( __FILE__ ), 'products_information_box_content_nonce' );
  echo '<h3>Title Link</h3>';
  echo '<label for="title_link"></label>';
  echo '<input type="text" id="title_link" name="title_link" placeholder="enter a link for when the user wants to look at the products"';
  echo 'value="';
  echo get_post_meta( get_the_ID(), 'title_link', true );
  echo '" />';

  echo '<h3>Product Description</h3>';
  echo '<label for="product_description"></label>';
  echo '<input type="text" id="product_description" name="product_description" placeholder="enter a name for your product"';
  echo 'value="';
  echo get_post_meta( get_the_ID(), 'product_description', true );
  echo '" />';
}


function products_information_box_save( $post_id ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
  return;

  if ( !wp_verify_nonce( $_POST['products_information_box_content_nonce'], plugin_basename( __FILE__ ) ) )
  return;

  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ) )
    return;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ) )
    return;
  }
  $title_link = $_POST['title_link'];
  update_post_meta( $post_id, 'title_link', $title_link );
  $product_description = $_POST['product_description'];
  update_post_meta( $post_id, 'product_description', $product_description );
}

function product_title_text( $input ) {

  global $post_type;

  if( is_admin() && 'Enter title here' == $input && 'ib_product' == $post_type )
  return 'Enter product name';

  return $input;
}

/*
* Function for creating our projects CPT
* TODO - Gjøre dette til en Octagone AS håndlagd CPT og kalle den sites/projects
*/

if ( ! function_exists('ib_setup_project') ) {

  // Register Custom Post Type
  function ib_setup_project() {

    $labels = array(
      'name'                  => _x( 'Projects', 'Post Type General Name', 'text_domain' ),
      'singular_name'         => _x( 'Project', 'Post Type Singular Name', 'text_domain' ),
      'menu_name'             => __( 'Projects', 'text_domain' ),
      'name_admin_bar'        => __( 'Project', 'text_domain' ),
      'archives'              => __( 'Project Archives', 'text_domain' ),
      'parent_item_colon'     => __( 'Parent Project:', 'text_domain' ),
      'all_items'             => __( 'All Projects', 'text_domain' ),
      'add_new_item'          => __( 'Add New Project', 'text_domain' ),
      'add_new'               => __( 'Add New', 'text_domain' ),
      'new_item'              => __( 'New Project', 'text_domain' ),
      'edit_item'             => __( 'Edit Project', 'text_domain' ),
      'update_item'           => __( 'Update Project', 'text_domain' ),
      'view_item'             => __( 'View Project', 'text_domain' ),
      'search_items'          => __( 'Search Project', 'text_domain' ),
      'not_found'             => __( 'Not found', 'text_domain' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
      'featured_image'        => __( 'Featured Image', 'text_domain' ),
      'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
      'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
      'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
      'insert_into_item'      => __( 'Insert into project', 'text_domain' ),
      'uploaded_to_this_item' => __( 'Uploaded to this project', 'text_domain' ),
      'items_list'            => __( 'Projects list', 'text_domain' ),
      'items_list_navigation' => __( 'Projects list navigation', 'text_domain' ),
      'filter_items_list'     => __( 'Filter projects list', 'text_domain' ),
    );
    $rewrite = array(
      'slug'                  => 'project',
      'with_front'            => true,
      'pages'                 => true,
      'feeds'                 => true,
    );
    $args = array(
      'label'                 => __( 'Project', 'text_domain' ),
      'description'           => __( 'Custom Post Type - Project', 'text_domain' ),
      'labels'                => $labels,
      'supports'              => array( 'title', 'editor', 'thumbnail', ),
      'taxonomies'            => array( 'category', 'post_tag' ),
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 5,
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => true,
      'exclude_from_search'   => false,
      'publicly_queryable'    => true,
      'rewrite'               => $rewrite,
      'capability_type'       => 'page',
    );
    register_post_type( 'ib_project', $args );

  }
  add_action( 'init', 'ib_setup_project', 0 );

}

function project_title_text( $input ) {

  global $post_type;

  if( is_admin() && 'Enter title here' == $input && 'ib_project' == $post_type )
  return 'Enter project name';

  return $input;
}

/*
* Function for creating our shipyard CPT
*/

if ( ! function_exists('ib_setup_shipyard') ) {

  // Register Custom Post Type
  function ib_setup_shipyard() {

    $labels = array(
      'name'                  => _x( 'Shipyards', 'Post Type General Name', 'text_domain' ),
      'singular_name'         => _x( 'Shipyard', 'Post Type Singular Name', 'text_domain' ),
      'menu_name'             => __( 'Shipyards', 'text_domain' ),
      'name_admin_bar'        => __( 'Shipyard', 'text_domain' ),
      'archives'              => __( 'Shipyard Archives', 'text_domain' ),
      'parent_item_colon'     => __( 'Parent Shipyard:', 'text_domain' ),
      'all_items'             => __( 'All Shipyards', 'text_domain' ),
      'add_new_item'          => __( 'Add New Shipyard', 'text_domain' ),
      'add_new'               => __( 'Add New', 'text_domain' ),
      'new_item'              => __( 'New Shipyard', 'text_domain' ),
      'edit_item'             => __( 'Edit Shipyard', 'text_domain' ),
      'update_item'           => __( 'Update Shipyard', 'text_domain' ),
      'view_item'             => __( 'View Shipyard', 'text_domain' ),
      'search_items'          => __( 'Search Shipyard', 'text_domain' ),
      'not_found'             => __( 'Not found', 'text_domain' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
      'featured_image'        => __( 'Featured Image', 'text_domain' ),
      'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
      'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
      'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
      'insert_into_item'      => __( 'Insert into shipyard', 'text_domain' ),
      'uploaded_to_this_item' => __( 'Uploaded to this shipyard', 'text_domain' ),
      'items_list'            => __( 'Shipyards list', 'text_domain' ),
      'items_list_navigation' => __( 'Shipyards list navigation', 'text_domain' ),
      'filter_items_list'     => __( 'Filter shipyards list', 'text_domain' ),
    );
    $rewrite = array(
      'slug'                  => 'shipyard',
      'with_front'            => true,
      'pages'                 => true,
      'feeds'                 => true,
    );
    $args = array(
      'label'                 => __( 'Shipyard', 'text_domain' ),
      'description'           => __( 'Custom Post Type - Shipyard', 'text_domain' ),
      'labels'                => $labels,
      'supports'              => array( 'title', 'editor', 'thumbnail', ),
      'taxonomies'            => array( 'category', 'post_tag' ),
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 5,
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => true,
      'exclude_from_search'   => false,
      'publicly_queryable'    => true,
      'rewrite'               => $rewrite,
      'capability_type'       => 'page',
    );
    register_post_type( 'ib_shipyard', $args );

  }
  add_action( 'init', 'ib_setup_shipyard', 0 );

}

function shipyard_title_text( $input ) {

  global $post_type;

  if( is_admin() && 'Enter title here' == $input && 'ib_shipyard' == $post_type )
  return 'Enter shipyard name';

  return $input;
}

/*
* Function for creating our custom post types for vessels
*/

if ( ! function_exists('ib_setup_vessel') ) {

  // Register Custom Post Type
  function ib_setup_vessel() {

    $labels = array(
      'name'                  => _x( 'Vessels', 'Post Type General Name', 'text_domain' ),
      'singular_name'         => _x( 'Vessel', 'Post Type Singular Name', 'text_domain' ),
      'menu_name'             => __( 'Vessels', 'text_domain' ),
      'name_admin_bar'        => __( 'Vessel', 'text_domain' ),
      'archives'              => __( 'Vessel Archives', 'text_domain' ),
      'parent_item_colon'     => __( 'Parent Vessel:', 'text_domain' ),
      'all_items'             => __( 'All Vessels', 'text_domain' ),
      'add_new_item'          => __( 'Add New Vessel', 'text_domain' ),
      'add_new'               => __( 'Add New', 'text_domain' ),
      'new_item'              => __( 'New Vessel', 'text_domain' ),
      'edit_item'             => __( 'Edit Vessel', 'text_domain' ),
      'update_item'           => __( 'Update Vessel', 'text_domain' ),
      'view_item'             => __( 'View Vessel', 'text_domain' ),
      'search_items'          => __( 'Search Vessel', 'text_domain' ),
      'not_found'             => __( 'Not found', 'text_domain' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
      'featured_image'        => __( 'Featured Image', 'text_domain' ),
      'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
      'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
      'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
      'insert_into_item'      => __( 'Insert into vessel', 'text_domain' ),
      'uploaded_to_this_item' => __( 'Uploaded to this vessel', 'text_domain' ),
      'items_list'            => __( 'Vessels list', 'text_domain' ),
      'items_list_navigation' => __( 'Vessels list navigation', 'text_domain' ),
      'filter_items_list'     => __( 'Filter vessels list', 'text_domain' ),
    );
    $rewrite = array(
      'slug'                  => 'vessel',
      'with_front'            => true,
      'pages'                 => true,
      'feeds'                 => true,
    );
    $args = array(
      'label'                 => __( 'Vessel', 'text_domain' ),
      'description'           => __( 'Custom Post Type - Vessel', 'text_domain' ),
      'labels'                => $labels,
      'supports'              => array( 'title', 'editor', 'thumbnail', ),
      'taxonomies'            => array( 'category', 'post_tag' ),
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 5,
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => true,
      'exclude_from_search'   => false,
      'publicly_queryable'    => true,
      'rewrite'               => $rewrite,
      'capability_type'       => 'page',
    );
    register_post_type( 'ib_vessel', $args );

  }
  add_action( 'init', 'ib_setup_vessel', 0 );

}

function vessel_title_text( $input ) {

  global $post_type;

  if( is_admin() && 'Enter title here' == $input && 'ib_vessel' == $post_type )
  return 'Enter vessel name';

  return $input;
}
