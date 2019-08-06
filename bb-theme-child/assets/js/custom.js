"use strict";

(function ($) {
  var acf_orderType = ".acf-field-5d25148656536";
  var acf_salesRep = ".acf-field-5d25156b56537";
  var acf_affiliate = ".acf-field-5d251671a38b3";
  var acf_ticket = ".acf-field-5d49dc3602ebc";
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

    if ($("body").hasClass("page-template-page-oliver-pos-php")) {
      // ACF OLIVER
      var trueTag = Cookies.getJSON('truecustomtags');

      if (trueTag == undefined) {
        initCookies();
      } else {
        if (trueTag.ordertype) {
          hideOrderType(trueTag.ordertype);
        }

        if (trueTag.salesRep.length > 0) {
          hideSalesRep(trueTag.salesRep);
        }

        if (trueTag.affiliate.length > 0) {
          hideAffiliate(trueTag.affiliate);
        }
      }

      console.log("INITIAL COOKIE");
      console.log(trueTag);
      $('#acf-form').contents().unwrap();
      $('.acf-field').not('.acf-field--validate-email').wrap("<div class='col-sm-6'></div>");
      $('.acf-fields').addClass("row");
      $('.acf-button').hide();
    }

    $("#send-acf-to-oliver").click(function () {
      var trueTag = Cookies.getJSON('truecustomtags');
      var thisTicket = $(acf_ticket + " select").select2('data');

      if (trueTag.ordertypeVal == '') {
        if ($(acf_orderType + ' select').val()) {
          var ordertype = $(acf_orderType + " option:selected").text();
        } else {
          var ordertype = '';
        }

        var trueTag = {
          ordertypeVal: $(acf_orderType + ' select').val(),
          ordertype: ordertype,
          salesRep: $(acf_salesRep + " select").select2('data'),
          affiliate: $(acf_affiliate + " select").select2('data')
        };
      }

      sendToOliver(trueTag, thisTicket);
    });
    $("#clearAllTags").on("click", clearAll);
  });

  function sendToOliver(data, ticket) {
    console.log(data);

    if (data.ordertypeVal == '' || data.salesRep.length == 0) {
      // RESET EVENT TYPE SINCE THATS THE CHECK FOR ALOT
      clearAll();
      alert("Please Enter an Event Type and Sales Rep");
      return;
    }

    if (data.ordertypeVal === 'league' || data.ordertypeVal === 'facility_event') {
      console.log("IS LEAGUE");

      if (data.affiliate.length == 0) {
        alert("This order type requires an affiliate");
        return;
      }
    }

    if (data.ordertypeVal.length > 0) {
      // if data exist
      hideOrderType(data.ordertype);
    } else {// var ordertypeVal = $( acf_orderType + " option:selected").val();
      // var ordertype = $( acf_orderType + " option:selected").text();
    }

    if (data.salesRep.length > 0) {
      hideSalesRep(data.salesRep);
    } else {} // var salesRep = $( acf_salesRep + " select" ).select2('data');
    // HIDE IF LEFT BLANK, when sending


    hideAffiliate(data.affiliate);

    if (data.affiliate.length > 0) {} else {// var affiliate = $("#acf-field_5d251671a38b3").select2('data');
    }

    Cookies.set('truecustomtags', {
      ordertypeVal: data.ordertypeVal,
      ordertype: data.ordertype,
      salesRep: data.salesRep,
      affiliate: data.affiliate,
      ticket: ticket
    });
    returnCurrentCookie();
  }

  function clearAll() {
    // Cookies.remove('truecustomtags');
    initCookies();
    console.log(acf_orderType);
    $(".savedTag").remove();
    $(".acf-input").show();
    $(acf_orderType + " select, " + acf_salesRep + " select, " + acf_affiliate + " select, " + acf_ticket + " select").show().val("").trigger('change');
    returnCurrentCookie();
  }

  function hideOrderType(data) {
    var tag = $(' .tag-ordertype');
    $(acf_orderType + ' select').hide();

    if (tag) {
      tag.remove();
    }

    $(acf_orderType).append("<p class='savedTag tag-ordertype'>" + data + "</p>");
  }

  function hideSalesRep(data) {
    var tag = $(' .tag-sales');
    $(acf_salesRep + ' .acf-input').hide();

    if (tag) {
      tag.remove();
    }

    $(acf_salesRep).append("<p class='savedTag tag-sales'>" + data[0].text + "</p>");
  }

  function hideAffiliate(data) {
    var tag = $(' .tag-affiliate');
    $(acf_affiliate + ' .acf-input').hide();

    if (tag) {
      tag.remove();
    }

    if (data.length > 0) {
      $(acf_affiliate).append("<p class='savedTag tag-affiliate'>" + data[0].text + "</p>");
    } else {
      $(acf_affiliate).append("<p class='savedTag tag-affiliate'>N/A</p>");
    }
  }

  function initCookies() {
    var trueTag = {
      ordertypeVal: '',
      ordertype: '',
      salesRep: {},
      affiliate: {},
      ticket: {}
    };
    Cookies.set('truecustomtags', {
      ordertypeVal: trueTag.ordertypeVal,
      ordertype: trueTag.ordertype,
      salesRep: trueTag.salesRep,
      affiliate: trueTag.affiliate,
      ticket: trueTag.ticket
    });
  }

  function returnCurrentCookie() {
    var cookietest = Cookies.getJSON('truecustomtags');
    console.log(cookietest);
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
})(jQuery);