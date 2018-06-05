jQuery(function ($) {
    var defaultOwlOptions = {
        itemClass: 'item',
        navContainerClass: 'nav',
        navClass: ['prev', 'next'],
        dotsClass: 'dots',
        dotClass: 'dot',
        loop: false,
        dots: true,
        nav: false,
        navText: ['&lsaquo;', '&rsaquo;'],
        responsive: {
            0: {items: 1, stagePadding: 20, margin: 5},
            480: {items: 2, stagePadding: 25, margin: 10},
            768: {items: 3, margin: 15},
            1024: {items: 3, margin: 30},
            1200: {items: 4, margin: 22.49, nav: true}
        }
    };

    $('.social-feed-container.owl-carousel').owlCarousel(defaultOwlOptions);

    $('.social-feed-container.masonry').masonry({
      itemSelector: '.social-feed-item',
    });

    $('.social-feed-container .social-feed-title, .social-feed-container .social-feed-message').dotdotdot({
        watch: true
    });

    $('[data-identifier="pxa-load-ajax-feed"]').each(function () {
        var $this = $(this),
            uri = $this.data('uri');

        $.getJSON(uri)
            .done(function (data) {
                if (data.success) {
                    $this.html(data.html);
                    $this.find('.social-feed-container.owl-carousel').owlCarousel(defaultOwlOptions);
                    $this.find('.social-feed-container.masonry').masonry({
                        itemSelector: '.social-feed-item',
                    });
                    $this.find('.social-feed-container .social-feed-title, .social-feed-container .social-feed-message').dotdotdot({
                        watch: true
                    });
                }
            });
    });
});
