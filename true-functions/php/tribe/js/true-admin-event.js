(function ($) {
    "use strict";

    // ANYTHING SET ON PAGE LOAD
    $(document).ready(function () {

        $("#ticket_form_toggle").on("click", custom_ticket_form_toggle);

        function custom_ticket_form_toggle() {
            console.log("Add Ticket Panel Clicked")
            $("#ticket_name").val("FREE FITTING");
            $("#ticket_price").val("0");
            $("#Tribe__Tickets_Plus__Commerce__WooCommerce__Main_global_stock_cap").val("1");
            $(".accordion-header.tribe_attendee_meta").trigger("click");
            $("#saved_ticket-attendee-info").val('3159').change();
        }

        var exists = $('.tagchecklist li:contains("child")').length; // see if element(s) exists that matches by checking length
        
        // Run only on child events
        $("#EventStartTime, #EventEndTime").change(function () {
            if (exists > 0) {
                var venue = $('#select2-saved_tribe_venue-container').attr("title");
                var type = " | TRUE Bat Hit+Fit Challenge | ";
                var start_time = $("#EventStartTime").val();
                var end_time = $("#EventEndTime").val();
                var auto_title = $.trim(venue) + type + start_time + ' - ' + end_time;
                console.log(auto_title)
                $('#title').val(auto_title).change();
            }

        });



    });


})(jQuery);