<?php

/**
 * TRUE Additional Information Woo tab only
 *
 * @since 1.0
 * @class TrueTechnicalModule
 * 
 */
class TrueTechnicalModule extends FLBuilderModule {

    /**
	 * The module construct, we need to pass some basic info here.
	 */
    public function __construct()
    {
        parent::__construct(array(
            'name'            => __( 'TRUE Technical', 'fl-theme-builder' ),
            'description'     => __( 'Displays the Additional Information Tab', 'fl-theme-builder' ),
            'group'           => __( 'TRUE Modules', 'fl-theme-builder' ),
            'category'        => __( 'Content Modules', 'fl-theme-builder' ),
            'dir'             => BB_TRUE_DIR . 'modules/true-technical/',
            'url'             => BB_TRUE_URL . 'modules/true-technical/',
            'icon'            => 'button.svg',
            'editor_export'   => true, // Defaults to true and can be omitted.
            'enabled'         => true, // Defaults to true and can be omitted.
            'partial_refresh' => false, // Defaults to false and can be omitted.
        ));
    }

    /**
	 * @since 1.0
	 * @return string
	 */
	// static public function get_product_attributes() {
	// 	return self::get_template_html( 'wc_display_product_attributes' );
	// }
}

/**
 * Register the module and its form settings.
 * We are using a very simple form here with only two options, photo_one and photo_two.
 */
FLBuilder::register_module( 'TrueTechnicalModule', array() );

