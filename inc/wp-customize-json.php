<?php

class GlobalStylesJSONCustomizer {

	private $section_key = 'customize-global-styles-json';
	private $user_custom_post_type_id;

	function __construct() {
		add_action( 'customize_register', array( $this, 'initialize' ) );
		add_action( 'customize_save_after', array( $this, 'handle_customize_save_after' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_control_js' )  );
	}

	function customize_control_js() {
		wp_enqueue_script( 'customize-global-styles-json-control', plugin_dir_url( __FILE__ ) . 'wp-customize-json.js', array( 'customize-controls' ), null, true );
		wp_localize_script( 'customize-global-styles-json-control', 'userCustomPostTypeId', array( $this->user_custom_post_type_id ) );
		wp_localize_script( 'customize-global-styles-json-control', 'userJSON', array( $this->get_user_json() ) );
	}

	function get_user_json() {
		$user_theme_json_post         = get_post( $this->user_custom_post_type_id );
		$user_theme_json_post_content = json_decode( $user_theme_json_post->post_content );
		return $user_theme_json_post_content;
	}

	function get_merged_json() {
		$theme_json = WP_Theme_JSON_Resolver_Gutenberg::get_merged_data()->get_raw_data();
		return $theme_json;
	}

	function initialize( $wp_customize ) {

		// Get the user's theme.json from the CPT.
		if ( method_exists( 'WP_Theme_JSON_Resolver_Gutenberg', 'get_user_global_styles_post_id' ) ) { // This is the new name.
 			$this->user_custom_post_type_id = WP_Theme_JSON_Resolver_Gutenberg::get_user_global_styles_post_id();
		} else if ( method_exists( 'WP_Theme_JSON_Resolver_Gutenberg', 'get_user_custom_post_type_id' ) ) { // This is the old name.
 			$this->user_custom_post_type_id = WP_Theme_JSON_Resolver_Gutenberg::get_user_custom_post_type_id();
		}

		$theme = wp_get_theme();

		$wp_customize->add_section(
			$this->section_key,
			array(
				'capability'  => 'edit_theme_options',
				'description' => sprintf( __( 'User Customization for %1$s', 'globalstyles-json-editor' ), $theme->name ),
				'title'       => __( 'Global Styles User Theme JSON', 'globalstyles-json-editor' ),
				'priority'    => 210,
			)
		);

		$wp_customize->add_setting(
			$this->section_key,
			array(
				'type'    => 'theme_json',
				'transport' => 'postMessage',
				'default' => json_encode( $this->get_user_json(), JSON_PRETTY_PRINT ),
			)
		);

		$wp_customize->add_setting(
			$this->section_key . '_merged',
			array(
				'type'    => 'theme_json',
				'transport' => 'postMessage',
				'default' => json_encode( $this->get_merged_json(), JSON_PRETTY_PRINT ),
			)
		);


		$wp_customize->add_control(
			new WP_Customize_Code_Editor_Control(
				$wp_customize,
				$this->section_key,
				array(
					'label'   => 'Custom JSON',
					'section' => $this->section_key,
					'code_type' => 'application/json',
					'editor_settings' => array(
						'codemirror' => array(
							'lineWrapping' => false,
						)
					)
				)
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Code_Editor_Control(
				$wp_customize,
				$this->section_key . '_merged',
				array(
					'label'   => 'Merged JSON',
					'section' => $this->section_key,
					'code_type' => 'application/json',
					'editor_settings' => array(
						'codemirror' => array(
							'lineWrapping' => false,
						)
					)
				)
			)
		);
	}

	function handle_customize_save_after( $wp_customize ) {
		//TODO
		// $wp_customize->get_setting( $this->section_key )->set('pickles');

		wp_localize_script( 'customize-global-styles-json-control', 'userJSON', array( $this->get_user_json() ) );

	}
}

new GlobalStylesJSONCustomizer;

