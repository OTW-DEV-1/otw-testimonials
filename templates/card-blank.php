<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Blank / no-platform review card template.
 *
 * @var object $testimonial
 */

$image_url = $testimonial->image_id ? wp_get_attachment_image_url( $testimonial->image_id, 'thumbnail' ) : '';
?>
<div class="otw-testimonial-card otw-card--blank">
    <div class="otw-card__header">
        <div class="otw-card__author-info">
            <?php if ( $image_url ) : ?>
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $testimonial->title ); ?>" class="otw-card__avatar">
            <?php else : ?>
                <div class="otw-card__avatar otw-card__avatar--placeholder otw-card__avatar--blank">
                    <?php echo esc_html( mb_strtoupper( mb_substr( $testimonial->title, 0, 1 ) ) ); ?>
                </div>
            <?php endif; ?>
            <div class="otw-card__author-meta">
                <span class="otw-card__author-name"><?php echo esc_html( $testimonial->title ); ?></span>
                <?php if ( ! empty( $testimonial->author_name ) ) : ?>
                    <span class="otw-card__position"><?php echo esc_html( $testimonial->author_name ); ?></span>
                <?php endif; ?>
                <span class="otw-card__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $testimonial->created_at ) ) ); ?></span>
            </div>
        </div>
        <div class="otw-card__quote-icon" aria-hidden="true">
            <svg width="32" height="24" viewBox="0 0 32 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 24V14.667C0 6.577 4.405 1.811 13.216 0l1.451 2.418C10.094 3.663 7.752 6.39 7.126 10.667H13.333V24H0zm18.667 0V14.667C18.667 6.577 23.072 1.811 31.883 0L33.333 2.418C28.76 3.663 26.419 6.39 25.792 10.667H32V24H18.667z" fill="#e0e0e0"/>
            </svg>
        </div>
    </div>

    <div class="otw-card__rating otw-card__rating--blank">
        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
            <svg class="otw-star <?php echo $i <= $testimonial->rating ? 'otw-star--filled' : 'otw-star--empty'; ?>" width="18" height="18" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        <?php endfor; ?>
    </div>

    <div class="otw-card__content">
        <div class="otw-content-body"><?php echo wp_kses_post( $testimonial->description ); ?></div>
    </div>
</div>
