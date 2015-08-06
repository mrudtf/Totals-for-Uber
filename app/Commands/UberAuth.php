<?php namespace App\Commands;

use App\Commands\Command;

use App\Uber;
use Drewm\MailChimp;
use GuzzleHttp\Client;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Session;
use Stevenmaguire\Uber\Client as UberClient;


class UberAuth extends Command implements SelfHandling {

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		/**
		 * If scope/code not set, go home.
		 */
		if ( ! isset( $_GET['code'] ) )
		{
			$this->go_home();
		}

		/**
		 * This method is used to get the access token from stripe,
		 * by passing in the Auth returned code.
		 */
		$client = new Client();
		$response = $client->post('https://login.uber.com/oauth/token', [
			'body' => [
				'client_id' => env('UBER_CLIENT_ID'),
				'client_secret' => env('UBER_CLIENT_SECRET'),
				'redirect_uri' => env('UBER_REDIRECT_URI'),
				'code' => $_GET['code'],
				'grant_type' => 'authorization_code',
			],
		])->json();

		$client = new UberClient(array(
			'access_token' => $response['access_token'],
			'server_token' => env('UBER_SERVER_TOKEN'),
			'use_sandbox'  => false, // optional, default false
			'version'      => 'v1', // optional, default 'v1'
			'locale'       => 'en_US', // optional, default 'en_US'
		));

		$uber_profile = $client->getProfile();

        $client_new = new UberClient(array(
            'access_token' => $response['access_token'],
            'server_token' => env('UBER_SERVER_TOKEN'),
            'use_sandbox'  => false, // optional, default false
            'version'      => 'v1.1', // optional, default 'v1'
            'locale'       => 'en_US', // optional, default 'en_US'
        ));

        $history = $client_new->getHistory(array(
            'limit' => 50, // optional
            'offset' => 0 // optional
        ));

        if ( $history->count == 0 ) {
            $queryString = http_build_query(array('failed' => '2' ) );
            header("Location: " . action('PagesController@home', $queryString));
        }


		/**
		 * We're not using users or accounts here, so we're going to
         * save the data inside a Session. We will use a common key
         * naming system that prevents multiple sessions being
         * created for the same user / data.
         * @todo encrypt sessions (http://laravel.com/docs/5.0/session)
         * @todo and SAVE profile data in cache, DB, if successful
		 */
        $utid = substr($uber_profile->uuid, -8 );
        $data = [
            'utid'          => $utid,
            'uuid'          => $uber_profile->uuid,
            'access_token'  => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
        ];

        /**
         * Handle DB adding, updating stuff.
         * @var DB $uber
         */
        $uber = Uber::firstOrCreate([
            'uuid'      => $uber_profile->uuid,
        ]);
        $uber->utid = $utid;
        $uber->access_token = $response['access_token'];
        $uber->refresh_token = $response['refresh_token'];
        $uber->save();

        /**
         * Save to session.
         */
        Session::put('uber_profile', $data );

        if ($response['access_token']) {
			$params = array( 'utid' => $utid );
		} else {
			$params = array( 'uber' => 'failed', 'error' => 1 );
		}

        /**
         * Add them to MailChimp List
         */
        $MailChimp = new MailChimp(env('MAILCHIMP_ID'));
        $MailChimp->call('lists/subscribe', array(
            'id'                => env('MAILCHIMP_LIST'),
            'email'             => array('email' => $uber_profile->email),
            'merge_vars'        => array('FNAME' => $uber_profile->first_name, 'LNAME' => $uber_profile->last_name),
            'double_optin'      => false,
            'update_existing'   => true,
            'replace_interests' => false,
            'send_welcome'      => false,
        ));

		/**
		 * Redirect as needed.
		 */
        $queryString = http_build_query($params);
        header("Location: " . action('PagesController@home', $queryString));

        // Cannot die(); as this breaks the session storage
	}

	/**
	 * Redirects people back home.
	 */
	public function go_home()
	{
		header("Location: " . action('PagesController@home'));
		die();
	}

}
