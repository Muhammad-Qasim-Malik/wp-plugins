jQuery(document).ready(function($) {
    // alert("Hello");
    $(".mq-tabs ul li").click(function() {
        var tab_id = $(this).data("tab");

        $(".mq-tabs ul li").removeClass("active");
        $(".mq-tab-pane").removeClass("active");

        $(this).addClass("active");
        $("#" + tab_id).addClass("active");
    });
});