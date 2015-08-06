<?php namespace App\Http\Controllers;

use App\Commands\UberAuth;
use App\Commands\UberDeauth;
use App\Http\Requests;
use App\Http\Requests\UberRequest;

class OAuthController extends Controller {

	/**
	 * Main container for Uber.
	 * @param UberRequest $request
	 */
	public function uber( UberRequest $request )
	{
		/**
		 * If desired action is undefined, redirect back to home.
		 */
		if ( ! isset( $_GET['action'] ) )
		{
			$this->go_home();
		}

		/**
		 * If action is to authenticate, go ahead.
		 * If action is to deauth, go do it!
		 */
		if ( $_GET['action'] == 'auth' ) {
			$this->dispatch(
				new UberAuth()
			);
		}
		elseif ( $_GET['action'] == 'deauth' )
		{
			$this->dispatch(
				new UberDeauth( $request )
			);
		}
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
