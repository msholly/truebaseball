"use strict";

jQuery(document).ready(function ($) {
  if ($("body").hasClass("single-product")) {
    // ADDS SPAN TO ADD TO CART BUTTONS TO REMOVE THE SKEW CSS
    $(".single_add_to_cart_button").wrapInner("<span></span>");
  }

  if ($("body").hasClass("term-bats")) {
    // Replaces Prod Cat with TRUE / 2020
    $(".uabb-woo-product-category").text(function () {
      return $(this).text().replace("Bats", "TRUE / 2020");
    });
  }

  $(function () {
    var options = {
      byRow: true,
      property: 'max-height',
      target: $('.matchThis'),
      remove: false
    };
    $('.matchHeight').matchHeight(options);
  });
});