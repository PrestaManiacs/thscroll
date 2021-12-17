/**
 * 2006-2021 THECON SRL
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * YOU ARE NOT ALLOWED TO REDISTRIBUTE OR RESELL THIS FILE OR ANY OTHER FILE
 * USED BY THIS MODULE.
 *
 * @author    THECON SRL <contact@thecon.ro>
 * @copyright 2006-2021 THECON SRL
 * @license   Commercial
 */

$(document).ready(function() {
    $(".back-to-top a").click(function(e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, parseInt(THSCROLL_SPEED));
    });

    if (THSCROLL_SHOW_AFTER) {
        $(window).scroll(function() {
            if ($(this).scrollTop() > THSCROLL_SHOW_AFTER) {
                $('.back-to-top a').fadeIn();
            } else {
                $('.back-to-top a').fadeOut();
            }
        });
    }
});
