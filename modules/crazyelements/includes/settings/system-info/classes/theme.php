<?php
namespace CrazyElements\System_Info\Classes;

use CrazyElements\System_Info\Classes\Abstracts\Base_Reporter;

use CrazyElements\PrestaHelper; if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @since 1.0.0
 */
class Theme_Reporter extends Base_Reporter {

	private $theme = null;

	/**
	 * Get theme reporter title.
	 *
	 * Retrieve theme reporter title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Reporter title.
	 */
	public function get_title() {
		return 'Theme';
	}

	/**
	 * Get theme report fields.
	 *
	 * Retrieve the required fields for the theme report.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Required report fields with field ID and field label.
	 */
	public function get_fields() {
		$fields = [
			'name' => 'Name',
			'version' => 'Version',
			'author' => 'Author',
			'is_child_theme' => 'Child Theme',
		];

		if ( $this->get_parent_theme() ) {
			$parent_fields = [
				'parent_name' => 'Parent Theme Name',
				'parent_version' => 'Parent Theme Version',
				'parent_author' => 'Parent Theme Author',
			];
			$fields = array_merge( $fields, $parent_fields );
		}

		return $fields;
	}

	protected function _get_theme() {
		if ( is_null( $this->theme ) ) {
			$this->theme = wp_get_theme();
		}
		return $this->theme;
	}

	protected function get_parent_theme() {
		return $this->_get_theme()->parent();
	}

	public function get_name() {
		return [
			'value' => $this->_get_theme()->get( 'Name' ),
		];
	}

	/**
	 * Get theme author.
	 *
	 * Retrieve the theme author.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The theme author.
	 * }
	 */
	public function get_author() {
		return [
			'value' => $this->_get_theme()->get( 'Author' ),
		];
	}

	/**
	 * Get theme version.
	 *
	 * Retrieve the theme version.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The theme version.
	 * }
	 */
	public function get_version() {
		return [
			'value' => $this->_get_theme()->get( 'Version' ),
		];
	}

	/**
	 * Is the theme is a child theme.
	 *
	 * Whether the theme is a child theme.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value          Yes if the theme is a child theme, No otherwise.
	 *    @type string $recommendation Theme source code modification recommendation.
	 * }
	 */
	public function get_is_child_theme() {
		$is_child_theme = is_child_theme();

		$result = [
			'value' => $is_child_theme ? 'Yes' : 'No',
		];

		if ( ! $is_child_theme ) {
			$result['recommendation'] = sprintf(
				/* translators: %s: Codex URL */
				PrestaHelper::_x( 'If you want to modify the source code of your theme, we recommend using a <a href="%s">child theme</a>.', 'System Info', 'elementor' ),
				'https://codex.wordpress.org/Child_Themes'
			);
		}

		return $result;
	}

	/**
	 * Get parent theme version.
	 *
	 * Retrieve the parent theme version.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The parent theme version.
	 * }
	 */
	public function get_parent_version() {
		return [
			'value' => $this->get_parent_theme()->get( 'Version' ),
		];
	}

	/**
	 * Get parent theme author.
	 *
	 * Retrieve the parent theme author.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The parent theme author.
	 * }
	 */
	public function get_parent_author() {
		return [
			'value' => $this->get_parent_theme()->get( 'Author' ),
		];
	}

	/**
	 * Get parent theme name.
	 *
	 * Retrieve the parent theme name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The parent theme name.
	 * }
	 */
	public function get_parent_name() {
		return [
			'value' => $this->get_parent_theme()->get( 'Name' ),
		];
	}
}