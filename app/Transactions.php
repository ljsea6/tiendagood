<?php

namespace App;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Order;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected static $url;
    protected static $client;
    protected $table = 'transactions';

    protected $casts = [
        'receipt' => 'array',
       // 'payment_details' => 'array',
    ];

    protected $guarded = [];

    public function tercero()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public static function createTransaction($transaction, $shop)
    {
        return self::create([
            'transaction_id' => $transaction['id'],
            'order_id' => $transaction['order_id'],
            'amount' => $transaction['amount'],
            'kind' => $transaction['kind'],
            'gateway' => $transaction['gateway'],
            'status' => $transaction['status'],
            'message' => $transaction['message'],
            'created_at' => Carbon::parse($transaction['created_at']),
            'test' => $transaction['test'],
            'authorization' => $transaction['authorization'],
            'currency' => $transaction['currency'],
            'location_id' => $transaction['location_id'],
            'user_id' => $transaction['user_id'],
            'parent_id' => $transaction['parent_id'],
            'device_id' => $transaction['device_id'],
            'receipt' => $transaction['receipt'],
            'error_code' => $transaction['error_code'],
            'source_name' => $transaction['source_name'],
           
            //'payment_details' => $transaction['payment_details'],
            'shop' => $shop,
        ]);
    }


}
