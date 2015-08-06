<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Uber;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class AjaxController extends Controller {

    /**
     * @return mixed
     */
    public function pubpriv()
	{
		/**
		 * Verify CSRF token.
		 */
		if ( $_POST['_token'] !== Session::token() ) {
			return Response::json(array(
				'error' => true,
			));
		}

        /**
         * Session validation
         */
        $session = Session::get('uber_profile');
        if ( ! isset($session) || $session['utid'] !== $_POST['utid'] ) {
            return Response::json(array(
                'error' => true,
            ));
        }

		/**
		 * Find Uber row and change public/private status.
		 */
		$uber = Uber::where('utid', $_POST['utid'])->first();
        $status = ( $_POST['status'] == 1 ) ? false : true;
        $uber->public = $status;
        $uber->save();

		/**
		 * Respond with json success data.
		 */
		return Response::json(array(
			'success' => true,
            'dump' => $uber->public,
		));
	}
}
