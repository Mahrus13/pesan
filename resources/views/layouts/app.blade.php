<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pesan.css') }}" rel="stylesheet">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <!-- <script src="js/pesan.js" type="text/javascript"></script> -->

    <script>
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

    </script>


</body>
</html>
