(function ($) {

    var app = function () {
        this.body = $('body');

        //CREATE FUNCTIONS FOR THE SITE
        var hashSelect = function () {

            var parts = window.location.href.split('/');

            if (parts.length > 0) {
                $('#location').val(parts[parts.length - 1].split('?')[0]);
            }
        };

        var testimonialsSlider = function () {

            var $slickContainer = $('.testimonials-slider');

            $slickContainer.slick({
                arrows: true,

                responsive: [
                    {
                        breakpoint: 767,
                        settings: {
                            arrows: false
                        }
                    }
                ]
            });
        };

        var featuredCarousel = function () {

            var $slickContainer = $('.featured-listings-carousel');

            $slickContainer.each(function () {

                $(this).slick({
                    arrows: true,
                    dots: true,
                    infinite: true,
                    slidesToShow: 3,
                    slidesToScroll: 3,

                    responsive: [
                        {
                            breakpoint: 767,
                            settings: {
                                arrows: false,
                                slidesToShow: 1,
                                slidesToScroll: 1,
                            }
                        }
                    ]
                });
            });
        };

        var customLightbox = function () {
            $(document).on('click', '[data-toggle="lightbox"]', function (event) {
                event.preventDefault();
                $(this).ekkoLightbox();
            });
        };

        //THEN ADD THEM TO THE RUN FUNCTION
        var run = function () {
            hashSelect();
            testimonialsSlider();
            featuredCarousel();
            customLightbox();
        };

        run();
    };

    $(function () {
        app();
    });
})(jQuery);