<?php namespace App\Commands;

use App\Commands\Command;

use App\User;
use App\Uber;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Auth;

class UberDeauth extends Command implements SelfHandling {

	/**
	 * Create a new command instance.
	 * @param $request
	 */
	public function __construct( $request )
	{
		$this->request = $request;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{

		$user_id = Auth::user()->id;
		$ubers = User::find($user_id)->ubers;

		/**
		 * If they don't have any Uber accounts, let's go home.
		 */
		if ( ! $ubers->toArray() )
		{
			$this->go_home();
		}

		/**
		 * If the user ID is returned (deauth successful),
		 * delete the object from DB.
		 */
		$results = Uber::where('access_token', $this->request->input('delete_uber'))->get();
		$uber_object = $results[0];
		$uber_object->delete();
		$params = array( 'uber' => 'deleted' );

		/**
		 *  Redirect as needed.
		 */
		$queryString = http_build_query($params);
		header("Location: " . action('PagesController@home', $queryString));
		die();

	}

	/**
	 * Redirects people back home.
	 * @todo move to Command class for easier modifying for all Auths
	 */
	public function go_home()
	{
		header("Location: " . action('PagesController@home'));
		die();
	}

}
