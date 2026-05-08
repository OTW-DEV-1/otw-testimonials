<?php
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OTW_Testimonials_Settings {

	private const OPTION_KEY = 'otw_testimonials_design';

	private static ?self $instance = null;

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_submenu' ), 20 );
		add_action( 'admin_init', array( $this, 'handle_save' ) );
	}

	public function add_submenu(): void {
		add_submenu_page(
			'otw-testimonials',
			__( 'Design Settings', 'otw-testimonials' ),
			__( 'Design Settings', 'otw-testimonials' ),
			'manage_options',
			'otw-testimonials-design',
			array( $this, 'render_page' )
		);
	}

	public static function get_settings(): array {
		return (array) get_option( self::OPTION_KEY, array() );
	}

	public static function get_design_css( string $scope = '.otw-testimonials-wrapper' ): string {
		$settings = self::get_settings();
		if ( empty( $settings ) ) {
			return '';
		}
		$generator = new OTW_Testimonials_CSS_Generator( $settings, $scope );
		return $generator->generate();
	}

	/* ─────────────────────────────────────────────────────────────────────── */
	/*  Field definitions                                                       */
	/* ─────────────────────────────────────────────────────────────────────── */

	private static function get_field_types(): array {
		return array(
			// Card
			'card_bg_color'         => 'color',
			'card_border_width'     => 'int',
			'card_border_color'     => 'color',
			'card_border_style'     => 'select',
			'card_radius_tl'        => 'int',
			'card_radius_tr'        => 'int',
			'card_radius_br'        => 'int',
			'card_radius_bl'        => 'int',
			'card_pad_top'          => 'int',
			'card_pad_right'        => 'int',
			'card_pad_bottom'       => 'int',
			'card_pad_left'         => 'int',
			'card_box_shadow'       => 'text',
			'card_hover_bg'         => 'color',
			'card_hover_shadow'     => 'text',
			'card_hover_transform'  => 'text',
			// Avatar
			'avatar_size'           => 'int',
			'avatar_border_radius'  => 'text',
			'avatar_border_width'   => 'int',
			'avatar_border_color'   => 'color',
			'avatar_border_style'   => 'select',
			// Author Name
			'title_color'           => 'color',
			'title_font_size'       => 'int',
			'title_font_weight'     => 'select',
			'title_line_height'     => 'float',
			// Position
			'position_color'        => 'color',
			'position_font_size'    => 'int',
			'position_font_weight'  => 'select',
			// Date
			'date_color'            => 'color',
			'date_font_size'        => 'int',
			'date_display'          => 'select',
			// Content
			'content_color'         => 'color',
			'content_font_size'     => 'int',
			'content_line_height'   => 'float',
			// Rating
			'star_size'             => 'int',
			'star_color_filled'     => 'color',
			'star_color_empty'      => 'color',
			// Load More
			'loadmore_font_size'    => 'int',
			'loadmore_font_weight'  => 'select',
			'loadmore_color'        => 'color',
			'loadmore_bg'           => 'color',
			'loadmore_border_width' => 'int',
			'loadmore_border_color' => 'color',
			'loadmore_border_style' => 'select',
			'loadmore_border_radius'=> 'int',
			'loadmore_pad_top'      => 'int',
			'loadmore_pad_right'    => 'int',
			'loadmore_pad_bottom'   => 'int',
			'loadmore_pad_left'     => 'int',
			'loadmore_hover_color'  => 'color',
			'loadmore_hover_bg'     => 'color',
			// Arrows
			'arrow_color'           => 'color',
			'arrow_bg'              => 'color',
			'arrow_size'            => 'int',
			'arrow_border_radius'   => 'int',
			// Dots
			'dot_color'             => 'color',
			'dot_active_color'      => 'color',
			'dot_size'              => 'int',
		);
	}

	/* ─────────────────────────────────────────────────────────────────────── */
	/*  Save handler                                                            */
	/* ─────────────────────────────────────────────────────────────────────── */

	public function handle_save(): void {
		if ( empty( $_POST['otw_design_save'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['otw_design_nonce'] ?? '', 'otw_design_save' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'otw-testimonials' ) );
		}

		$raw  = wp_unslash( (array) ( $_POST['otw_design'] ?? array() ) );
		$data = $this->sanitize( $raw );
		update_option( self::OPTION_KEY, $data );

		wp_safe_redirect(
			add_query_arg(
				array( 'page' => 'otw-testimonials-design', 'message' => 'saved' ),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	private function sanitize( array $raw ): array {
		$types = self::get_field_types();
		$bps   = array( 'desktop', 'laptop', 'tablet', 'mobile' );
		$clean = array();

		$border_styles = array( '', 'none', 'solid', 'dashed', 'dotted', 'double' );
		$font_weights  = array( '', '100', '200', '300', '400', '500', '600', '700', '800', '900' );
		$date_display  = array( '', 'show', 'hide' );

		foreach ( $bps as $bp ) {
			foreach ( $types as $key => $type ) {
				$full = "{$key}_{$bp}";
				$val  = trim( (string) ( $raw[ $full ] ?? '' ) );

				switch ( $type ) {
					case 'color':
						$clean[ $full ] = sanitize_text_field( $val );
						break;
					case 'int':
						$clean[ $full ] = $val !== '' ? (string) absint( $val ) : '';
						break;
					case 'float':
						$clean[ $full ] = $val !== '' ? (string) round( abs( (float) $val ), 2 ) : '';
						break;
					case 'text':
						$clean[ $full ] = sanitize_text_field( $val );
						break;
					case 'select':
						if ( str_contains( $key, 'border_style' ) ) {
							$clean[ $full ] = in_array( $val, $border_styles, true ) ? $val : '';
						} elseif ( str_contains( $key, 'font_weight' ) ) {
							$clean[ $full ] = in_array( $val, $font_weights, true ) ? $val : '';
						} elseif ( $key === 'date_display' ) {
							$clean[ $full ] = in_array( $val, $date_display, true ) ? $val : '';
						} else {
							$clean[ $full ] = sanitize_text_field( $val );
						}
						break;
					default:
						$clean[ $full ] = sanitize_text_field( $val );
				}
			}
		}

		return $clean;
	}

	/* ─────────────────────────────────────────────────────────────────────── */
	/*  Page render                                                             */
	/* ─────────────────────────────────────────────────────────────────────── */

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = self::get_settings();
		$saved    = isset( $_GET['message'] ) && $_GET['message'] === 'saved';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Testimonials — Design Settings', 'otw-testimonials' ); ?></h1>
			<div class="otw-admin-wrap">

			<?php if ( $saved ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Design settings saved.', 'otw-testimonials' ); ?></p></div>
			<?php endif; ?>

			<p class="description" style="margin-bottom:16px;">
				<?php esc_html_e( 'These settings control the visual appearance of testimonials rendered via the [otw_testimonials] shortcode. Elementor widget styles are controlled separately inside the widget.', 'otw-testimonials' ); ?>
			</p>

			<form method="post" id="otw-design-form">
				<?php wp_nonce_field( 'otw_design_save', 'otw_design_nonce' ); ?>
				<input type="hidden" name="otw_design_save" value="1">

				<!-- Main section tabs -->
				<ul class="nav nav-tabs" id="otwDesignTabs" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#otw-tab-card" type="button" role="tab"><?php esc_html_e( 'Card', 'otw-testimonials' ); ?></button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#otw-tab-avatar" type="button" role="tab"><?php esc_html_e( 'Avatar', 'otw-testimonials' ); ?></button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#otw-tab-typography" type="button" role="tab"><?php esc_html_e( 'Typography', 'otw-testimonials' ); ?></button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#otw-tab-rating" type="button" role="tab"><?php esc_html_e( 'Rating', 'otw-testimonials' ); ?></button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#otw-tab-loadmore" type="button" role="tab"><?php esc_html_e( 'Load More', 'otw-testimonials' ); ?></button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#otw-tab-carousel" type="button" role="tab"><?php esc_html_e( 'Carousel', 'otw-testimonials' ); ?></button>
					</li>
				</ul>

				<div class="tab-content otw-tab-card">
					<div class="tab-pane fade show active p-4" id="otw-tab-card" role="tabpanel">
						<?php $this->render_section_card( $settings ); ?>
					</div>
					<div class="tab-pane fade p-4" id="otw-tab-avatar" role="tabpanel">
						<?php $this->render_section_avatar( $settings ); ?>
					</div>
					<div class="tab-pane fade p-4" id="otw-tab-typography" role="tabpanel">
						<?php $this->render_section_typography( $settings ); ?>
					</div>
					<div class="tab-pane fade p-4" id="otw-tab-rating" role="tabpanel">
						<?php $this->render_section_rating( $settings ); ?>
					</div>
					<div class="tab-pane fade p-4" id="otw-tab-loadmore" role="tabpanel">
						<?php $this->render_section_loadmore( $settings ); ?>
					</div>
					<div class="tab-pane fade p-4" id="otw-tab-carousel" role="tabpanel">
						<?php $this->render_section_carousel( $settings ); ?>
					</div>
				</div>

				<button type="submit" class="btn btn-primary px-4">
					<?php esc_html_e( 'Save Design Settings', 'otw-testimonials' ); ?>
				</button>
			</form>

			</div><!-- /.otw-admin-wrap -->
		</div><!-- /.wrap -->
		<?php
	}

	/* ─────────────────────────────────────────────────────────────────────── */
	/*  Section renders                                                         */
	/* ─────────────────────────────────────────────────────────────────────── */

	private function render_section_card( array $s ): void {
		$this->bp_tabs_start( 'card' );
		foreach ( $this->breakpoints() as $bp => $label ) {
			$this->bp_pane_start( 'card', $bp, $bp === 'desktop' );
			echo '<div class="row g-3">';

			echo '<div class="col-12"><h6 class="otw-section-heading">' . esc_html__( 'Background & Border', 'otw-testimonials' ) . '</h6></div>';
			$this->field_color( "card_bg_color_{$bp}", __( 'Background Color', 'otw-testimonials' ), $s );
			$this->field_number( "card_border_width_{$bp}", __( 'Border Width (px)', 'otw-testimonials' ), $s );
			$this->field_color( "card_border_color_{$bp}", __( 'Border Color', 'otw-testimonials' ), $s );
			$this->field_select( "card_border_style_{$bp}", __( 'Border Style', 'otw-testimonials' ), $s, $this->border_style_options() );

			echo '<div class="col-12"><h6 class="otw-section-heading mt-2">' . esc_html__( 'Border Radius (px)', 'otw-testimonials' ) . '</h6></div>';
			$this->field_number( "card_radius_tl_{$bp}", __( 'Top Left', 'otw-testimonials' ), $s, 'col-sm-3' );
			$this->field_number( "card_radius_tr_{$bp}", __( 'Top Right', 'otw-testimonials' ), $s, 'col-sm-3' );
			$this->field_number( "card_radius_br_{$bp}", __( 'Bottom Right', 'otw-testimonials' ), $s, 'col-sm-3' );
			$this->field_number( "card_radius_bl_{$bp}", __( 'Bottom Left', 'otw-testimonials' ), $s, 'col-sm-3' );

			echo '<div class="col-12"><h6 class="otw-section-heading mt-2">' . esc_html__( 'Padding (px)', 'otw-testimonials' ) . '</h6></div>';
			$this->field_number( "card_pad_top_{$bp}", __( 'Top', 'otw-testimonials' ), $s, 'col-sm-3' );
			$this->field_number( "card_pad_right_{$bp}", __( 'Right', 'otw-testimonials' ), $s, 'col-sm-3' );
			$this->field_number( "card_pad_bottom_{$bp}", __( 'Bottom', 'otw-testimonials' ), $s, 'col-sm-3' );
			$this->field_number( "card_pad_left_{$bp}", __( 'Left', 'otw-testimonials' ), $s, 'col-sm-3' );

			echo '<div class="col-12"><h6 class="otw-section-heading mt-2">' . esc_html__( 'Shadow & Effects', 'otw-testimonials' ) . '</h6></div>';
			$this->field_text( "card_box_shadow_{$bp}", __( 'Box Shadow', 'otw-testimonials' ), $s, __( 'e.g. 0 4px 20px rgba(0,0,0,0.1)', 'otw-testimonials' ) );

			echo '<div class="col-12"><h6 class="otw-section-heading mt-2">' . esc_html__( 'Hover State', 'otw-testimonials' ) . '</h6></div>';
			$this->field_color( "card_hover_bg_{$bp}", __( 'Hover Background', 'otw-testimonials' ), $s );
			$this->field_text( "card_hover_shadow_{$bp}", __( 'Hover Box Shadow', 'otw-testimonials' ), $s, __( 'e.g. 0 8px 30px rgba(0,0,0,0.15)', 'otw-testimonials' ) );
			$this->field_text( "card_hover_transform_{$bp}", __( 'Hover Transform', 'otw-testimonials' ), $s, __( 'e.g. translateY(-4px)', 'otw-testimonials' ) );

			echo '</div>';
			$this->bp_pane_end();
		}
		$this->bp_tabs_end();
	}

	private function render_section_avatar( array $s ): void {
		$this->bp_tabs_start( 'avatar' );
		foreach ( $this->breakpoints() as $bp => $label ) {
			$this->bp_pane_start( 'avatar', $bp, $bp === 'desktop' );
			echo '<div class="row g-3">';

			$this->field_number( "avatar_size_{$bp}", __( 'Size (px)', 'otw-testimonials' ), $s );
			$this->field_text( "avatar_border_radius_{$bp}", __( 'Border Radius', 'otw-testimonials' ), $s, __( 'e.g. 50% or 8px', 'otw-testimonials' ) );

			echo '<div class="col-12"><h6 class="otw-section-heading mt-2">' . esc_html__( 'Border', 'otw-testimonials' ) . '</h6></div>';
			$this->field_number( "avatar_border_width_{$bp}", __( 'Border Width (px)', 'otw-testimonials' ), $s );
			$this->field_color( "avatar_border_color_{$bp}", __( 'Border Color', 'otw-testimonials' ), $s );
			$this->field_select( "avatar_border_style_{$bp}", __( 'Border Style', 'otw-testimonials' ), $s, $this->border_style_options() );

			echo '</div>';
			$this->bp_pane_end();
		}
		$this->bp_tabs_end();
	}

	private function render_section_typography( array $s ): void {
		// Sub-tabs: Author Name, Position, Date, Content
		$sub_sections = array(
			'name'    => __( 'Author Name', 'otw-testimonials' ),
			'pos'     => __( 'Position', 'otw-testimonials' ),
			'date'    => __( 'Date', 'otw-testimonials' ),
			'content' => __( 'Content', 'otw-testimonials' ),
		);
		?>
		<ul class="nav nav-pills nav-sm mb-3 otw-typo-tabs" id="otwTypoTabs" role="tablist">
			<?php foreach ( $sub_sections as $sub_key => $sub_label ) : ?>
			<li class="nav-item" role="presentation">
				<button class="nav-link <?php echo $sub_key === 'name' ? 'active' : ''; ?>"
					data-bs-toggle="tab"
					data-bs-target="#otw-typo-<?php echo esc_attr( $sub_key ); ?>"
					type="button" role="tab">
					<?php echo esc_html( $sub_label ); ?>
				</button>
			</li>
			<?php endforeach; ?>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade show active" id="otw-typo-name" role="tabpanel">
				<?php $this->render_typo_group( 'name', 'title', $s, true ); ?>
			</div>
			<div class="tab-pane fade" id="otw-typo-pos" role="tabpanel">
				<?php $this->render_typo_group( 'pos', 'position', $s, false ); ?>
			</div>
			<div class="tab-pane fade" id="otw-typo-date" role="tabpanel">
				<?php $this->render_typo_group( 'date', 'date', $s, false, true ); ?>
			</div>
			<div class="tab-pane fade" id="otw-typo-content" role="tabpanel">
				<?php $this->render_typo_group( 'content', 'content', $s, true ); ?>
			</div>
		</div>
		<?php
	}

	private function render_typo_group( string $sub, string $prefix, array $s, bool $has_line_height, bool $has_display = false ): void {
		$this->bp_tabs_start( "typo_{$sub}" );
		foreach ( $this->breakpoints() as $bp => $label ) {
			$this->bp_pane_start( "typo_{$sub}", $bp, $bp === 'desktop' );
			echo '<div class="row g-3">';
			$this->field_color( "{$prefix}_color_{$bp}", __( 'Color', 'otw-testimonials' ), $s );
			$this->field_number( "{$prefix}_font_size_{$bp}", __( 'Font Size (px)', 'otw-testimonials' ), $s );
			$this->field_select( "{$prefix}_font_weight_{$bp}", __( 'Font Weight', 'otw-testimonials' ), $s, $this->font_weight_options() );
			if ( $has_line_height ) {
				$this->field_float( "{$prefix}_line_height_{$bp}", __( 'Line Height', 'otw-testimonials' ), $s, '1.6' );
			}
			if ( $has_display ) {
				$this->field_select( "date_display_{$bp}", __( 'Visibility', 'otw-testimonials' ), $s, array(
					''     => __( '— default —', 'otw-testimonials' ),
					'show' => __( 'Show', 'otw-testimonials' ),
					'hide' => __( 'Hide', 'otw-testimonials' ),
				) );
			}
			echo '</div>';
			$this->bp_pane_end();
		}
		$this->bp_tabs_end();
	}

	private function render_section_rating( array $s ): void {
		$this->bp_tabs_start( 'rating' );
		foreach ( $this->breakpoints() as $bp => $label ) {
			$this->bp_pane_start( 'rating', $bp, $bp === 'desktop' );
			echo '<div class="row g-3">';
			$this->field_number( "star_size_{$bp}", __( 'Star Size (px)', 'otw-testimonials' ), $s );
			$this->field_color( "star_color_filled_{$bp}", __( 'Filled Star Color', 'otw-testimonials' ), $s );
			$this->field_color( "star_color_empty_{$bp}", __( 'Empty Star Color', 'otw-testimonials' ), $s );
			echo '</div>';
			$this->bp_pane_end();
		}
		$this->bp_tabs_end();
	}

	private function render_section_loadmore( array $s ): void {
		$this->bp_tabs_start( 'loadmore' );
		foreach ( $this->breakpoints() as $bp => $label ) {
			$this->bp_pane_start( 'loadmore', $bp, $bp === 'desktop' );
			echo '<div class="row g-3">';

			echo '<div class="col-12"><h6 class="otw-section-heading">' . esc_html__( 'Typography', 'otw-testimonials' ) . '</h6></div>';
			$this->field_color( "loadmore_color_{$bp}", __( 'Text Color', 'otw-testimonials' ), $s );
			$this->field_number( "loadmore_font_size_{$bp}", __( 'Font Size (px)', 'otw-testimonials' ), $s );
			$this->field_select( "loadmore_font_weight_{$bp}", __( 'Font Weight', 'otw-testimonials' ), $s, $this->font_weight_options() );

			echo '<div class="col-12"><h6 class="otw-section-heading mt-2">' . esc_html__( 'Background & Border', 'otw-testimonials' ) . '</h6></div>';
			$this->field_color( "loadmore_bg_{$bp}", __( 'Background Color', 'otw-testimonials' ), $s );
			$this->field_number( "loadmore_border_width_{$bp}", __( 'Border Width (px)', 'otw-testimonials' ), $s );
			$this->field_color( "loadmore_border_color_{$bp}", __( 'Border Color', 'otw-testimonials' ), $s );
			$this->field_select( "loadmore_border_style_{$bp}", __( 'Border Style', 'otw-testimonials' ), $s, $this->border_style_options() );
			$this->field_number( "loadmore_border_radius_{$bp}", __( 'Border Radius (px)', 'otw-testimonials' ), $s );

			echo '<div class="col-12"><h6 class="otw-section-heading mt-2">' . esc_html__( 'Padding (px)', 'otw-testimonials' ) . '</h6></div>';
			$this->field_number( "loadmore_pad_top_{$bp}", __( 'Top', 'otw-testimonials' ), $s, 'col-sm-3' );
			$this->field_number( "loadmore_pad_right_{$bp}", __( 'Right', 'otw-testimonials' ), $s, 'col-sm-3' );
			$this->field_number( "loadmore_pad_bottom_{$bp}", __( 'Bottom', 'otw-testimonials' ), $s, 'col-sm-3' );
			$this->field_number( "loadmore_pad_left_{$bp}", __( 'Left', 'otw-testimonials' ), $s, 'col-sm-3' );

			echo '<div class="col-12"><h6 class="otw-section-heading mt-2">' . esc_html__( 'Hover State', 'otw-testimonials' ) . '</h6></div>';
			$this->field_color( "loadmore_hover_color_{$bp}", __( 'Hover Text Color', 'otw-testimonials' ), $s );
			$this->field_color( "loadmore_hover_bg_{$bp}", __( 'Hover Background', 'otw-testimonials' ), $s );

			echo '</div>';
			$this->bp_pane_end();
		}
		$this->bp_tabs_end();
	}

	private function render_section_carousel( array $s ): void {
		?>
		<ul class="nav nav-pills nav-sm mb-3" id="otwCarouselTabs" role="tablist">
			<li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#otw-car-arrows" type="button"><?php esc_html_e( 'Arrows', 'otw-testimonials' ); ?></button></li>
			<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#otw-car-dots" type="button"><?php esc_html_e( 'Pagination Dots', 'otw-testimonials' ); ?></button></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade show active" id="otw-car-arrows">
				<?php
				$this->bp_tabs_start( 'arrows' );
				foreach ( $this->breakpoints() as $bp => $label ) {
					$this->bp_pane_start( 'arrows', $bp, $bp === 'desktop' );
					echo '<div class="row g-3">';
					$this->field_color( "arrow_color_{$bp}", __( 'Arrow Color', 'otw-testimonials' ), $s );
					$this->field_color( "arrow_bg_{$bp}", __( 'Arrow Background', 'otw-testimonials' ), $s );
					$this->field_number( "arrow_size_{$bp}", __( 'Arrow Size (px)', 'otw-testimonials' ), $s );
					$this->field_number( "arrow_border_radius_{$bp}", __( 'Border Radius (px)', 'otw-testimonials' ), $s );
					echo '</div>';
					$this->bp_pane_end();
				}
				$this->bp_tabs_end();
				?>
			</div>
			<div class="tab-pane fade" id="otw-car-dots">
				<?php
				$this->bp_tabs_start( 'dots' );
				foreach ( $this->breakpoints() as $bp => $label ) {
					$this->bp_pane_start( 'dots', $bp, $bp === 'desktop' );
					echo '<div class="row g-3">';
					$this->field_color( "dot_color_{$bp}", __( 'Dot Color', 'otw-testimonials' ), $s );
					$this->field_color( "dot_active_color_{$bp}", __( 'Active Dot Color', 'otw-testimonials' ), $s );
					$this->field_number( "dot_size_{$bp}", __( 'Dot Size (px)', 'otw-testimonials' ), $s );
					echo '</div>';
					$this->bp_pane_end();
				}
				$this->bp_tabs_end();
				?>
			</div>
		</div>
		<?php
	}

	/* ─────────────────────────────────────────────────────────────────────── */
	/*  Breakpoint tab helpers                                                  */
	/* ─────────────────────────────────────────────────────────────────────── */

	private function breakpoints(): array {
		return array(
			'desktop' => __( 'Desktop ≥1400px', 'otw-testimonials' ),
			'laptop'  => __( 'Laptop 1200–1399px', 'otw-testimonials' ),
			'tablet'  => __( 'Tablet 768–1199px', 'otw-testimonials' ),
			'mobile'  => __( 'Mobile &lt;768px', 'otw-testimonials' ),
		);
	}

	private function bp_tabs_start( string $group ): void {
		echo '<ul class="nav nav-tabs nav-sm otw-bp-tabs mb-3" id="otwBp-' . esc_attr( $group ) . '" role="tablist">';
		foreach ( $this->breakpoints() as $bp => $label ) {
			$active = $bp === 'desktop' ? 'active' : '';
			printf(
				'<li class="nav-item" role="presentation"><button class="nav-link %s" data-bs-toggle="tab" data-bs-target="#otw-%s-%s" type="button" role="tab">%s</button></li>',
				esc_attr( $active ),
				esc_attr( $group ),
				esc_attr( $bp ),
				wp_kses_post( $label )
			);
		}
		echo '</ul>';
		echo '<div class="tab-content">';
	}

	private function bp_tabs_end(): void {
		echo '</div>';
	}

	private function bp_pane_start( string $group, string $bp, bool $active ): void {
		printf(
			'<div class="tab-pane fade %s" id="otw-%s-%s" role="tabpanel">',
			$active ? 'show active' : '',
			esc_attr( $group ),
			esc_attr( $bp )
		);
	}

	private function bp_pane_end(): void {
		echo '</div>';
	}

	/* ─────────────────────────────────────────────────────────────────────── */
	/*  Field render helpers                                                    */
	/* ─────────────────────────────────────────────────────────────────────── */

	private function field_color( string $key, string $label, array $s, string $col = 'col-sm-4' ): void {
		$val = esc_attr( $s[ $key ] ?? '' );
		printf(
			'<div class="%s"><label class="form-label fw-medium small mb-1" for="%s">%s</label>'
			. '<input type="text" id="%s" name="otw_design[%s]" value="%s" class="form-control otw-color-picker" data-default-color=""></div>',
			esc_attr( $col ), esc_attr( $key ), esc_html( $label ),
			esc_attr( $key ), esc_attr( $key ), $val
		);
	}

	private function field_number( string $key, string $label, array $s, string $col = 'col-sm-4' ): void {
		$val = esc_attr( $s[ $key ] ?? '' );
		printf(
			'<div class="%s"><label class="form-label fw-medium small mb-1" for="%s">%s</label>'
			. '<input type="number" id="%s" name="otw_design[%s]" value="%s" class="form-control" min="0" step="1" placeholder="—"></div>',
			esc_attr( $col ), esc_attr( $key ), esc_html( $label ),
			esc_attr( $key ), esc_attr( $key ), $val
		);
	}

	private function field_float( string $key, string $label, array $s, string $placeholder = '', string $col = 'col-sm-4' ): void {
		$val = esc_attr( $s[ $key ] ?? '' );
		printf(
			'<div class="%s"><label class="form-label fw-medium small mb-1" for="%s">%s</label>'
			. '<input type="number" id="%s" name="otw_design[%s]" value="%s" class="form-control" min="0" step="0.01" placeholder="%s"></div>',
			esc_attr( $col ), esc_attr( $key ), esc_html( $label ),
			esc_attr( $key ), esc_attr( $key ), $val, esc_attr( $placeholder )
		);
	}

	private function field_text( string $key, string $label, array $s, string $placeholder = '', string $col = 'col-sm-6' ): void {
		$val = esc_attr( $s[ $key ] ?? '' );
		printf(
			'<div class="%s"><label class="form-label fw-medium small mb-1" for="%s">%s</label>'
			. '<input type="text" id="%s" name="otw_design[%s]" value="%s" class="form-control" placeholder="%s"></div>',
			esc_attr( $col ), esc_attr( $key ), esc_html( $label ),
			esc_attr( $key ), esc_attr( $key ), $val, esc_attr( $placeholder )
		);
	}

	private function field_select( string $key, string $label, array $s, array $options, string $col = 'col-sm-4' ): void {
		$current = $s[ $key ] ?? '';
		$opts    = '';
		foreach ( $options as $opt_val => $opt_label ) {
			$opts .= sprintf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $opt_val ),
				selected( $current, $opt_val, false ),
				esc_html( $opt_label )
			);
		}
		printf(
			'<div class="%s"><label class="form-label fw-medium small mb-1" for="%s">%s</label>'
			. '<select id="%s" name="otw_design[%s]" class="form-select otw-design-select">%s</select></div>',
			esc_attr( $col ), esc_attr( $key ), esc_html( $label ),
			esc_attr( $key ), esc_attr( $key ), $opts
		);
	}

	/* ─────────────────────────────────────────────────────────────────────── */
	/*  Option lists                                                            */
	/* ─────────────────────────────────────────────────────────────────────── */

	private function border_style_options(): array {
		return array(
			''       => __( '— default —', 'otw-testimonials' ),
			'none'   => __( 'None', 'otw-testimonials' ),
			'solid'  => __( 'Solid', 'otw-testimonials' ),
			'dashed' => __( 'Dashed', 'otw-testimonials' ),
			'dotted' => __( 'Dotted', 'otw-testimonials' ),
			'double' => __( 'Double', 'otw-testimonials' ),
		);
	}

	private function font_weight_options(): array {
		return array(
			''    => __( '— default —', 'otw-testimonials' ),
			'300' => '300 (Light)',
			'400' => '400 (Normal)',
			'500' => '500 (Medium)',
			'600' => '600 (Semi-Bold)',
			'700' => '700 (Bold)',
			'800' => '800 (Extra-Bold)',
			'900' => '900 (Black)',
		);
	}
}
