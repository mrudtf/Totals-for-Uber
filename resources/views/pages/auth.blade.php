@extends( 'template' )
@section( 'content' )
    <div class="row">
        <div class="col-md-12 view-leaderboard leaderboard">
            <a href="{{ action( 'PagesController@leaderboard' ) }}">Leaderboard</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-xs-12 want-to-see">
            @if( isset($message) )
                <div class="alert alert-danger">
                    <strong>Oops!</strong> {{ $message }}
                </div>
            @endif
            @if( isset($_GET['failed']) && $_GET['failed'] == 2 )
                <div class="alert alert-danger">
                    <strong>Sorry!</strong> You need at least 1 ride to use this.
                </div>
            @endif
            Want to see your <strong>total Uber rides</strong> or how many <strong>hours you've spent</strong> in an Uber?
            <span class="now-you-can">
                Now you can!
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 sign-in-uber">
            <a href="https://login.uber.com/oauth/authorize?client_id={{ $uber_client_id }}&response_type=code">
                Sign in to Uber
            </a>
        </div>
    </div>
@stop