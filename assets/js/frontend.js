(function () {
    'use strict';

    /* ── Carousel ───────────────────────────────────────────────── */
    function initCarousels() {
        var carousels = document.querySelectorAll('.otw-testimonials-carousel');

        carousels.forEach(function (el) {
            var container = el.querySelector('.swiper');
            if (!container || container.swiper) return;

            var config = {
                slidesPerView: parseInt(el.dataset.colsMobile, 10) || 1,
                spaceBetween: parseInt(el.dataset.gap, 10) || 24,
                loop: el.dataset.loop === '1',
                autoHeight: false,
                breakpoints: {
                    768: {
                        slidesPerView: parseInt(el.dataset.colsTablet, 10) || 2,
                    },
                    1025: {
                        slidesPerView: parseInt(el.dataset.cols, 10) || 3,
                    },
                },
            };

            if (el.dataset.autoplay === '1') {
                config.autoplay = {
                    delay: parseInt(el.dataset.autoplaySpeed, 10) || 3000,
                    disableOnInteraction: false,
                };
            }

            if (el.dataset.arrows === '1') {
                config.navigation = {
                    nextEl: el.querySelector('.swiper-button-next'),
                    prevEl: el.querySelector('.swiper-button-prev'),
                };
            }

            if (el.dataset.dots === '1') {
                config.pagination = {
                    el: el.querySelector('.swiper-pagination'),
                    clickable: true,
                };
            }

            new Swiper(container, config);
        });
    }

    /* ── Read more / Modal ─────────────────────────────────────── */
    function buildModal() {
        if (document.getElementById('otw-testimonial-modal')) return;

        var modal = document.createElement('div');
        modal.id = 'otw-testimonial-modal';
        modal.className = 'otw-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.innerHTML =
            '<div class="otw-modal__overlay"></div>' +
            '<div class="otw-modal__panel">' +
                '<button type="button" class="otw-modal__close" aria-label="Close">' +
                    '<svg width="14" height="14" viewBox="0 0 14 14" fill="none">' +
                        '<path d="M1 1l12 12M13 1L1 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>' +
                    '</svg>' +
                '</button>' +
                '<div class="otw-modal__body"></div>' +
            '</div>';

        document.body.appendChild(modal);

        modal.querySelector('.otw-modal__overlay').addEventListener('click', closeModal);
        modal.querySelector('.otw-modal__close').addEventListener('click', closeModal);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });
    }

    function openModal(bodyEl) {
        var modal  = document.getElementById('otw-testimonial-modal');
        var target = modal.querySelector('.otw-modal__body');
        var card   = bodyEl.closest('.otw-testimonial-card');
        var html   = '';

        var authorInfo = card.querySelector('.otw-card__author-info');
        if (authorInfo) {
            html += '<div class="otw-modal__author">' + authorInfo.innerHTML + '</div>';
        }

        var rating = card.querySelector('.otw-card__rating');
        if (rating) {
            html += rating.outerHTML;
        }

        var title = card.querySelector('.otw-card__title');
        if (title) {
            html += title.outerHTML;
        }

        html += '<div class="otw-card__content otw-modal__content">' + bodyEl._otwFullHTML + '</div>';

        target.innerHTML = html;
        modal.classList.add('otw-modal--open');
        document.body.classList.add('otw-modal-open');
    }

    function closeModal() {
        var modal = document.getElementById('otw-testimonial-modal');
        if (!modal) return;
        modal.classList.remove('otw-modal--open');
        document.body.classList.remove('otw-modal-open');
    }

    function initReadMore() {
        buildModal();

        document.querySelectorAll('.otw-content-body').forEach(function (el) {
            if (el._otwInitialized) return;
            el._otwInitialized = true;
            el._otwFullHTML = el.innerHTML;

            el.classList.add('otw-content-body--clamped');

            if (el.scrollHeight > el.clientHeight + 1) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'otw-read-more-btn';
                btn.textContent = 'Read more';
                btn.addEventListener('click', function () { openModal(el); });
                el.parentNode.insertBefore(btn, el.nextSibling);
            } else {
                el.classList.remove('otw-content-body--clamped');
            }
        });
    }

    /* ── Init ──────────────────────────────────────────────────── */
    function init() {
        initCarousels();
        initReadMore();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    if (window.elementorFrontend) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/otw-testimonials.default', function () {
            init();
        });
    }
})();
