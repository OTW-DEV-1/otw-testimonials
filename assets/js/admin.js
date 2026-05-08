(function ($) {
    'use strict';

    $(function () {

        /* ── Select2 ────────────────────────────────────────────── */
        if ( $.fn.select2 ) {
            $('#otw_rating, #otw_platform, #otw_status').select2({
                width: 'resolve',
                minimumResultsForSearch: Infinity,
            });

            // Design settings selects
            $('.otw-design-select').select2({
                width: 'resolve',
                minimumResultsForSearch: Infinity,
            });
        }

        /* ── WP Color Picker (design settings page) ─────────────── */
        if ( $.fn.wpColorPicker && $('.otw-color-picker').length ) {
            $('.otw-color-picker').wpColorPicker();
        }

        /* ── Tab switcher for design settings page ───────────────────
         * Bootstrap 5 data-bs-toggle="tab" conflicts with WP admin's
         * own event handlers. We implement tab switching in plain jQuery
         * so it works regardless of whether Bootstrap JS loads.
         * ───────────────────────────────────────────────────────────── */
        $(document).on('click', '#otw-design-form [data-bs-toggle="tab"]', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn   = $(this);
            var target = $btn.attr('data-bs-target');

            if ( ! target ) return;
            if ( $btn.hasClass('active') ) return;

            // Deactivate all nav-links in this specific nav.
            $btn.closest('.nav').find('.nav-link').removeClass('active');
            $btn.addClass('active');

            // Find the sibling .tab-content (or .otw-tab-card which doubles as one).
            var $nav     = $btn.closest('.nav');
            var $content = $nav.next('.tab-content, .otw-tab-card');

            if ( ! $content.length ) {
                // Nested nav inside a pane: look for sibling tab-content.
                $content = $nav.siblings('.tab-content, .otw-tab-card').first();
            }

            // Swap active pane.
            $content.children('.tab-pane').removeClass('active show');
            $content.children(target).addClass('active show');

            // Re-init Select2 elements that become visible after a tab switch
            // (Select2 calculates width on visible elements only).
            if ( $.fn.select2 ) {
                $content.find(target).find('.otw-design-select').each(function () {
                    if ( $(this).hasClass('select2-hidden-accessible') ) {
                        $(this).select2( 'destroy' );
                    }
                    $(this).select2({ width: 'resolve', minimumResultsForSearch: Infinity });
                });
            }
        });

        /* ── Media uploader ─────────────────────────────────────── */
        var frame;

        $('#otw-upload-btn').on('click', function (e) {
            e.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'Select Image',
                button: { text: 'Use this image' },
                multiple: false,
                library: { type: 'image' },
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                var url = attachment.sizes && attachment.sizes.thumbnail
                    ? attachment.sizes.thumbnail.url
                    : attachment.url;

                $('#otw_image_id').val(attachment.id);
                $('#otw-image-preview').html('<img src="' + url + '" alt="">').show();
                $('#otw-remove-btn').show();
            });

            frame.open();
        });

        $('#otw-remove-btn').on('click', function (e) {
            e.preventDefault();
            $('#otw_image_id').val('0');
            $('#otw-image-preview').html('').hide();
            $(this).hide();
        });

        /* ── Gallery uploader ───────────────────────────────────── */
        var galleryFrame;
        var $galleryInput   = $('#otw_gallery_ids');
        var $galleryPreview = $('#otw-gallery-preview');

        // Parse existing IDs from the hidden input.
        var galleryIds = [];
        try { galleryIds = JSON.parse($galleryInput.val() || '[]'); } catch(e) { galleryIds = []; }
        if (!Array.isArray(galleryIds)) galleryIds = [];

        function serializeGallery() {
            $galleryInput.val(JSON.stringify(galleryIds));
        }

        function addGalleryItem(id, thumbUrl) {
            if (galleryIds.indexOf(id) !== -1) return; // already added
            galleryIds.push(id);
            $galleryPreview.append(
                '<div class="otw-gallery-item" data-id="' + id + '">' +
                    '<img src="' + thumbUrl + '" alt="">' +
                    '<button type="button" class="otw-gallery-item__remove" aria-label="Remove">&times;</button>' +
                '</div>'
            );
            serializeGallery();
        }

        $('#otw-gallery-add-btn').on('click', function (e) {
            e.preventDefault();

            if (galleryFrame) {
                galleryFrame.open();
                return;
            }

            galleryFrame = wp.media({
                title: 'Select Gallery Images',
                button: { text: 'Add to gallery' },
                multiple: true,
                library: { type: 'image' },
            });

            galleryFrame.on('select', function () {
                var selection = galleryFrame.state().get('selection');
                selection.each(function (attachment) {
                    var data  = attachment.toJSON();
                    var thumb = data.sizes && data.sizes.thumbnail ? data.sizes.thumbnail.url : data.url;
                    addGalleryItem(data.id, thumb);
                });
            });

            galleryFrame.open();
        });

        $galleryPreview.on('click', '.otw-gallery-item__remove', function () {
            var $item = $(this).closest('.otw-gallery-item');
            var id    = parseInt($item.data('id'), 10);
            var idx   = galleryIds.indexOf(id);
            if (idx !== -1) galleryIds.splice(idx, 1);
            $item.remove();
            serializeGallery();
        });

        /* ── Related post AJAX search ───────────────────────────── */
        var $search  = $('#otw_related_post_search');
        var $hidden  = $('#otw_related_post_id');
        var $results = $('#otw-post-results');
        var $clear   = $('#otw-post-clear');
        var searchTimer;

        if ( ! $search.length ) return;

        $search.on('input', function () {
            clearTimeout(searchTimer);
            var term = $.trim($(this).val());

            if (term.length < 2) {
                $results.hide().empty();
                return;
            }

            $results.html('<div class="otw-post-result otw-post-result--loading">' + otwAdmin.searching + '</div>').show();

            searchTimer = setTimeout(function () {
                $.get(otwAdmin.ajaxurl, {
                    action: 'otw_search_posts',
                    nonce:  otwAdmin.searchNonce,
                    search: term,
                }, function (response) {
                    $results.empty();

                    if ( ! response.success || ! response.data.length) {
                        $results.html('<div class="otw-post-result otw-post-result--empty">' + otwAdmin.noResults + '</div>').show();
                        return;
                    }

                    $.each(response.data, function (i, item) {
                        $('<div class="otw-post-result" tabindex="0">')
                            .text(item.text)
                            .data('id', item.id)
                            .appendTo($results);
                    });

                    $results.show();
                });
            }, 300);
        });

        // Select a result.
        $results.on('click', '.otw-post-result[tabindex]', function () {
            var id   = $(this).data('id');
            var text = $(this).text();
            $hidden.val(id);
            $search.val(text);
            $results.hide().empty();
            $clear.show();
        });

        // Clear selection.
        $clear.on('click', function (e) {
            e.preventDefault();
            $hidden.val('0');
            $search.val('');
            $results.hide().empty();
            $(this).hide();
        });

        // Close dropdown when clicking outside.
        $(document).on('click', function (e) {
            if ( ! $(e.target).closest('.otw-post-search-wrap').length) {
                $results.hide();
            }
        });

        // Keyboard navigation in dropdown.
        $search.on('keydown', function (e) {
            if (e.key === 'Escape') {
                $results.hide().empty();
            }
        });
    });
})(jQuery);
