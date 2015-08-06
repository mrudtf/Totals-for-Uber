<?php namespace App;

use App\Http\Requests\MailChimpRequest;
use App\Ride;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

use Stevenmaguire\Uber\Client as UberClient;
use Stevenmaguire\Uber\Exception;

/**
 * Class Uber
 * @package App
 */
class Uber extends Model {

    protected $table = 'ubers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'utid', 'uuid', 'auth_token', 'refresh_token', 'profile_data', 'user_activity',
    ];

    /**
     * @return mixed
     */
    public function rides()
    {
        return Ride::where('utdb_id', $this->id);
    }

    /**
     * @return UberClient
     */
    public function uber()
    {
        return new UberClient(array(
            'access_token' => $this->access_token,
            'server_token' => env('UBER_SERVER_TOKEN'),
            'use_sandbox'  => false, // optional, default false
            'version'      => 'v1', // optional, default 'v1'
            'locale'       => 'en_US', // optional, default 'en_US'
        ));
    }

    /**
     * @return UberClient
     */
    public function uber_new()
    {
        return new UberClient(array(
            'access_token' => $this->access_token,
            'server_token' => env('UBER_SERVER_TOKEN'),
            'use_sandbox'  => false, // optional, default false
            'version'      => 'v1.1', // optional, default 'v1'
            'locale'       => 'en_US', // optional, default 'en_US'
        ));
    }

    /**
     * Retrieve profile data about Uber user.
     *
     * @return array
     */
    public function get_profile_data()
    {
        $key = 'uber_' . $this->uuid . '_profile';
        if ( Cache::tags('uber')->has($key)) {
            $profile = Cache::tags('uber')->get($key);
        } else {
            $profile = $this->uber()->getProfile();
            Cache::tags('uber')->put($key, $profile, 1440);
        }
        return $profile;
    }

    /**
     * @return mixed
     */
    public function get_user_rides()
    {
        return $this->rides()->get();
    }

    /**
     * @return \Stevenmaguire\Uber\stdClass
     */
    public function get_user_activity()
    {
        $key = 'uber_' . $this->uuid . '_user_activity';
        if ( Cache::tags('uber')->has($key)) {
            $activity = Cache::tags('uber')->get($key);
        } else {
            $activity = $this->uber_new()->getHistory(array(
                'limit' => 50, // optional
                'offset' => 0 // optional
            ));
            /**
             * Go through remaining rides if we are exceeding the 50 limit
             * We need to check offset and count and do some calc's to get all of the history.
             */
            $ratio = $activity->count / 50;
            $times = floor( $ratio );
            for ($x = 1; $x <= $times; $x++) {
                $new_activity = $this->uber_new()->gethistory(array(
                    'limit' => ($times == $x ) ? $activity->count - ( 50 * $x ) : 50,
                    'offset' => ( 50 * $x )
                ));
                $activity->history = array_merge( $activity->history, $new_activity->history );
                Cache::tags('uber')->put($key, $activity, 300);
            }
            $this->store_rides( $activity->history );
            Cache::tags('uber')->put($key, $activity, 300);
        }
        return $activity;
    }

    /**
     * Goes through and stores all the rides.
     * @param mixed $rides
     * @return bool
     */
    public function store_rides( $rides = false )
    {
        if ( ! $rides ) {
            $rides = $this->get_user_activity()->history;
        }
        foreach( $rides as $ride )
        {
            /**
             * Look for a ride with this URID.
             * If none found, create it.
             */
            try {
                Ride::where('urid', $ride->uuid)->firstOrFail();
            } catch(ModelNotFoundException $e) {
                Ride::firstOrCreate([
                    'utdb_id' => $this->id,
                    'uuid' => $this->uuid, // Uber ID
                    'urid' => $ride->uuid, // Ride ID
                    'product_id' => isset( $ride->product_id ) ? $ride->product_id : '0', // Product IDs blank sometimes
                    'distance' => $ride->distance,
                    'request_time' => $ride->request_time,
                    'start_time' => $ride->start_time,
                    'end_time' => $ride->end_time,
                    'ride_time' => $ride->end_time - $ride->start_time,
                    'wait_time' => $ride->start_time - $ride->request_time,
                    'distance_hour' => $ride->distance / ( ($ride->end_time - $ride->start_time) / 3600 )
                ]);
            }
        }
        return true;
    }

    public function get_first_name()
    {
        $name = $this->get_profile_data()->first_name;

        // Save to DB.
        $this->name = $name;
        $this->save();

        return $name;
    }

    /**
     * @return string
     */
    public function get_full_name()
    {
        $name = $this->get_profile_data()->first_name . ' ' . $this->get_profile_data()->last_name;

        return $name;
    }

    /**
     * @return mixed
     */
    public function get_user_logo()
    {
        $uber_picture = $this->get_profile_data()->picture;

        if ( strpos($uber_picture,'default') !== false ) {
            $email = $this->get_profile_data()->email;
            $picture = 'https://www.gravatar.com/avatar/' . md5( $email ) . '?d=404';
            $headers = get_headers( $picture, 1 );
            if ( $headers[0] == 'HTTP/1.0 404 Not Found' ) {
                $picture = $uber_picture;
            }
        } else {
            $picture = $uber_picture;
        }

        // Save to DB.
        $this->photo = $picture;
        $this->save();

        return $picture;
    }

    /**
     * @return mixed
     */
    public function get_user_total_rides()
    {
        $total_rides = $this->get_user_activity()->count;

        // Save to DB.
        $this->rides_count = $total_rides;
        $this->save();

        return $total_rides;
    }

    /**
     * @return int
     */
    public function get_user_total_distance()
    {
        $trips = $this->get_user_activity()->history;
        $distance = 0;
        foreach( $trips as $trip ) {
            $distance += $trip->distance;
        }

        // Save to DB.
        $this->miles_driven = $distance;
        $this->save();

        return $distance;
    }

    /**
     * @return float
     */
    public function get_user_total_distance_average()
    {
        return $this->get_user_total_distance() / $this->get_user_total_rides();
    }

    /**
     * @return int
     */
    public function get_user_total_time()
    {
        $trips = $this->get_user_activity()->history;
        $time = 0;
        foreach( $trips as $trip ) {
            $trip_time = $trip->end_time - $trip->start_time;
            $time += $trip_time;
        }

        // Save to DB.
        $this->total_time = $time;
        $this->save();

        return $time;
    }

    /**
     * @return float
     */
    public function get_user_total_time_average()
    {
        return $this->get_user_total_time() / $this->get_user_total_rides();
    }

    /**
     * @return int
     */
    public function get_user_wait_time()
    {
        $trips = $this->get_user_activity()->history;
        $time = 0;
        foreach( $trips as $trip ) {
            $wait_time = $trip->start_time - $trip->request_time;
            $time += $wait_time;
        }

        // Save to DB.
        $this->wait_time = $time;
        $this->save();

        return $time;
    }

    /**
     * @return float
     */
    public function get_user_wait_time_average()
    {
        return $this->get_user_wait_time() / $this->get_user_total_rides();
    }

    /**
     * Gets an instance of the requested product ID.
     *
     * @param $id
     * @return \Stevenmaguire\Uber\stdClass
     */
    public function get_product($id)
    {
        $key = 'uber_product_' . $id;
        if ( Cache::tags('uber')->has($key)) {
            $product = Cache::tags('uber')->get($key);
        } else {
            try {
                $product = $this->uber()->getProduct($id);
            } catch( Exception $e ) {
                $product = false;
            }
            Cache::tags('uber')->put($key, $product, 10000);
        }
        return $product;
    }

    /**
     * Gets product usage, including the used 'product' IDs,
     * their names/images and their usage counts.
     */
    public function get_product_usage()
    {
        $trips = $this->get_user_activity()->history;

        /**
         * Create an array of the used products.
         */
        $products = array();
        foreach( $trips as $trip ) {
            $product = $this->get_product($trip->product_id);
            if ( $product ) {
                // SSL check and replace
                $image = ( strpos( $product->image, 'http://' ) !== false ) ? '//' . substr( $product->image, 7 ) : $product->image;
                $products[$product->display_name] = array(
                    'id' => $trip->product_id,
                    'image' => ($product->display_name == 'uberPOOL') ? '/images/uberpool.png' : $image,
                    'count' => 0,
                );
            }
        }

        /**
         * Count the usage of each product type.
         */
        foreach( $trips as $trip ) {
            $product = $this->get_product($trip->product_id);
            if ( $product ) {
                $products[$product->display_name]['count'] += 1;
            }
        }

        /**
         * Add the use percent to each product.
         */
        foreach( $products as $name => $product ) {
            $products[$name]['percent'] = number_format( ( $product['count'] / $this->get_user_total_rides() ) * 100, 2 );
        }

        /**
         * Sort products array by usage count.
         */
        uasort($products, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        return $products;
    }

}