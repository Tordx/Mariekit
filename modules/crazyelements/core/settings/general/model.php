<?php
namespace CrazyElements\Core\Settings\General;

use CrazyElements\Controls_Manager;
use CrazyElements\Core\Settings\Base\Model as BaseModel;

use CrazyElements\PrestaHelper; if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @since 1.0.0
 */
class Model extends BaseModel {

	/**
	 * Get model name.
	 *
	 * Retrieve global settings model name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Model name.
	 */
	public function get_name() {
		return 'global-settings';
	}

	/**
	 * Get CSS wrapper selector.
	 *
	 * Retrieve the wrapper selector for the global settings model.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string CSS wrapper selector.
	 */

	public function get_css_wrapper_selector() {
		return '';
	}

	/**
	 * Get panel page settings.
	 *
	 * Retrieve the panel setting for the global settings model.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Panel settings.
	 *
	 *    @type string $title The panel title.
	 *    @type array  $menu  The panel menu.
	 * }
	 */
	public function get_panel_page_settings() {
		return [
			'title' => PrestaHelper::__( 'Global Settings', 'elementor' ),
			'menu' => [
				'icon' => 'ceicon-cogs',
				'beforeItem' => 'elementor-settings',
			],
		];
	}

	/**
	 * Get controls list.
	 *
	 * Retrieve the global settings model controls list.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return array Controls list.
	 */
	public static function get_controls_list() {
		return [
			Controls_Manager::TAB_STYLE => [
				'style' => [
					'label' => PrestaHelper::__( 'Style', 'elementor' ),
					'controls' => [
						'elementor_default_generic_fonts' => [
							'label' => PrestaHelper::__( 'Default Generic Fonts', 'elementor' ),
							'type' => Controls_Manager::TEXT,
							'default' => 'Sans-serif',
							'description' => PrestaHelper::__( 'The list of fonts used if the chosen font is not available.', 'elementor' ),
							'label_block' => true,
						],
						'elementor_container_width' => [
							'label' => PrestaHelper::__( 'Content Width', 'elementor' ) . ' (px)',
							'type' => Controls_Manager::NUMBER,
							'min' => 300,
							'description' => PrestaHelper::__( 'Sets the default width of the content area (Default: 1140)', 'elementor' ),
							'selectors' => [
								'.elementor-section.elementor-section-boxed > .elementor-container' => 'max-width: {{VALUE}}px',
							],
						],
						'elementor_space_between_widgets' => [
							'label' => PrestaHelper::__( 'Widgets Space', 'elementor' ) . ' (px)',
							'type' => Controls_Manager::NUMBER,
							'min' => 0,
							'placeholder' => '20',
							'description' => PrestaHelper::__( 'Sets the default space between widgets (Default: 20)', 'elementor' ),
							'selectors' => [
								'.elementor-widget:not(:last-child)' => 'margin-bottom: {{VALUE}}px',
							],
						],
						'elementor_stretched_section_container' => [
							'label' => PrestaHelper::__( 'Stretched Section Fit To', 'elementor' ),
							'type' => Controls_Manager::TEXT,
							'placeholder' => 'body',
							'description' => PrestaHelper::__( 'Enter parent element selector to which stretched sections will fit to (e.g. #primary / .wrapper / main etc). Leave blank to fit to page width.', 'elementor' ),
							'label_block' => true,
							'frontend_available' => true,
						],
						'elementor_page_title_selector' => [
							'label' => PrestaHelper::__( 'Page Title Selector', 'elementor' ),
							'type' => Controls_Manager::TEXT,
							'placeholder' => 'h1.entry-title',
							'description' => PrestaHelper::__( 'Elementor lets you hide the page title. This works for themes that have "h1.entry-title" selector. If your theme\'s selector is different, please enter it above.', 'elementor' ),
							'label_block' => true,
						],
					],
				],
			],
			Manager::PANEL_TAB_LIGHTBOX => [
				'lightbox' => [
					'label' => PrestaHelper::__( 'Lightbox', 'elementor' ),
					'controls' => [
						'elementor_global_image_lightbox' => [
							'label' => PrestaHelper::__( 'Image Lightbox', 'elementor' ),
							'type' => Controls_Manager::SWITCHER,
							'default' => 'yes',
							'description' => PrestaHelper::__( 'Open all image links in a lightbox popup window. The lightbox will automatically work on any link that leads to an image file.', 'elementor' ),
							'frontend_available' => true,
						],
						'elementor_enable_lightbox_in_editor' => [
							'label' => PrestaHelper::__( 'Enable In Editor', 'elementor' ),
							'type' => Controls_Manager::SWITCHER,
							'default' => 'yes',
							'frontend_available' => true,
						],
						'elementor_lightbox_color' => [
							'label' => PrestaHelper::__( 'Background Color', 'elementor' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'.elementor-lightbox' => 'background-color: {{VALUE}}',
							],
						],
						'elementor_lightbox_ui_color' => [
							'label' => PrestaHelper::__( 'UI Color', 'elementor' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'.elementor-lightbox .dialog-lightbox-close-button, .elementor-lightbox .elementor-swiper-button' => 'color: {{VALUE}}',
							],
						],
						'elementor_lightbox_ui_color_hover' => [
							'label' => PrestaHelper::__( 'UI Hover Color', 'elementor' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'.elementor-lightbox .dialog-lightbox-close-button:hover, .elementor-lightbox .elementor-swiper-button:hover' => 'color: {{VALUE}}',
							],
						],
					],
				],
			],
		];
	}

	/**
	 * Register model controls.
	 *
	 * Used to add new controls to the global settings model.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$controls_list = self::get_controls_list();

		foreach ( $controls_list as $tab_name => $sections ) {

			foreach ( $sections as $section_name => $section_data ) {

				$this->start_controls_section(
					$section_name, [
						'label' => $section_data['label'],
						'tab' => $tab_name,
					]
				);

				foreach ( $section_data['controls'] as $control_name => $control_data ) {
					$this->add_control( $control_name, $control_data );
				}

				$this->end_controls_section();
			}
		}
	}
}
