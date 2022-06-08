<?php

class GlobalStylesJSONCustomizer {

	private $section_key = 'customize-global-styles-json';
	private $user_custom_post_type_id;

	function __construct() {
		add_action( 'customize_register', array( $this, 'initialize' ) );
		add_action( 'customize_save_after', array( $this, 'handle_customize_save_after' ) );
	}

	function get_user_json() {
		$user_json = WP_Theme_JSON_Resolver::get_user_data()->get_raw_data();
		return $user_json;
	}

	function get_theme_json() {
		$theme_json = WP_Theme_JSON_Resolver::get_theme_data()->get_raw_data();
		return $theme_json;
	}

	function get_merged_json() {
		$merged_json = WP_Theme_JSON_Resolver::get_merged_data()->get_raw_data();
		return $merged_json;
	}


	function initialize( $wp_customize ) {

		// Get the user's theme.json from the CPT.
		if ( method_exists( 'WP_Theme_JSON_Resolver', 'get_user_global_styles_post_id' ) ) {
 			$this->user_custom_post_type_id = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
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
			$this->section_key . '_theme',
			array(
				'type'    => 'theme_json',
				'transport' => 'postMessage',
				'default' => json_encode( $this->get_theme_json(), JSON_PRETTY_PRINT ),
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
				$this->section_key . '_theme',
				array(
					'label'   => 'Theme JSON',
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

