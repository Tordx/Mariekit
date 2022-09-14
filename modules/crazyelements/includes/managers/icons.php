<?php
namespace CrazyElements;

use CrazyElements\Core\Common\Modules\Ajax\Module as Ajax;
use CrazyElements\Core\Files\Assets\Svg\Svg_Handler;

use CrazyElements\PrestaHelper; 

if ( ! defined( '_PS_VERSION_' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @since 1.0.0
 */
class Icons_Manager {

	const NEEDS_UPDATE_OPTION = 'icon_manager_needs_update';
	/**
	 * Tabs.
	 *
	 * Holds the list of all the tabs.
	 *
	 * @access private
	 * @static
	 * @since 1.0.0
	 * @var array
	 */
	private static $tabs;

	private static function get_needs_upgrade_option() {
		return PrestaHelper::get_option( 'elementor_' . self::NEEDS_UPDATE_OPTION, null );
	}

	/**
	 * register styles
	 *
	 * Used to register all icon types stylesheets so they could be enqueued later by widgets
	 */
	public function register_styles() {
		$config = self::get_icon_manager_tabs_config();

		$shared_styles = [];

		foreach ( $config as $type => $icon_type ) {
			if ( ! isset( $icon_type['url'] ) ) {
				continue;
			}
			$dependencies = [];
			if ( isset( $icon_type['enqueue'] ) ) {
				foreach ( (array) $icon_type['enqueue'] as $font_css_url ) {
					if ( ! in_array( $font_css_url, array_keys( $shared_styles ) ) ) {
						$style_handle = 'ce-icons-shared-' . count( $shared_styles );
						PrestaHelper::wp_register_style(
							$style_handle,
							$font_css_url,
							[],
							$icon_type['ver']
						);
						$shared_styles[ $font_css_url ] = $style_handle;
					}
					$dependencies[] = $shared_styles[ $font_css_url ];
				}
			}
			PrestaHelper::wp_register_style(
				'ce-icons-' . $icon_type['name'],
				$icon_type['url'],
				$dependencies,
				$icon_type['ver']
			);
		}
	}

	/**
	 * Init Tabs
	 *
	 * Initiate Icon Manager Tabs.
	 *
	 * @access private
	 * @static
	 * @since 1.0.0
	 */
	private static function init_tabs() {
		self::$tabs = PrestaHelper::apply_filters( 'elementor/icons_manager/native', [
			'fa-regular' => [
				'name' => 'fa-regular',
				'label' => PrestaHelper::__( 'Font Awesome - Regular', 'elementor' ),
				'url' => self::get_fa_asset_url( 'regular' ),
				'enqueue' => [ self::get_fa_asset_url( 'fontawesome' ) ],
				'prefix' => 'fa-',
				'displayPrefix' => 'far',
				'labelIcon' => 'fab fa-font-awesome-alt',
				'ver' => '5.9.0',
				'fetchJson' => self::get_fa_asset_url( 'regular', 'json', false ),
				'native' => true,
			],
			'fa-solid' => [
				'name' => 'fa-solid',
				'label' => PrestaHelper::__( 'Font Awesome - Solid', 'elementor' ),
				'url' => self::get_fa_asset_url( 'solid' ),
				'enqueue' => [ self::get_fa_asset_url( 'fontawesome' ) ],
				'prefix' => 'fa-',
				'displayPrefix' => 'fas',
				'labelIcon' => 'fab fa-font-awesome',
				'ver' => '5.9.0',
				'fetchJson' => self::get_fa_asset_url( 'solid', 'json', false ),
				'native' => true,
			],
			'fa-brands' => [
				'name' => 'fa-brands',
				'label' => PrestaHelper::__( 'Font Awesome - Brands', 'elementor' ),
				'url' => self::get_fa_asset_url( 'brands' ),
				'enqueue' => [ self::get_fa_asset_url( 'fontawesome' ) ],
				'prefix' => 'fa-',
				'displayPrefix' => 'fab',
				'labelIcon' => 'fab fa-font-awesome-flag',
				'ver' => '5.9.0',
				'fetchJson' => self::get_fa_asset_url( 'brands', 'json', false ),
				'native' => true,
			],
		] );
	}

	/**
	 * Get Icon Manager Tabs
	 * @return array
	 */
	public static function get_icon_manager_tabs() {
		if ( ! self::$tabs ) {
			self::init_tabs();
		}
		$fontsoption = PrestaHelper::get_option('custom_icon_upload_fonts');
		$fontsoption = \Tools::jsonDecode($fontsoption,true);
		$returnicons = [];
        if (!empty($fontsoption)) {
            foreach ($fontsoption as $key => $font) {
                if (file_exists($font['maindir'] . "fontarray.json")) {
                    $returnicons[$font['fontname']] = [
                        'name' => $font['fontname'],
                        'label' => $font['fontname'],
                        'url' => '',
                        'enqueue' => array($font['mainurl'] . 'style.css'),
                        'prefix' => '',
                        'displayPrefix' => $font['fontname'],
                        'labelIcon' => $font['firsticonname'],
                        'ver' => '1.0',
                        'fetchJson' => $font['mainurl'] .  "fontarray.json",
                    ];
                }
            }
        }
		$additional_tabs = $returnicons;
		return array_merge( self::$tabs, $additional_tabs );
	}

	public static function enqueue_shim() {
		PrestaHelper::wp_enqueue_script(
			'font-awesome-4-shim',
			self::get_fa_asset_url( 'v4-shims', 'js' ),
			[],
			CRAZY_VERSION
		);
		PrestaHelper::wp_enqueue_style(
			'font-awesome-5-all',
			self::get_fa_asset_url( 'all' ),
			[],
			CRAZY_VERSION
		);
		PrestaHelper::wp_enqueue_style(
			'font-awesome-4-shim',
			self::get_fa_asset_url( 'v4-shims' ),
			[],
			CRAZY_VERSION
		);
	}

	private static function get_fa_asset_url( $filename, $ext_type = 'css', $add_suffix = true ) {
		static $is_test_mode = null;
		if ( null === $is_test_mode ) {
			$is_test_mode = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || defined( 'CRAZY_TESTS' ) && CRAZY_TESTS;
		}
		$url = CRAZY_ASSETS_URL . 'lib/font-awesome/' . $ext_type . '/' . $filename;
		if ( ! $is_test_mode && $add_suffix ) {
			$url .= '.min';
		}
		return $url . '.' . $ext_type;
	}

	public static function get_icon_manager_tabs_config() {
		$tabs = [
			'all' => [
				'name' => 'all',
				'label' => PrestaHelper::__( 'All Icons', 'elementor' ),
				'labelIcon' => 'ceicon-filter',
				'native' => true,
			],
		];

		return array_values( array_merge( $tabs, self::get_icon_manager_tabs() ) );
	}

	private static function render_svg_icon( $value ) {
		if ( ! isset( $value['url'] ) ) {
			return '';
		}
		
		return Svg_Handler::get_inline_svg( $value['url'] );
	}

	private static function render_icon_html( $icon, $attributes = [], $tag = 'i' ) {
		$icon_types = self::get_icon_manager_tabs();
		if ( isset( $icon_types[ $icon['library'] ]['render_callback'] ) && is_callable( $icon_types[ $icon['library'] ]['render_callback'] ) ) {
			return call_user_func_array( $icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ] );
		}

		if ( empty( $attributes['class'] ) ) {
			$attributes['class'] = $icon['value'];
		} else {
			if ( is_array( $attributes['class'] ) ) {
				$attributes['class'][] = $icon['value'];
			} else {
				$attributes['class'] .= ' ' . $icon['value'];
			}
		}
		return '<' . $tag . ' ' . Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
	}

	/**
	 * Render Icon
	 *
	 * @param array $icon             Icon Type, Icon value
	 * @param array $attributes       Icon HTML Attributes
	 * @param string $tag             Icon HTML tag, defaults to <i>
	 *
	 * @return mixed|string
	 */
	public static function render_icon( $icon, $attributes = [], $tag = 'i' ) {
		if ( empty( $icon['library'] ) ) {
			return false;
		}
		$output = '';
		// handler SVG Icon
		if ( 'svg' === $icon['library'] ) {
			$output = self::render_svg_icon( $icon['value'] );
		} else {
			$output = self::render_icon_html( $icon, $attributes, $tag );
		}
		echo $output;
		return true;
	}

	public static function on_import_migration( $element, $old_control, $new_control ) {
		if ( isset( $element['settings'][ $old_control ] ) && empty( $element['settings'][ $old_control ] ) && self::is_migration_allowed() ) {
			unset( $element['settings'][ $old_control ] );
			$element['settings'][ $new_control ] = [
				'value' => '',
				'library' => '',
			];
		}
		return $element;
	}

	/**
	 * is_migration_allowed
	 * @return bool
	 */
	public static function is_migration_allowed() {
		static $migration_allowed = false;
		if ( false === $migration_allowed ) {
			$migration_allowed = null === self::get_needs_upgrade_option();

			/**
			 * allowed to filter migration allowed
			 */
			$migration_allowed = PrestaHelper::apply_filters( 'elementor/icons_manager/migration_allowed', $migration_allowed );
		}
		return $migration_allowed;
	}

	/**
	 * Register_Admin Settings
	 *
	 * adds Font Awesome migration / update admin settings
	 * @param Settings $settings
	 */
	public function register_admin_settings( Settings $settings ) {
		$settings->add_field(
			Settings::TAB_ADVANCED,
			Settings::TAB_ADVANCED,
			'load_fa4_shim',
			[
				'label' => PrestaHelper::__( 'Load Font Awesome 4 Support', 'elementor' ),
				'field_args' => [
					'type' => 'select',
					'std' => 1,
					'options' => [
						'' => PrestaHelper::__( 'No', 'elementor' ),
						'yes' => PrestaHelper::__( 'Yes', 'elementor' ),
					],
					'desc' => PrestaHelper::__( 'Font Awesome 4 support script (shim.js) is a script that makes sure all previously selected Font Awesome 4 icons are displayed correctly while using Font Awesome 5 library.', 'elementor' ),
				],
			]
		);
	}

	public function register_admin_tools_settings( Tools $settings ) {
		$settings->add_tab( 'fontawesome4_migration', [ 'label' => PrestaHelper::__( 'Font Awesome Upgrade', 'elementor' ) ] );

		$settings->add_section( 'fontawesome4_migration', 'fontawesome4_migration', [
			'callback' => function() {
				echo '<h2>' . PrestaHelper::esc_html__( 'Font Awesome Upgrade', 'elementor' ) . '</h2>';
				echo '<p>' .
				PrestaHelper::esc_html__( 'Access 1,500+ amazing Font Awesome 5 icons and enjoy faster performance and design flexibility.', 'elementor' ) . '<br>' .
				PrestaHelper::esc_html__( 'By upgrading, whenever you edit a page containing a Font Awesome 4 icon, Elementor will convert it to the new Font Awesome 5 icon.', 'elementor' ) .
				'</p><p><strong>' .
				PrestaHelper::esc_html__( 'Please note that the upgrade process may cause some of the previously used Font Awesome 4 icons to look a bit different due to minor design changes made by Font Awesome.', 'elementor' ) .
				'</strong></p><p>' .
				PrestaHelper::esc_html__( 'This action is not reversible and cannot be undone by rolling back to previous versions.', 'elementor' ) .
				'</p>';
			},
			'fields' => [
				[
					'label'      => PrestaHelper::__( 'Font Awesome Upgrade', 'elementor' ),
					'field_args' => [
						'type' => 'raw_html',
						'html' => sprintf( '<span data-action="%s" data-_nonce="%s" class="button" id="elementor_upgrade_fa_button">%s</span>',
							self::NEEDS_UPDATE_OPTION . '_upgrade',
							wp_create_nonce( self::NEEDS_UPDATE_OPTION ),
							PrestaHelper::__( 'Upgrade To Font Awesome 5', 'elementor' )
						),
					],
				],
			],
		] );
	}

	/**
	 * Ajax Upgrade to FontAwesome 5
	 */
	public function ajax_upgrade_to_fa5() {
		check_ajax_referer( self::NEEDS_UPDATE_OPTION, '_nonce' );

		delete_option( 'elementor_' . self::NEEDS_UPDATE_OPTION );

		wp_send_json_success( [ 'message' => '<p>' . PrestaHelper::__( 'Hurray! The upgrade process to Font Awesome 5 was completed successfully.', 'elementor' ) . '</p>' ] );
	}

	/**
	 * Add Update Needed Flag
	 * @param array $settings
	 *
	 * @return array;
	 */
	public function add_update_needed_flag( $settings ) {
		$settings['icons_update_needed'] = true;
		return $settings;
	}

	public function enqueue_fontawesome_css() {
		if ( ! self::is_migration_allowed() ) {
			PrestaHelper::wp_enqueue_style( 'font-awesome' );
		} else {
			$current_filter = PrestaHelper::current_filter();
			$load_shim = PrestaHelper::get_option( 'elementor_load_fa4_shim', false );
			if ( 'elementor/editor/after_enqueue_styles' === $current_filter ) {
				self::enqueue_shim();
			} else if ( 'yes' === $load_shim ) {
				self::enqueue_shim();
			}
		}
	}

	public function add_admin_strings( $settings ) {
		$settings['i18n']['confirm_fa_migration_admin_modal_body']  = PrestaHelper::__( 'I understand that by upgrading to Font Awesome 5,', 'elementor' ) . '<br>' . PrestaHelper::__( 'I acknowledge that some changes may affect my website and that this action cannot be undone.', 'elementor' );
		$settings['i18n']['confirm_fa_migration_admin_modal_head']  = PrestaHelper::__( 'Font Awesome 5 Migration', 'elementor' );
		return $settings;
	}

	public function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'enable_svg_uploads', [ $this, 'ajax_enable_svg_uploads' ] );
	}

	public function ajax_enable_svg_uploads() {
		PrestaHelper::update_option( 'elementor_allow_svg', 1 );
	}

	/**
	 * Icons Manager constructor
	 */
	public function __construct() {

		PrestaHelper::add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_fontawesome_css' ] );
		PrestaHelper::add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_fontawesome_css' ] );

		PrestaHelper::add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );

		// Ajax.
		PrestaHelper::add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );

		if ( ! self::is_migration_allowed() ) {
			PrestaHelper::add_filter( 'elementor/editor/localize_settings', [ $this, 'add_update_needed_flag' ] );
			PrestaHelper::add_action( 'elementor/admin/after_create_settings/' . Tools::PAGE_ID, [ $this, 'register_admin_tools_settings' ], 100 );

			if ( ! empty( $_POST ) ) { // phpcs:ignore -- nonce validation done in callback
				PrestaHelper::add_action( 'wp_ajax_' . self::NEEDS_UPDATE_OPTION . '_upgrade', [ $this, 'ajax_upgrade_to_fa5' ] );
			}
		}
	}
}
