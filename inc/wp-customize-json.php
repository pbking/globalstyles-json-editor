<?php

class GlobalStylesJSONCustomizer {

	private $section_key = 'customize-global-styles-json';

	function __construct() {
		add_action( 'customize_register', array( $this, 'initialize' ) );
		add_action( 'customize_save_after', array( $this, 'handle_customize_save_after' ) );
	}

	function initialize( $wp_customize ) {

		$user_custom_post_type_id     = WP_Theme_JSON_Resolver_Gutenberg::get_user_custom_post_type_id();
		$user_theme_json_post         = get_post( $user_custom_post_type_id );
		$user_theme_json_post_content = json_decode( $user_theme_json_post->post_content );

		$theme = wp_get_theme();

		$wp_customize->add_section(
			$this->section_key,
			array(
				'capability'  => 'edit_theme_options',
				'description' => sprintf( __( 'JSON Customization for %1$s', 'globalstyles-json-editor' ), $theme->name ),
				'title'       => __( 'Global Styles JSON', 'globalstyles-json-editor' ),
				'priority'    => 210,
			)
		);

		//TODO: Because this sets this only at load if something (like changing colors) changes the user JSON this isn't updated
		//and you'll have to refresh to see the changes.
		$wp_customize->add_setting(
			$this->section_key,
			array(
				'type'    => 'theme_json',
				'transport' => 'postMessage',
				'default' => json_encode( json_decode( $user_theme_json_post->post_content ), JSON_PRETTY_PRINT ),
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
	}

	function handle_customize_save_after( $wp_customize ) {
		//TODO
	}
}

new GlobalStylesJSONCustomizer;

