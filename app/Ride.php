<?php namespace App;

use App\Http\Requests\MailChimpReques\App\t;
use App\Uber;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

use Stevenmaguire\Uber\Client as UberClient;
use Stevenmaguire\Uber\Exception;

/**
 * Class Ride
 * @package App
 */
class Ride extends Model {

    protected $table = 'rides';
    public $timestamps = false;

    /**
     * @return mixed
     */
    public function uber()
    {
        return Uber::where('id', $this->utdb_id );
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'utdb_id', 'uuid', 'urid',
        'product_id', 'distance',
        'request_time', 'start_time', 'end_time',
        'ride_time', 'wait_time', 'distance_hour',
    ];

}