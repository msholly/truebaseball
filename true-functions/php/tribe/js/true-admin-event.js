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
    });


})(jQuery);