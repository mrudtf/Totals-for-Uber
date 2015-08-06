<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Uber;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Stevenmaguire\Uber\Exception;

class PagesController extends Controller {

	public function __construct()
	{
		if ( ! Auth::guest() ) {
			$user_id            = Auth::user()->id;
			$this->user_id      = $user_id;
		}
	}
	public function home()
	{
        /**
         * Add in a check for the uber_profile session.
         */
        $session = Session::get('uber_profile');

        $utid = isset( $_GET['utid'] ) ? $_GET['utid'] : '';
        if ( !$utid && $session ) {
            $utid = $session['utid'];
        }
        $uber = Uber::where('utid', $utid)->get()->first();

        if ( $uber ) {
            try {
                $uber->uber()->getProfile();
            } catch (Exception $e) {
                $client = new Client();
                $response = $client->post('https://login.uber.com/oauth/token', [
                    'body' => [
                        'client_id' => env('UBER_CLIENT_ID'),
                        'client_secret' => env('UBER_CLIENT_SECRET'),
                        'redirect_uri' => env('UBER_REDIRECT_URI'),
                        'refresh_token' => $uber->refresh_token,
                        'grant_type' => 'refresh_token',
                    ],
                ])->json();
                if (isset($response['access_token'])) {
                    $uber->access_token = $response['access_token'];
                    $uber->refresh_token = $response['refresh_token'];
                    $uber->save();
                }
            }
        }

        if ( $uber && $uber->get_user_total_rides() > 0 )
		{
            $total_rides = $uber->get_user_total_rides();
            if ( $total_rides > 10000 ) {
                $trips_message = 'Uber God. You win.';
            } elseif ( $total_rides > 5000 ) {
                $trips_message = 'Almost impossible!';
            } elseif ( $total_rides > 2000 ) {
                $trips_message = 'Kudos to you. Bravo.';
            } elseif ( $total_rides > 1000 ) {
                $trips_message = 'Wow. Holy wow.';
            } elseif ( $total_rides > 500 ) {
                $trips_message = 'That\'s a fine effort!';
            } elseif ( $total_rides > 200 ) {
                $trips_message = 'Okay - Nice!';
            } elseif ( $total_rides > 100 ) {
                $trips_message = 'That\'s pretty good ' . $uber->get_first_name() . '!';
            } elseif ( $total_rides > 50 ) {
                $trips_message = 'Going to the top!';
            } elseif ( $total_rides > 10 ) {
                $trips_message = 'A beginner, but trying...';
            } else {
                $trips_message = 'Come on ' . $uber->get_first_name() . '...';
            }

            /**
             * Prepare data for this view, including all Uber stats.
             */
            $data = [
                'uber_client_id'        => env('UBER_CLIENT_ID'),
                'name'                  => $uber->get_first_name(),
                'full_name'             => $uber->get_full_name(),
                'photo'                 => $uber->get_user_logo(),
                'utid'                  => $utid,
                'status'                => $uber->public,
                'trips_taken_count'     => $total_rides,
                'trips_message'         => $trips_message,
                'miles_driven_count'    => number_format($uber->get_user_total_distance(), 2),
                'miles_driven_average'  => number_format($uber->get_user_total_distance_average(), 2),
                'total_time_count'      => display_seconds_pretty($uber->get_user_total_time()),
                'total_time_average'    => display_seconds_pretty($uber->get_user_total_time_average()),
                'wait_time_count'       => display_seconds_pretty($uber->get_user_wait_time()),
                'wait_time_average'     => display_seconds_pretty($uber->get_user_wait_time_average()),
                'products'              => $uber->get_product_usage(),
                //'ranking'   => '12th', //$ranking,
            ];

            /**
             * Check if the current session UTID matches the requested one.
             * If so, the current user is the owner of the data.
             */
            if ( isset($session) && $session['utid'] == $utid ) {
                $data['owner'] = true;
            } else {
                $data['owner'] = false;
            }

			return view( 'pages.dashboard', $data );
		} else {
            $data = [
                'uber_client_id'        => env('UBER_CLIENT_ID'),
                'uber_redirect_uri'     => env('UBER_REDIRECT_URI'),
            ];

            if ( isset( $_GET['utid'] ) ) {
                $data['message'] = 'This profile does not exist, is private or has no rides.';
            }

            return view( 'pages.auth', $data );
        }

	}

    /**
     * Returns view for Leaderboard page.
     *
     * @return \Illuminate\View\View
     */
    public function leaderboard()
    {
        $top_query = DB::table('ubers')
            ->select(DB::raw('utid, photo, name, rides_count, miles_driven, total_time, wait_time'))
            ->where('rides_count', '>', 0)
            ->where('public', 1);

        // Per Page Arg
        $per_page = isset( $_GET['per_page'] ) ? intval( $_GET['per_page'] ) : $per_page = 100;
        $top_query = $top_query->take($per_page);

        // Get Results Count
        $count = $top_query->count();

        // Page Arg
        if ( isset( $_GET['page'] ) ) {
            $page = intval( $_GET['page'] ) - 1;
            $skip = $page * 100;
            $top_query = $top_query->skip($skip);
        }

        // Order Arg
        if ( isset( $_GET['order'] ) ) {
            switch( $_GET['order'] ) {
                case 'miles':
                case 'shervin':
                    $top_query = $top_query->orderBy('miles_driven', 'desc');
                    break;
                case 'time':
                    $top_query = $top_query->orderBy('total_time', 'desc');
                    break;
                case 'wait':
                    $top_query = $top_query->orderBy('wait_time', 'desc');
                    break;
                default:
                    $top_query = $top_query->orderBy('rides_count', 'desc');
            }
        } else {
            $top_query = $top_query->orderBy('rides_count', 'desc');
        }

        $data = [
            'top100' => $top_query->get(),
            'count' => $count,
            'per_page' => $per_page,
        ];

        /**
         * Pass session in if it exists.
         */
        $session = Session::get('uber_profile');
        $data['session'] = isset( $session ) ? $session : false;

        /**
         * Pass in page if it exits.
         */
        if ( isset( $_GET['page'] ) ) {
            $data['page'] = $_GET['page'];
        }

        return view( 'pages.leaderboard', $data );
    }

    /**
     * Returns view for Privacy page.
     *
     * @return \Illuminate\View\View
     */
    public function privacy()
    {
        return view( 'pages.privacy' );
    }

}