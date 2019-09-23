<?php

/**
 * TRUE Nonce for Fitting Algo
 *
 * @since 1.0
 * @class TrueNonceModule
 * 
 */
class TrueNonceModule extends FLBuilderModule {

    /**
	 * The module construct, we need to pass some basic info here.
	 */
    public function __construct()
    {
        parent::__construct(array(
            'name'            => __( 'TRUE Nonce', 'fl-theme-builder' ),
            'description'     => __( 'TRUE Nonce for Fitting Algo', 'fl-theme-builder' ),
            'group'           => __( 'TRUE Modules', 'fl-theme-builder' ),
            'category'        => __( 'Content Modules', 'fl-theme-builder' ),
            'dir'             => BB_TRUE_DIR . 'modules/true-nonce/',
            'url'             => BB_TRUE_URL . 'modules/true-nonce/',
            'icon'            => 'button.svg',
            'editor_export'   => true, // Defaults to true and can be omitted.
            'enabled'         => true, // Defaults to true and can be omitted.
            'partial_refresh' => false, // Defaults to false and can be omitted.
        ));
    }

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module( 'TrueNonceModule', array() );

