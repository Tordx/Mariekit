<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper; if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}


class Widget_Spacer extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve spacer widget name.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'spacer';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve spacer widget title.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return PrestaHelper::__( 'Spacer', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve spacer widget icon.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'ceicon-spacer';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the spacer widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'basic' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'space' ];
	}

	/**
	 * Register spacer widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_spacer',
			[
				'label' => PrestaHelper::__( 'Spacer', 'elementor' ),
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label' => PrestaHelper::__( 'Space', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 600,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-spacer-inner' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => PrestaHelper::__( 'View', 'elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render spacer widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0
	 * @access protected
	 */
	protected function render() {
		?>
		<div class="elementor-spacer">
			<div class="elementor-spacer-inner"></div>
		</div>
		<?php
	}

	/**
	 * Render spacer widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<div class="elementor-spacer">
			<div class="elementor-spacer-inner"></div>
		</div>
		<?php
	}
}