"use strict";

var checkoutData, oliverTaxResponse, oliverProductTaxes;

(function ($) {
  var acf_orderType = ".acf-field-5d25148656536";
  var acf_salesRep = ".acf-field-5d25156b56537";
  var acf_affiliate = ".acf-field-5d251671a38b3";
  var acf_ticket = ".acf-field-5d4a0a0c75c12";
  jQuery(document).ready(function ($) {
    if ($("body").hasClass("single-product")) {
      // ADDS SPAN TO ADD TO CART BUTTONS TO REMOVE THE SKEW CSS
      $(".single_add_to_cart_button").wrapInner("<span></span>");
    }

    if ($("body").hasClass("woocommerce-page")) {
      // ADDS SPAN TO ADD TO CART BUTTONS TO REMOVE THE SKEW CSS
      $(".order_details tfoot tr:last-child").remove();
    }

    if ($("body").hasClass("term-bats")) {
      // Replaces Prod Cat with TRUE / 2020
      $(".uabb-woo-product-category").text(function () {
        return $(this).text().replace("Bats", "TRUE / 2020");
      });
    }

    if ($("body").hasClass("single-tribe_events")) {
      // ADDS SPAN TO ADD TO CART BUTTONS TO REMOVE THE SKEW CSS
      $(".tribe-button").wrapInner("<span></span>").parent().addClass("cta-btn solid text-center"); // ADDS TICKETS REMAINING TO CTA

      $(".cta-tickets-remaining .uabb-marketing-subheading").text(function () {
        if ($(".available-stock").length) {
          return "Only " + $(".available-stock").first().text() + " Spots Available";
        }
      });
    } // AFFILIATE JOIN FORM


    if ($("body").hasClass("page-id-671")) {
      $("#affwp-user-login").parent().prepend("<p class='helper'>Please choose a recognizable user name for you or your organization. Do not include any special characters. This CAN NOT be changed later. </p>");
      $("#affwp-register-form legend").after("<p class='helper'>The TRUE Affiliate program is invite only! To apply, you'll need a referral code that is sent to your email. Please enter that below. </p>");
    }
  });
  $(function () {
    var options = {
      byRow: true,
      property: 'max-height',
      target: $('.matchThis'),
      remove: false
    };
    $('.matchHeight').matchHeight(options);
  });
})(jQuery);