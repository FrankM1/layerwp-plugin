<?php 
/**
 * Customizer Configuration File
 *
 * This file is used to define the different panels, sections and controls for Layers
 *
 * @package Layers
 * @since Layers 1.0.0
 */

if ( !class_exists( 'Layers_Customizer_Config' ) ) :

	class Layers_Customizer_Config {
	
		/**
		* Layers Customiser Panels
		*
		* @return   array		Panels to be registered in the customizer
		*/
	
		public function panels(){
	
			return apply_filters( 'layers_customizer_panels', array() );
			
		}
	
		/**
		* Layers Customiser Sections
		*
		* @return   array 		Sections to be registered in the customizer
		*/
	
		public function default_sections(){
				
			return apply_filters( 'layers_customizer_default_sections', array() );
		}
	
		/**
		* Layers Customiser Sections
		*
		* @return array 		Sections to be registered in the customizer
		*/
	
		public function sections(){
			
			return apply_filters( 'layers_customizer_sections', array() );
		
		}
	
		public function controls( $controls = array() ){
			 
			return apply_filters( 'layers_customizer_controls', array() );
		}
	}

endif;