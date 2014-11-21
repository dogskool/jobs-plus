<?php

// +----------------------------------------------------------------------+
// | Copyright Incsub (http://incsub.com/)                                |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+

/**
 * PostType module.
 *
 * This module use for register custom content
 *
 * @category JobsExperts
 * @package  Module
 *
 * @since    1.0.0
 */
class JobsExperts_Core_CustomContent extends JobsExperts_Framework_Module
{
	const NAME = __CLASS__;


	/**
	 * The jobs custom post type object
	 *
	 * @since 1.0.0
	 *
	 */
	public $jobs;

	/**
	 * The experts custom post type object
	 *
	 * @since 1.0.0
	 *
	 */
	public $experts;


	public function __construct()
	{
		$this->_add_action('init', 'load_custom_post_types', 1);
		$this->_add_action('init', 'load_custom_taxonomies', 1);
		$this->_add_action('init', 'custom_post_status', 1);

		//load the post type object data
		$this->jobs = get_post_type_object('jbp_job');
		$this->experts = get_post_type_object('jbp_pro');
		//register all scripts
		$this->_add_action('wp_enqueue_scripts', 'scripts');
		$this->_add_action('admin_enqueue_scripts', 'scripts');

		$this->_add_action('wp_enqueue_scripts', 'determine_css', 9999);
	}

	function determine_css()
	{
		$shortcodes = array(
			'jbp-expert-post-btn' => 'jobs-buttons-shortcode',
			'jbp-job-post-btn' => 'jobs-buttons-shortcode',
			'jbp-job-browse-btn' => 'jobs-buttons-shortcode',
			'jbp-expert-profile-btn' => 'jobs-buttons-shortcode',
			'jbp-my-job-btn' => 'jobs-buttons-shortcode',
			'jbp-expert-browse-btn' => 'jobs-buttons-shortcode',
			'jbp-expert-contact-page',
			'jbp-landing-page' => array(
				'jobs-landing-shortcode',
				'expert-list-shortcode',
				'jobs-list-shortcode'
			),
			'jbp-job-update-page' => 'jobs-form-shortcode',
			'jbp-job-archive-page',
			'jbp-job-contact-page',
			'jbp-my-job-page',
			'jbp-expert-update-page' => 'expert-form-shortcode',
			'jbp-expert-archive-page',
			'jbp-expert-contact-page',
			'jbp-my-expert-page',
			''
		);
		$plugin = JobsExperts_Plugin::instance();
		$page_module = $plugin->page_module();
		global $post;
		if ($post) {
			if ($page_module->is_core_page($post->ID)) {
				foreach ($shortcodes as $shortcode => $q) {
					$has = preg_match_all('/\[' . $shortcode . '\]/s', $post->post_content, $matches);
					if ($has == 1) {
						if (is_array($q)) {
							foreach ($q as $v) {
								wp_enqueue_style($v);
							}
						} else {
							wp_enqueue_style($q);
						}
					}
				}
			} elseif (is_single() && $post->post_type == 'jbp_job') {
				wp_enqueue_style('jobs-single-shortcode');
			} elseif (is_single() && $post->post_type == 'jbp_pro') {
				wp_enqueue_style('expert-single-shortcode');
			} elseif (is_post_type_archive('jbp_job') || is_tax('jbp_category') || is_tax('jbp_skills_tag')) {
				wp_enqueue_style('jobs-list-shortcode');
			} elseif (is_post_type_archive('jbp_pro')) {
				wp_enqueue_style('expert-list-shortcode');
			}
		}
	}

	function scripts()
	{
		$plugin = JobsExperts_Plugin::instance();
		//style
		wp_enqueue_style('jobs-bootstrap', $plugin->_module_url . 'assets/bootstrap/css/bootstrap-with-namespace.css', array(), JBP_VERSION);
		wp_enqueue_style('jobs-main', $plugin->_module_url . 'assets/main.css', array(), JBP_VERSION);
		wp_register_style('jobs-buttons-shortcode', $plugin->_module_url . 'assets/buttons.css', array(), JBP_VERSION);
		wp_register_style('jobs-single-shortcode', $plugin->_module_url . 'assets/jobs-single.css', array(), JBP_VERSION);
		wp_register_style('jobs-form-shortcode', $plugin->_module_url . 'assets/jobs-form.css', array(), JBP_VERSION);
		wp_register_style('expert-form-shortcode', $plugin->_module_url . 'assets/expert-form.css', array(), JBP_VERSION);
		wp_register_style('expert-single-shortcode', $plugin->_module_url . 'assets/expert-single.css', array(), JBP_VERSION);
		wp_register_style('jobs-list-shortcode', $plugin->_module_url . 'assets/jobs-list.css', array(), JBP_VERSION);
		wp_register_style('expert-list-shortcode', $plugin->_module_url . 'assets/expert-list.css', array(), JBP_VERSION);
		wp_register_style('jobs-contact', $plugin->_module_url . 'assets/contact.css', array(), JBP_VERSION);
		wp_register_style('jobs-landing-shortcode', $plugin->_module_url . 'assets/landing.css', array(), JBP_VERSION);
		wp_register_style('job-plus-widgets', $plugin->_module_url . 'assets/widget.css', array(), JBP_VERSION);
		//js
		wp_enqueue_script('jobs-responsedjs', $plugin->_module_url . 'assets/respond.js', array('jquery'), JBP_VERSION);
		wp_enqueue_script('jobs-html5-shiv', $plugin->_module_url . 'assets/html5shiv.js', array('jobs-modern'), JBP_VERSION);
		//wp_enqueue_script('jobs-modern', $plugin->_module_url . 'assets/modernizr.js', array(), JBP_VERSION);
		wp_enqueue_script('jobs-main', $plugin->_module_url . 'assets/main.js', array('jquery'), JBP_VERSION, true);
		wp_enqueue_script('jobs-bootstrap', $plugin->_module_url . 'assets/bootstrap/js/bootstrap.min.js', array('jquery'), JBP_VERSION, true);

		//plugin
		wp_register_script('jobs-validation', $plugin->_module_url . 'assets/vendors/jquery-validation-engine/js/jquery.validationEngine.js', array('jquery'), JBP_VERSION, true);
		wp_register_script('jobs-validation-en', $plugin->_module_url . 'assets/vendors/jquery-validation-engine/js/languages/jquery.validationEngine-en.js', array('jquery'), JBP_VERSION, true);
		wp_register_style('jobs-validation', $plugin->_module_url . 'assets/vendors/jquery-validation-engine/css/validationEngine.jquery.css', array(), JBP_VERSION);

		wp_register_style('jobs-datepicker', $plugin->_module_url . 'assets/vendors/datepicker/css/datepicker.css', array(), JBP_VERSION);
		wp_register_script('jobs-datepicker', $plugin->_module_url . 'assets/vendors/datepicker/js/bootstrap-datepicker.js', array('jquery', 'jobs-bootstrap'), JBP_VERSION, true);

		wp_register_script('jobs-select2', $plugin->_module_url . 'assets/vendors/select2/select2.min.js');
		wp_register_style('jobs-select2', $plugin->_module_url . 'assets/vendors/select2/select2.css');

		wp_register_script('jobs-noty', $plugin->_module_url . 'assets/vendors/noty/packaged/jquery.noty.packaged.min.js', array(), JBP_VERSION, true);

		///wp_register_script('jobs-ellipsis', $plugin->_module_url . 'assets/vendors/jquery.dotdotdot.min.js', array('jquery'), JBP_VERSION, true);
	}

	function custom_post_status()
	{
		if (is_admin()) {
			register_post_status('virtual', array(
				'label' => __('Virtual', JBP_TEXT_DOMAIN),
				'public' => false,
				'exclude_from_search' => true,
				'show_in_admin_all_list' => false,
				'show_in_admin_status_list' => apply_filters('jpb_virtual_status_show_in_admin_status_list', true),
				'label_count' => _n_noop('Virtual <span class="count">(%s)</span>', 'Virtual <span class="count">(%s)</span>'),
			));
		} else {
			//we allowed this status available on frontend to make it compatibility with other themes
			register_post_status('virtual', array(
				'label' => __('Virtual', JBP_TEXT_DOMAIN),
				'public' => apply_filters('jpb_virtual_status_public', true),
				'exclude_from_search' => true,
				'show_in_admin_all_list' => false,
				'show_in_admin_status_list' => apply_filters('jpb_virtual_status_show_in_admin_status_list', true),
				'label_count' => _n_noop('Virtual <span class="count">(%s)</span>', 'Virtual <span class="count">(%s)</span>'),
			));
		}
	}

	/**
	 * Creates the initial custom post types if they don't already exist.
	 *
	 */
	function load_custom_post_types()
	{
		$plugin = JobsExperts_Plugin::instance();
		/**
		 * Create jbp_job post type
		 */
		if (!post_type_exists('jbp_job')) {
			$jbp_job = array(
				'labels' => array(
					'name' => __('Jobs', JBP_TEXT_DOMAIN),
					'singular_name' => __('Job', JBP_TEXT_DOMAIN),
					'add_new' => __('Add New', JBP_TEXT_DOMAIN),
					'add_new_item' => __('Add New Job', JBP_TEXT_DOMAIN),
					'edit_item' => __('Edit Job', JBP_TEXT_DOMAIN),
					'new_item' => __('New Job', JBP_TEXT_DOMAIN),
					'view_item' => __('View Job', JBP_TEXT_DOMAIN),
					'search_items' => __('Search Jobs', JBP_TEXT_DOMAIN),
					'not_found' => __('No jobs found', JBP_TEXT_DOMAIN),
					'not_found_in_trash' => __('No jobs found in Trash', JBP_TEXT_DOMAIN),
					'custom_fields_block' => __('Jobs Fields', JBP_TEXT_DOMAIN),
				),
				'supports' => array(
					'title' => 'title',
					'editor' => 'editor',
					'author' => 'author',
					'thumbnail' => 'thumbnail',
					'excerpt' => false,
					'custom_fields' => 'custom-fields',
					'revisions' => 'revisions',
					'page_attributes' => 'page-attributes',
				),
				'supports_reg_tax' => array(
					'category' => '',
					'post_tag' => '',
				),
				'capability_type' => 'post',
				'map_meta_cap' => true,
				'description' => __('Job offerings', JBP_TEXT_DOMAIN),
				'menu_position' => '',
				'public' => true,
				'hierarchical' => true,
				'has_archive' => apply_filters('jbp_job_archive_slug', 'jobs'),
				'rewrite' => array(
					'slug' => 'job',
					'with_front' => false,
					'feeds' => true,
					'pages' => true,
					'ep_mask' => 4096,
				),
				'query_var' => true,
				'can_export' => true,
				'cf_columns' => NULL,
				'menu_icon' => $plugin->_module_url . 'assets/image/backend/icons/16px/16px_Jobs_Bright.svg',
			);

			register_post_type('jbp_job', $jbp_job);

		} //jbp_job post type complete

		/**
		 * Create jbp_pro post type
		 */
		if (!post_type_exists('jbp_pro')) {

			$jbp_pro = array(
				'labels' =>
					array(
						'name' => __('Experts', JBP_TEXT_DOMAIN),
						'singular_name' => __('Expert', JBP_TEXT_DOMAIN),
						'add_new' => __('Add New', JBP_TEXT_DOMAIN),
						'add_new_item' => __('Add New Expert', JBP_TEXT_DOMAIN),
						'edit_item' => __('Edit Expert', JBP_TEXT_DOMAIN),
						'new_item' =>__( 'New Expert', JBP_TEXT_DOMAIN),
						'view_item' => __('View Expert', JBP_TEXT_DOMAIN),
						'search_items' => __('Search Expert', JBP_TEXT_DOMAIN),
						'not_found' => __('No experts found', JBP_TEXT_DOMAIN),
						'not_found_in_trash' =>__( 'No experts found in Trash', JBP_TEXT_DOMAIN),
						'custom_fields_block' => __('Expert fields', JBP_TEXT_DOMAIN),
					),
				'supports' =>
					array(
						'title' => 'title',
						'editor' => 'editor',
						'author' => 'author',
						'thumbnail' => 'thumbnail',
						'excerpt' => false,
						'revisions' => 'revisions',
						'post-formats' => 'post-formats'
						//'page_attributes' => 'page-attributes',
					),
				'supports_reg_tax' =>
					array(
						'category' => '',
						'post_tag' => '',
					),
				'capability_type' => 'post',
				'map_meta_cap' => true,
				'description' => __('Expert and extended profile', JBP_TEXT_DOMAIN),
				'menu_position' => '',
				'public' => true,
				'hierarchical' => true,
				'has_archive' => apply_filters('jbp_pro_archive_slug', 'experts'),
				'rewrite' =>
					array(
						'slug' => 'expert',
						'with_front' => false,
						'feeds' => true,
						'pages' => true,
						'ep_mask' => 4096,
					),
				'query_var' => true,
				'can_export' => true,
				'cf_columns' => NULL,
				'menu_icon' => $plugin->_module_url . 'assets/image/backend/icons/16px/16px_Expert_Bright.svg',
			);
			register_post_type('jbp_pro', $jbp_pro);
		} //jbp_pro post type complete

		if (!post_type_exists('jbp_media')) {
			$jbp_media = array(
				'capability_type' => 'post',
				'map_meta_cap' => true,
				'menu_position' => '',
				'public' => false,
				'hierarchical' => false,
				'rewrite' => false
			);
			register_post_type('jbp_media', $jbp_media);
		}

	}

	/**
	 * Create the default taxonomies
	 *
	 */
	function load_custom_taxonomies()
	{

		if (!taxonomy_exists('jbp_category')) {
			$jbp_category = array(
				'object_type' => array(
					0 => 'jbp_job',
				),
				'hide_type' => array(
					0 => 'jbp_job',
				),
				'args' => array(
					'labels' => array(
						'name' => __('Job Categories', JBP_TEXT_DOMAIN),
						'singular_name' => __('Job Category', JBP_TEXT_DOMAIN),
						'add_new_item' => __('Add New Job Categories', JBP_TEXT_DOMAIN),
						'new_item_name' => __('New Job Category', JBP_TEXT_DOMAIN),
						'edit_item' => __('Edit Job Category', JBP_TEXT_DOMAIN),
						'update_item' => __('Update Job Category', JBP_TEXT_DOMAIN),
						'popular_items' => __('Search Job Categories', JBP_TEXT_DOMAIN),
						'all_items' => __('All Job Categories', JBP_TEXT_DOMAIN),
						'parent_item' => __('Job Categories', JBP_TEXT_DOMAIN),
						'parent_item_colon' => __('Job Categories: ', JBP_TEXT_DOMAIN),
						'add_or_remove_items' => __('Add or Remove Job Categories', JBP_TEXT_DOMAIN),
						'choose_from_most_used' => __('All Job Categories', JBP_TEXT_DOMAIN),
					),
					'public' => true,
					'show_admin_column' => NULL,
					'hierarchical' => true,
					'rewrite' => array(
						'slug' => 'jobs-category',
						'with_front' => true,
						'hierarchical' => false,
						'ep_mask' => 0,
					),
					'query_var' => true,
					'capabilities' => array(
						'manage_terms' => 'manage_categories',
						'edit_terms' => 'manage_categories',
						'delete_terms' => 'manage_categories',
						//'assign_terms' => 'edit_jobs',
					),
				),

			);

			register_taxonomy('jbp_category', array('jbp_job'), $jbp_category['args']);
		}

		if (!taxonomy_exists('jbp_skills_tag')) {
			$jbp_tag = array(
				'object_type' => array(
					0 => 'jbp_job',
				),
				'hide_type' => array(
					0 => 'jbp_job',
				),
				'args' => array(
					'labels' => array(
						'name' => __('Job Skills Tags', JBP_TEXT_DOMAIN),
						'singular_name' => __('Job Skills Tag', JBP_TEXT_DOMAIN),
						'add_new_item' => __('Add New Job Skills Tag', JBP_TEXT_DOMAIN),
						'new_item_name' => __('New Job Skills Tag', JBP_TEXT_DOMAIN),
						'edit_item' => __('Edit Job Skills Tag', JBP_TEXT_DOMAIN),
						'update_item' => __('Update Job Skills Tag', JBP_TEXT_DOMAIN),
						'search_items' => __('Search Job Skills Tags', JBP_TEXT_DOMAIN),
						'popular_items' => __('Popular Job Skills Tags', JBP_TEXT_DOMAIN),
						'all_items' => __('All Job Skills Tags', JBP_TEXT_DOMAIN),
						'parent_item_colon' => __('Jobs tags:', JBP_TEXT_DOMAIN),
						'add_or_remove_items' => __('Add or Remove Job Skills Tags', JBP_TEXT_DOMAIN),
						'choose_from_most_used' => __('All Job Skills Tags', JBP_TEXT_DOMAIN),
					),
					'public' => true,
					'hierarchical' => false,
					'rewrite' =>
						array(
							'slug' => 'job-skills',
							'with_front' => true,
							'hierarchical' => false,
							'ep_mask' => 0,
						),
					'query_var' => true,
					'capabilities' => array(
						'manage_terms' => 'manage_categories',
						'edit_terms' => 'manage_categories',
						'delete_terms' => 'manage_categories',
						//'assign_terms' => 'edit_jobs',
					),
				),
			);

			register_taxonomy('jbp_skills_tag', array('jbp_job'), $jbp_tag['args']);
		}

	}
}