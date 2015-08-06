@extends( 'template' )
@section( 'content' )

    <div class="row">
        <div class="col-md-12 view-leaderboard dash">
            <a href="{{ action( 'PagesController@leaderboard' ) }}">Leaderboard</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="user-profile">
                <img src="{{ $photo }}" class="photo" width="60" />
                <h2>{{ $name }}'s <img src="/images/uber-badge.png" class="uber-badge "/> History!</h2>
            </div>
        </div>
    </div>

    <div class="row user-stats">
        <div class="col-md-6">
            <h2>{{ $trips_taken_count }}</h2>
            <h3>{{ plural($trips_taken_count, 'Ride', 'Rides') }} Taken</h3>
            <h4>{{ $trips_message }}</h4>
        </div>
        <div class="col-md-6">
            <h2>{{ $miles_driven_count }}</h2>
            <h3>Miles Ridden</h3>
            <h4>{{ $miles_driven_average }} miles on average</h4>
        </div>
    </div>

    <div class="row user-stats">
        <div class="col-md-6">
            <h2>{{ $total_time_count }}</h2>
            <h3>Total Riding Time</h3>
            <h4>{{ $total_time_average }} on average</h4>
        </div>
        <div class="col-md-6">
            <h2>{{ $wait_time_count }}</h2>
            <h3>Total Wait Time</h3>
            <h4>{{ $wait_time_average }} on average</h4>
        </div>
    </div>

    @if( $products )
        <div class="row"><div class="col-md-8 col-md-offset-2" style="border-top:3px solid #1fbad6;margin-top:30px;margin-bottom:30px;"></div></div>
        <div class="user-products">
        @foreach( $products as $name => $product )
            <div class="row">
                <div class="col-xs-6 col-md-3 col-md-offset-3 product-image">
                    <img src="{{ $product['image'] }}" />
                </div>
                <div class="col-xs-6 col-md-6 product-details">
                    <h2>{{ $name }}</h2>
                    <h3>{{ $product['count'] }} {{ plural($product['count'], 'Ride', 'Rides') }} ({{ $product['percent'] }}%)</h3>
                </div>
            </div>
        @endforeach
        </div>
    @endif

    <div class="row"><div class="col-md-6 col-md-offset-3" style="border-top:3px solid #1fbad6;margin-top:15px;margin-bottom:15px;"></div></div>

    <div class="row share-stats">
        <div class="col-md-8 col-md-offset-2">

            @if( $owner )
                <a href="{{ action('AjaxController@pubpriv') }}" class="pubpriv" data-utid="{{ $utid }}" data-status="{{ $status }}" data-csrf="{{ csrf_token() }}">
                    @if( $status )
                        Hide from Leaderboard
                    @else
                        Show on Leaderboard
                    @endif
                </a>
                <?php
                reset( $products );
                $text = 'I\'ve taken ' . $trips_taken_count . ' @Uber ' . plural($trips_taken_count, 'Ride', 'Rides') . ' (' . $miles_driven_count . ' Miles), over ' . $total_time_count . '. My most ridden vehicle is ' . key($products) . '.'; ?>
                <a class="twitter" href="https://twitter.com/intent/tweet?text={{ urlencode($text . ' #ubertotals') }}&url={{ urlencode('https://uber.totals.io?utid=' . $utid) }}&related={{ urlencode('bryceadams, Follow for more cool stuff like Uber Totals!') }}">
                    <i class="fa fa-twitter"></i> Tweet Your Uber Stats!
                </a>
                <a class="facebook" onclick="window.open('https://www.facebook.com/dialog/feed?link={{ urlencode('https://uber.totals.io?utid=' . $utid) }}&name={{ urlencode('My Uber Totals') }}&app_id=1610053299209384&redirect_uri=http://uber.totals.io&description={{ urlencode($text) }}&picture=http://uber.totals.io/images/share.png&display=popup', 'fbshare', 'width=640,height=320');">
                    <i class="fa fa-facebook"></i> Share on Facebook!
                </a>
            @else
                <a class="uber" href="https://login.uber.com/oauth/authorize?client_id={{ $uber_client_id }}&response_type=code">
                    Want to see YOUR stats?
                    <strong>Sign in to Uber!</strong>
                </a>
                <a href="https://twitter.com/share" class="twitter-share-button" data-text="Totals for Uber: All your @Uber stats in one place! #ubertotals" data-via="bryceadams" data-size="large" data-related="bryceadams" data-count="none">Share</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
            @endif

        </div>
    </div>

@stop