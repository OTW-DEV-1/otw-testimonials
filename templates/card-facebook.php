<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Facebook review card template.
 *
 * @var object $testimonial
 */

$image_url = $testimonial->image_id ? wp_get_attachment_image_url( $testimonial->image_id, 'thumbnail' ) : '';
$recommends = $testimonial->rating >= 4;
?>
<div class="otw-testimonial-card otw-card--facebook">
    <div class="otw-card__header">
        <div class="otw-card__author-info">
            <?php if ( $image_url ) : ?>
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $testimonial->title ); ?>" class="otw-card__avatar">
            <?php else : ?>
                <div class="otw-card__avatar otw-card__avatar--placeholder otw-card__avatar--fb">
                    <?php echo esc_html( mb_strtoupper( mb_substr( $testimonial->title, 0, 1 ) ) ); ?>
                </div>
            <?php endif; ?>
            <div class="otw-card__author-meta">
                <span class="otw-card__author-name"><?php echo esc_html( $testimonial->title ); ?></span>
                <?php if ( ! empty( $testimonial->author_name ) ) : ?>
                    <span class="otw-card__position"><?php echo esc_html( $testimonial->author_name ); ?></span>
                <?php endif; ?>
                <span class="otw-card__recommendation">
                    <?php if ( $recommends ) : ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#1877F2" style="vertical-align:middle;">
                            <path d="M2 21h4V9H2v12zm22-11a2 2 0 0 0-2-2h-6.31l.95-4.57.03-.32a1.49 1.49 0 0 0-.44-1.06L15.17 1 8.59 7.59C8.22 7.95 8 8.45 8 9v10a2 2 0 0 0 2 2h9a2.006 2.006 0 0 0 1.84-1.21l3.02-7.05c.09-.23.14-.47.14-.74v-2z"/>
                        </svg>
                        <?php esc_html_e( 'Recommends', 'otw-testimonials' ); ?>
                    <?php else : ?>
                        <?php esc_html_e( 'Does not recommend', 'otw-testimonials' ); ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        <div class="otw-card__platform-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="#1877F2" xmlns="http://www.w3.org/2000/svg">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
        </div>
    </div>

    <div class="otw-card__rating otw-card__rating--facebook">
        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
            <svg class="otw-star <?php echo $i <= $testimonial->rating ? 'otw-star--filled' : 'otw-star--empty'; ?>" width="18" height="18" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        <?php endfor; ?>
    </div>

    <div class="otw-card__content">
        <div class="otw-content-body"><?php echo wp_kses_post( $testimonial->description ); ?></div>
    </div>

    <div class="otw-card__footer">
        <span class="otw-card__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $testimonial->created_at ) ) ); ?></span>
    </div>
</div>
