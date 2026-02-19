(function ($) {
    'use strict';

    $(function () {

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
