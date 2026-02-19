(function () {
    'use strict';

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

    // Init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCarousels);
    } else {
        initCarousels();
    }

    // Re-init for Elementor editor preview
    if (window.elementorFrontend) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/otw-testimonials.default', function ($scope) {
            initCarousels();
        });
    }
})();
