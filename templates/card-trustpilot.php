<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Trustpilot review card template.
 *
 * @var object $testimonial
 */

$image_url = $testimonial->image_id ? wp_get_attachment_image_url( $testimonial->image_id, 'thumbnail' ) : '';
?>
<div class="otw-testimonial-card otw-card--trustpilot">
    <div class="otw-card__header">
        <div class="otw-card__rating otw-card__rating--trustpilot">
            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                <span class="otw-tp-star <?php echo $i <= $testimonial->rating ? 'otw-tp-star--filled' : 'otw-tp-star--empty'; ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </span>
            <?php endfor; ?>
        </div>
        <div class="otw-card__platform-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="#00B67A"/>
            </svg>
        </div>
    </div>

    <div class="otw-card__content">
        <div class="otw-content-body"><?php echo wp_kses_post( $testimonial->description ); ?></div>
    </div>

    <div class="otw-card__footer">
        <div class="otw-card__author-info">
            <?php if ( $image_url ) : ?>
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $testimonial->author_name ); ?>" class="otw-card__avatar otw-card__avatar--small">
            <?php endif; ?>
            <div class="otw-card__author-meta">
                <span class="otw-card__author-name"><?php echo esc_html( $testimonial->title ); ?></span>
                <?php if ( ! empty( $testimonial->author_name ) ) : ?>
                    <span class="otw-card__position"><?php echo esc_html( $testimonial->author_name ); ?></span>
                <?php endif; ?>
                <span class="otw-card__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $testimonial->created_at ) ) ); ?></span>
            </div>
        </div>
    </div>
</div>
