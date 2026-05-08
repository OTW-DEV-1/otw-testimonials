<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Instagram card template.
 *
 * @var object $testimonial
 */

$image_url    = $testimonial->image_id ? wp_get_attachment_image_url( $testimonial->image_id, 'thumbnail' ) : '';
$display_date = ! empty( $testimonial->testimonial_date ) ? $testimonial->testimonial_date : $testimonial->created_at;
?>
<div class="otw-testimonial-card otw-card--instagram otw-testimonial-<?php echo absint( $testimonial->id ); ?>">
    <div class="otw-card__header">
        <div class="otw-card__author-info">
            <?php if ( $image_url ) : ?>
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $testimonial->title ); ?>" class="otw-card__avatar">
            <?php else : ?>
                <div class="otw-card__avatar otw-card__avatar--placeholder otw-card__avatar--instagram">
                    <?php echo esc_html( mb_strtoupper( mb_substr( $testimonial->title, 0, 1 ) ) ); ?>
                </div>
            <?php endif; ?>
            <div class="otw-card__author-meta">
                <span class="otw-card__author-name"><?php echo esc_html( $testimonial->title ); ?></span>
                <?php if ( ! empty( $testimonial->author_name ) ) : ?>
                    <span class="otw-card__position"><?php echo esc_html( $testimonial->author_name ); ?></span>
                <?php endif; ?>
                <span class="otw-card__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $display_date ) ) ); ?></span>
            </div>
        </div>
        <div class="otw-card__platform-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="otw-ig-grad" x1="0%" y1="100%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#f09433"/>
                        <stop offset="25%" style="stop-color:#e6683c"/>
                        <stop offset="50%" style="stop-color:#dc2743"/>
                        <stop offset="75%" style="stop-color:#cc2366"/>
                        <stop offset="100%" style="stop-color:#bc1888"/>
                    </linearGradient>
                </defs>
                <rect width="24" height="24" rx="5.5" fill="url(#otw-ig-grad)"/>
                <circle cx="12" cy="12" r="4.5" fill="none" stroke="#fff" stroke-width="1.8"/>
                <circle cx="17.5" cy="6.5" r="1.2" fill="#fff"/>
            </svg>
        </div>
    </div>

    <div class="otw-card__rating otw-card__rating--instagram">
        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
            <svg class="otw-star <?php echo $i <= $testimonial->rating ? 'otw-star--filled' : 'otw-star--empty'; ?>" width="18" height="18" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        <?php endfor; ?>
    </div>

    <div class="otw-card__content">
        <div class="otw-content-body"><?php echo wp_kses_post( $testimonial->description ); ?></div>
    </div>

    <?php
    $gallery_ids = ! empty( $testimonial->gallery_ids )
        ? array_filter( array_map( 'absint', json_decode( $testimonial->gallery_ids, true ) ?: array() ) )
        : array();
    if ( ! empty( $gallery_ids ) ) :
        $count = count( $gallery_ids );
    ?>
    <div class="otw-card__gallery<?php echo $count === 1 ? ' otw-card__gallery--single' : ''; ?>">
        <?php foreach ( $gallery_ids as $gid ) :
            $full_url  = wp_get_attachment_image_url( $gid, 'full' );
            $thumb_url = wp_get_attachment_image_url( $gid, 'medium' );
            if ( ! $full_url ) continue;
        ?>
            <a href="<?php echo esc_url( $full_url ); ?>"
               class="otw-gallery__item glightbox"
               data-gallery="testimonial-<?php echo esc_attr( $testimonial->id ); ?>"
               data-type="image"
               data-elementor-open-lightbox="no">
                <img src="<?php echo esc_url( $thumb_url ?: $full_url ); ?>" alt="" loading="lazy">
            </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
