"use strict";

(function ($) {
  jQuery(document).ready(function ($) {
    // if ($("body").hasClass("page-tribe-attendee-registration") || $("body").hasClass("page-template-page-tribe-attendee-registration")) {
    // console.log("attendee page")
    // makeCTABtn($(".tribe-block__tickets__item__attendee__fields__form button"), false, true);
    // makeCTABtn($(".tribe-block__tickets__registration__checkout__submit"), true, true);
    // Disclaimer popup via JS
    // $(".tribe-tickets-meta-fieldset__checkbox-radio").each(function (index) {
    // 	var str = $('.tribe-tickets-meta-label').text();
    // 	console.log(str)
    // 	if (str.toLowerCase().indexOf("waiver") >= 0) {
    // 		var markup = "Did you read and do you accept the <a href='/waiver-and-release-of-liability/' target='_blank' class='open-disclaimer'>Waiver and Release of Liability</a>? <small>(opens in a new tab)</small>";
    // 		$('.tribe-tickets-meta-label h3').html(markup)
    // 	}
    // });
    // }
    if ($("body").hasClass("woocommerce-cart")) {
      // CTA STYLE FOR BLACK FRIDAY NOTIFICATION
      $(".woocommerce-info a:contains('GET DEAL')").each(function () {
        $(this).parent().addClass("cta-notification");
      });
    }

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
      makeCTABtn($(".tribe-button"), true, true); // ADDS TICKETS REMAINING TO CTA

      $(".cta-tickets-remaining .uabb-marketing-subheading").text(function () {
        if ($(".available-stock").length) {
          return "Only " + $(".available-stock").first().text() + " Spots Available";
        }
      });
    } // AFFILIATE JOIN FORM


    if ($("body").hasClass("page-id-671") || $("body").hasClass("page-id-667")) {
      $("#affwp-user-login").parent().prepend("<hr><p class='helper'>Please choose a recognizable user name for you or your organization. This will be used in your unique Tracking URL that you will give to your clients. Do not include any special characters. This CAN NOT be changed later. </p><p class='helper'>Example: https://truediamondscience.com/affiliate/battingcage101/ </p>");
      $("#affwp-register-form legend").after("<p class='helper'>The TRUE Affiliate program is invite only! To apply, you'll need a referral code that is sent to your email. Please enter that below. </p>");
    } // Ninja Forms - Datepicker Customizations


    if (typeof Marionette !== 'undefined') {
      new (Marionette.Object.extend({
        initialize: function initialize() {
          this.listenTo(Backbone.Radio.channel('pikaday'), 'init', this.modifyDatepicker);
        },
        modifyDatepicker: function modifyDatepicker(dateObject, fieldModel) {
          // dateObject.pikaday.setDate( '04/11/2016' );
          dateObject.pikaday.gotoDate(moment().add(14, 'days').toDate());
          dateObject.pikaday.setMinDate(moment().add(14, 'days').toDate());
        }
      }))();
    }

    function makeCTABtn(element, solid, center) {
      var action = element.wrapInner("<span></span>").wrap("<div class='cta-btn'></div>");

      if (solid) {
        action.parent().addClass("solid");
      }

      if (center) {
        action.parent().addClass("text-center");
      }

      return action;
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