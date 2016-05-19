jQuery(function ($) {
    $(".social-feed-container").owlCarousel({
        itemClass: "item",
        navContainerClass: "nav",
        navClass: ["prev", "next"],
        dotsClass: "dots",
        dotClass: "dot",
        loop: false,
        dots: true,
        nav: false,
        navText: ["", ""],
        responsive: {
            0: {items: 1, stagePadding: 20, margin: 5},
            480: {items: 2, stagePadding: 25, margin: 10},
            768: {items: 3, margin: 15},
            1024: {items: 3, margin: 30},
            1200: {items: 4, margin: 22.5, nav: true}
        }
    });
    $(".social-feed-container .social-feed-title, .social-feed-container .social-feed-message").dotdotdot({
        watch: true
    });
});