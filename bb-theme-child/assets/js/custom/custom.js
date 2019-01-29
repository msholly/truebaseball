jQuery(document).ready(function($) {
    if ($("body").hasClass("single-product")) {
        // ADDS SPAN TO ADD TO CART BUTTONS TO REMOVE THE SKEW CSS
        $(".single_add_to_cart_button").wrapInner( "<span></span>")
    }
});
