<!DOCTYPE html>
<html class="{{ Auth::guest() ? 'guest' : 'logged-in' }}">
<head>
	<title>Totals for Uber: All Your Uber Numbers in One Place</title>

    <link href='https://fonts.googleapis.com/css?family=Raleway:500,700,400' rel='stylesheet' type='text/css'>
    <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" async src="//platform.twitter.com/widgets.js"></script>

    <link rel="stylesheet" href="/css/all.css?v=1.1.2">
    <script type="text/javascript" src="/js/all.js?v=1.1.2"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#070716">

    <meta property="og:image" content="http://uber.totals.io/images/share.png"/>
    <meta property="og:image:secure_url" content="https://uber.totals.io/images/share.png" />
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@bryceadams">
    <meta name="twitter:title" content="Uber Totals">
    <meta name="twitter:description" content="Want to see your total Uber rides or how many hours you've spent in an Uber? Now you can!">
    <meta name="twitter:creator" content="@bryceadams">
    <meta name="twitter:image:src" content="http://uber.totals.io/images/share.png">
    <meta name="twitter:domain" content="uber.totals.io">

</head>
<body class="v-{{ str_replace('/', '-', Request::path() ) }}">

    <!-- Include and init Facebook JS SDK -->
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '1610053299209384',
                xfbml      : true,
                version    : 'v2.3'
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <div id="googlemaps"></div>
    <div id="page-loading"></div>

    <!-- Content / container -->
    <div class="container content-container">
        <div class="row">

            <div class="title-container">
                <a href="{{ action( 'PagesController@home') }}">
                    <h2 class="title"><strong>Totals</strong> for <img src="/images/uber-badge.png" class="uber-badge"/></h2>
                </a>
            </div>

            <div class="col-md-12">
                @yield('content')
            </div>

        </div>

        @include('partials.footer')
    </div>
    <!-- /.container -->

    <!-- Google Analytics tracking code -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-61899621-2', 'auto');
        ga('send', 'pageview');
    </script>

</body>
</html>