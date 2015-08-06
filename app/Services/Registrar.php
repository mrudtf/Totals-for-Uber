<?php namespace App\Services;

use App\User;
use GuzzleHttp\Client;
use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;

class Registrar implements RegistrarContract {

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		return Validator::make($data, [
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	public function create(array $data)
	{
		//$response = $this->getAccessToken( $data['stripe_code'] )->json();

		return User::create([
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
			/*'access_token' => $response['access_token'],
			'refresh_token' => $response['refresh_token'],
			'stripe_publishable_key' => $response['stripe_publishable_key'],
			'stripe_user_id' => $response['stripe_user_id']*/
		]);
	}
	
}
