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

        //THEN ADD THEM TO THE RUN FUNCTION
        var run = function () {
            hashSelect();
            testimonialsSlider();
        };

        run();
    };

    $(function () {
        app();
    });
})(jQuery);