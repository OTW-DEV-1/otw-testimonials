<?php
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OTW_Testimonials_CSS_Generator {

	private array  $settings;
	private string $scope;

	public function __construct( array $settings, string $scope = '.otw-testimonials-wrapper' ) {
		$this->settings = $settings;
		$this->scope    = $scope;
	}

	public function generate(): string {
		$css = '';

		$desktop = $this->generate_for_breakpoint( 'desktop' );
		if ( $desktop ) {
			$css .= $desktop;
		}

		$laptop = $this->generate_for_breakpoint( 'laptop' );
		if ( $laptop ) {
			$css .= "@media (min-width:1200px) and (max-width:1399px) {\n{$laptop}}\n";
		}

		$tablet = $this->generate_for_breakpoint( 'tablet' );
		if ( $tablet ) {
			$css .= "@media (min-width:768px) and (max-width:1199px) {\n{$tablet}}\n";
		}

		$mobile = $this->generate_for_breakpoint( 'mobile' );
		if ( $mobile ) {
			$css .= "@media (max-width:767px) {\n{$mobile}}\n";
		}

		return $css;
	}

	private function v( string $key, string $bp ): string {
		return trim( (string) ( $this->settings[ "{$key}_{$bp}" ] ?? '' ) );
	}

	private function generate_for_breakpoint( string $bp ): string {
		$s     = $this->scope;
		$rules = array();

		/* ── Card ─────────────────────────────────────────────────────── */
		$card = '';
		$v = $this->v( 'card_bg_color', $bp );
		if ( $v ) $card .= "background-color:{$v};";

		$bw = $this->v( 'card_border_width', $bp );
		$bc = $this->v( 'card_border_color', $bp );
		$bs = $this->v( 'card_border_style', $bp );
		if ( $bw !== '' ) $card .= "border-width:{$bw}px;";
		if ( $bc )        $card .= "border-color:{$bc};";
		if ( $bs )        $card .= "border-style:{$bs};";

		$tl = $this->v( 'card_radius_tl', $bp );
		$tr = $this->v( 'card_radius_tr', $bp );
		$br = $this->v( 'card_radius_br', $bp );
		$bl = $this->v( 'card_radius_bl', $bp );
		if ( $tl !== '' || $tr !== '' || $br !== '' || $bl !== '' ) {
			$card .= sprintf(
				'border-radius:%spx %spx %spx %spx;',
				$tl !== '' ? $tl : '0',
				$tr !== '' ? $tr : '0',
				$br !== '' ? $br : '0',
				$bl !== '' ? $bl : '0'
			);
		}

		$pt = $this->v( 'card_pad_top', $bp );
		$pr = $this->v( 'card_pad_right', $bp );
		$pb = $this->v( 'card_pad_bottom', $bp );
		$pl = $this->v( 'card_pad_left', $bp );
		if ( $pt !== '' || $pr !== '' || $pb !== '' || $pl !== '' ) {
			$card .= sprintf(
				'padding:%spx %spx %spx %spx;',
				$pt !== '' ? $pt : '0',
				$pr !== '' ? $pr : '0',
				$pb !== '' ? $pb : '0',
				$pl !== '' ? $pl : '0'
			);
		}

		$v = $this->v( 'card_box_shadow', $bp );
		if ( $v ) $card .= "box-shadow:{$v};";

		if ( $card ) {
			$rules[] = "{$s} .otw-testimonial-card { {$card} }";
		}

		// Card hover
		$hover = '';
		$v = $this->v( 'card_hover_bg', $bp );
		if ( $v ) $hover .= "background-color:{$v};";
		$v = $this->v( 'card_hover_shadow', $bp );
		if ( $v ) $hover .= "box-shadow:{$v};";
		$v = $this->v( 'card_hover_transform', $bp );
		if ( $v ) $hover .= "transform:{$v};";
		if ( $hover ) {
			$rules[] = "{$s} .otw-testimonial-card:hover { {$hover} }";
		}

		/* ── Avatar ───────────────────────────────────────────────────── */
		$avatar = '';
		$v = $this->v( 'avatar_size', $bp );
		if ( $v !== '' ) $avatar .= "width:{$v}px;height:{$v}px;min-width:{$v}px;";
		$v = $this->v( 'avatar_border_radius', $bp );
		if ( $v ) $avatar .= "border-radius:{$v};";
		$bw = $this->v( 'avatar_border_width', $bp );
		$bc = $this->v( 'avatar_border_color', $bp );
		$bs = $this->v( 'avatar_border_style', $bp );
		if ( $bw !== '' ) $avatar .= "border-width:{$bw}px;";
		if ( $bc )        $avatar .= "border-color:{$bc};";
		if ( $bs )        $avatar .= "border-style:{$bs};";
		if ( $avatar ) {
			$rules[] = "{$s} .otw-card__avatar { {$avatar} }";
		}

		/* ── Author Name ──────────────────────────────────────────────── */
		$name = '';
		$v = $this->v( 'title_color', $bp );
		if ( $v )   $name .= "color:{$v};";
		$v = $this->v( 'title_font_size', $bp );
		if ( $v !== '' ) $name .= "font-size:{$v}px;";
		$v = $this->v( 'title_font_weight', $bp );
		if ( $v )   $name .= "font-weight:{$v};";
		$v = $this->v( 'title_line_height', $bp );
		if ( $v !== '' ) $name .= "line-height:{$v};";
		if ( $name ) {
			$rules[] = "{$s} .otw-card__author-name { {$name} }";
		}

		/* ── Position ─────────────────────────────────────────────────── */
		$pos = '';
		$v = $this->v( 'position_color', $bp );
		if ( $v )   $pos .= "color:{$v};";
		$v = $this->v( 'position_font_size', $bp );
		if ( $v !== '' ) $pos .= "font-size:{$v}px;";
		$v = $this->v( 'position_font_weight', $bp );
		if ( $v )   $pos .= "font-weight:{$v};";
		if ( $pos ) {
			$rules[] = "{$s} .otw-card__position { {$pos} }";
		}

		/* ── Date ─────────────────────────────────────────────────────── */
		$date = '';
		$show = $this->v( 'date_display', $bp );
		if ( $show === 'hide' ) $date .= "display:none;";
		$v = $this->v( 'date_color', $bp );
		if ( $v )   $date .= "color:{$v};";
		$v = $this->v( 'date_font_size', $bp );
		if ( $v !== '' ) $date .= "font-size:{$v}px;";
		if ( $date ) {
			$rules[] = "{$s} .otw-card__date { {$date} }";
		}

		/* ── Content ──────────────────────────────────────────────────── */
		$content = '';
		$v = $this->v( 'content_color', $bp );
		if ( $v )   $content .= "color:{$v};";
		$v = $this->v( 'content_font_size', $bp );
		if ( $v !== '' ) $content .= "font-size:{$v}px;";
		$v = $this->v( 'content_line_height', $bp );
		if ( $v !== '' ) $content .= "line-height:{$v};";
		if ( $content ) {
			$rules[] = "{$s} .otw-card__content, {$s} .otw-content-body { {$content} }";
		}

		/* ── Rating Stars ─────────────────────────────────────────────── */
		$v = $this->v( 'star_size', $bp );
		if ( $v !== '' ) {
			$rules[] = "{$s} .otw-star { width:{$v}px;height:{$v}px; }";
			$rules[] = "{$s} .otw-tp-star { width:calc({$v}px + 8px);height:calc({$v}px + 8px); }";
			$rules[] = "{$s} .otw-tp-star svg { width:{$v}px;height:{$v}px; }";
		}
		$v = $this->v( 'star_color_filled', $bp );
		if ( $v ) {
			$rules[] = "{$s} .otw-star--filled path { fill:{$v}; }";
			$rules[] = "{$s} .otw-tp-star--filled { background-color:{$v}; }";
		}
		$v = $this->v( 'star_color_empty', $bp );
		if ( $v ) {
			$rules[] = "{$s} .otw-star--empty path { fill:{$v}; }";
			$rules[] = "{$s} .otw-tp-star--empty { background-color:{$v}; }";
		}

		/* ── Load More Button ─────────────────────────────────────────── */
		$lm = '';
		$v = $this->v( 'loadmore_color', $bp );
		if ( $v )   $lm .= "color:{$v};";
		$v = $this->v( 'loadmore_bg', $bp );
		if ( $v )   $lm .= "background-color:{$v};";
		$v = $this->v( 'loadmore_font_size', $bp );
		if ( $v !== '' ) $lm .= "font-size:{$v}px;";
		$v = $this->v( 'loadmore_font_weight', $bp );
		if ( $v )   $lm .= "font-weight:{$v};";
		$bw = $this->v( 'loadmore_border_width', $bp );
		$bc = $this->v( 'loadmore_border_color', $bp );
		$bs = $this->v( 'loadmore_border_style', $bp );
		if ( $bw !== '' ) $lm .= "border-width:{$bw}px;";
		if ( $bc )        $lm .= "border-color:{$bc};";
		if ( $bs )        $lm .= "border-style:{$bs};";
		$v = $this->v( 'loadmore_border_radius', $bp );
		if ( $v !== '' ) $lm .= "border-radius:{$v}px;";
		$pt = $this->v( 'loadmore_pad_top', $bp );
		$pr = $this->v( 'loadmore_pad_right', $bp );
		$pb = $this->v( 'loadmore_pad_bottom', $bp );
		$pl = $this->v( 'loadmore_pad_left', $bp );
		if ( $pt !== '' || $pr !== '' || $pb !== '' || $pl !== '' ) {
			$lm .= sprintf(
				'padding:%spx %spx %spx %spx;',
				$pt !== '' ? $pt : '0',
				$pr !== '' ? $pr : '0',
				$pb !== '' ? $pb : '0',
				$pl !== '' ? $pl : '0'
			);
		}
		if ( $lm ) {
			$rules[] = "{$s} .otw-load-more-btn { {$lm} }";
		}

		$lm_hover = '';
		$v = $this->v( 'loadmore_hover_color', $bp );
		if ( $v ) $lm_hover .= "color:{$v};";
		$v = $this->v( 'loadmore_hover_bg', $bp );
		if ( $v ) $lm_hover .= "background-color:{$v};";
		if ( $lm_hover ) {
			$rules[] = "{$s} .otw-load-more-btn:hover { {$lm_hover} }";
		}

		/* ── Carousel Arrows ──────────────────────────────────────────── */
		$arrow = '';
		$v = $this->v( 'arrow_color', $bp );
		if ( $v )   $arrow .= "color:{$v};--swiper-navigation-color:{$v};";
		$v = $this->v( 'arrow_bg', $bp );
		if ( $v )   $arrow .= "background-color:{$v};";
		$v = $this->v( 'arrow_size', $bp );
		if ( $v !== '' ) $arrow .= "--swiper-navigation-size:{$v}px;";
		$v = $this->v( 'arrow_border_radius', $bp );
		if ( $v !== '' ) $arrow .= "border-radius:{$v}px;";
		if ( $arrow ) {
			$rules[] = "{$s} .swiper-button-prev, {$s} .swiper-button-next { {$arrow} }";
		}

		/* ── Carousel Dots ────────────────────────────────────────────── */
		$v = $this->v( 'dot_color', $bp );
		if ( $v ) {
			$rules[] = "{$s} .swiper-pagination-bullet { background-color:{$v}; }";
		}
		$v = $this->v( 'dot_active_color', $bp );
		if ( $v ) {
			$rules[] = "{$s} .swiper-pagination-bullet-active { background-color:{$v}; }";
		}
		$v = $this->v( 'dot_size', $bp );
		if ( $v !== '' ) {
			$rules[] = "{$s} .swiper-pagination-bullet { width:{$v}px;height:{$v}px; }";
		}

		return $rules ? implode( "\n", $rules ) . "\n" : '';
	}
}
