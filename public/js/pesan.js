var receiver_id = "";
var my_id = "{{ Auth::id() }}";
$(document).ready(function () {
    // ajax setup from csrf token
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher("454331a90dcaf309d7e6", {
        cluster: "ap1",
    });

    var channel = pusher.subscribe("my-channel");
    channel.bind("my-event", function (data) {
        // alert(JSON.stringify(data));
        if (my_id == data.from) {
            // alert("sender");
            $('#' + data.to).click();
        } else if (my_id == data.to) {
            if (receiver_id == data.from) {
                $("#" + data.from).click();
            } else {
                var pending = parseInt(
                    $("#" + data.from)
                        .find(".pending")
                        .html()
                );

                if (pending) {
                    $("#" + data.from)
                        .find(".pending")
                        .html(pending + 1);
                } else {
                    $("#" + data.from).append('<span class="pending">1</span>');
                }
            }
        }
    });

    $(".user").click(function () {
        $(".user").removeClass("active");
        $(this).addClass("active");
        $(this).find('.pending').remove();

        receiver_id = $(this).attr("id");
        $.ajax({
            type: "get",
            url: "pesan/" + receiver_id,
            data: "",
            cache: false,
            success: function (data) {
                $("#pesan").html(data);
                scrollToBottomFunc();
            },
        });
    });

    $(document).on("keyup", ".input-text input", function (e) {
        var pesan = $(this).val();
        // check if enter key is pressed end messade is not null also receiver is selected
        if (e.keyCode == 13 && pesan != "" && receiver_id != "") {
            // alert(pesan);
            $(this).val(""); // while pressed enter text box will be empty

            var datastr = "receiver_id=" + receiver_id + "&pesan=" + pesan;
            $.ajax({
                type: "post",
                url: "pesan", //need to create this post route
                data: datastr,
                cache: false,
                success: function (data) {},
                error: function (jqXHR, status, err) {},
                complete: function () {
                    scrollToBottomFunc();
                },
            });
        }
    });

    function scrollToBottomFunc(){
        $('.pesan-wrapper').animate({
            scrollTop: $('.pesan-wrapper').get(0).scrollHeight
        }, 50);
    }
});
