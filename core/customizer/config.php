<?php 
/**
 * Customizer Configuration File
 *
 * This file is used to define the different panels, sections and controls for Layers
 *
 * @package Layers
 * @since Layers 1.0.0
 */

class Layers_Customizer_Config {

	/**
	* Layers Customiser Panels
	*
	* @return   array		Panels to be registered in the customizer
	*/

	public function panels(){

		$panels = array();

		return apply_filters( 'layers_customizer_panels', $panels );
	}

	/**
	* Layers Customiser Sections
	*
	* @return   array 		Sections to be registered in the customizer
	*/

	public function default_sections(){

		$default_sections = array();
		
		return apply_filters( 'layers_customizer_default_sections', $default_sections );
	}

	/**
	* Layers Customiser Sections
	*
	* @return array 		Sections to be registered in the customizer
	*/

	public function sections(){

		$sections = array();

		return apply_filters( 'layers_customizer_sections', $sections );
	}

	public function controls( $controls = array() ){

		$controls = array();
		 
		return apply_filters( 'layers_customizer_controls', $controls );
	}
}