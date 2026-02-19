<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Google review card template.
 *
 * @var object $testimonial
 */

$image_url = $testimonial->image_id ? wp_get_attachment_image_url( $testimonial->image_id, 'thumbnail' ) : '';
?>
<div class="otw-testimonial-card otw-card--google">
    <div class="otw-card__header">
        <div class="otw-card__author-info">
            <?php if ( $image_url ) : ?>
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $testimonial->author_name ); ?>" class="otw-card__avatar">
            <?php else : ?>
                <div class="otw-card__avatar otw-card__avatar--placeholder">
                    <?php echo esc_html( mb_strtoupper( mb_substr( $testimonial->author_name, 0, 1 ) ) ); ?>
                </div>
            <?php endif; ?>
            <div class="otw-card__author-meta">
                <span class="otw-card__author-name"><?php echo esc_html( $testimonial->author_name ); ?></span>
                <span class="otw-card__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $testimonial->created_at ) ) ); ?></span>
            </div>
        </div>
        <div class="otw-card__platform-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
        </div>
    </div>

    <div class="otw-card__rating otw-card__rating--google">
        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
            <svg class="otw-star <?php echo $i <= $testimonial->rating ? 'otw-star--filled' : 'otw-star--empty'; ?>" width="18" height="18" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        <?php endfor; ?>
    </div>

    <?php if ( ! empty( $testimonial->title ) ) : ?>
        <h4 class="otw-card__title"><?php echo esc_html( $testimonial->title ); ?></h4>
    <?php endif; ?>

    <div class="otw-card__content">
        <?php echo wp_kses_post( $testimonial->description ); ?>
    </div>
</div>
