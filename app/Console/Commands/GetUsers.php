<?php

namespace App\Console\Commands;

use App\Entities\Tercero;
use DB;
use Auth;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;

class GetUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para migrar todos los usuarios de good a mercando';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $api_url_good = 'https://'. env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');
        $api_url_mercando = 'https://'. env('API_KEY_MERCANDO') . ':' . env('API_PASSWORD_MERCANDO') . '@' . env('API_SHOP_MERCANDO');
        $client = new \GuzzleHttp\Client();

        $terceros = Tercero::all();

        foreach ($terceros as $tercero) {

            $res_good = $client->request('GET',  $api_url_good . '/admin/customers/search.json?query=email:' . $tercero->email);
            $headers = $res_good->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];

            if ($diferencia < 20) {

                usleep(20000000);
            }

            $results_good = json_decode($res_good->getBody(), true);

            if (count($results_good['customers']) > 0) {

                $this->info('El usuario existe en good');

                $res_mercando = $client->request('GET',  $api_url_mercando . '/admin/customers/search.json?query=email:' . $tercero->email);

                $headers =  $res_mercando->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                $x = explode('/', $headers[0]);
                $diferencia = $x[1] - $x[0];

                if ($diferencia < 20) {

                    usleep(20000000);
                }

                $results_mercando = json_decode($res_mercando->getBody(), true);

                if (count($results_mercando['customers']) > 0) {

                    $this->info('El usuario existe en mercando');

                    $a = DB::table('terceros_tiendas')
                        ->where('tercero_id', $tercero->id)
                        ->where('customer_id_good', $results_good['customers'][0]['id'])
                        ->where('customer_id_mercando', $results_mercando['customers'][0]['id'])
                        ->first();

                    if (count($a) == 0) {

                        DB::table('terceros_tiendas')->insertGetId(
                            [
                                'tercero_id' => $tercero->id,
                                'customer_id_good' =>  $results_good['customers'][0]['id'],
                                'customer_id_mercando' => $results_mercando['customers'][0]['id'],
                            ]
                        );
                    }

                } else {

                    $this->info('El usuario no existe en mercando, se creará.');

                    try {

                        $res = $client->request('post', $api_url_mercando . '/admin/customers.json', array(
                                'form_params' => array(
                                    'customer' => array(
                                        'first_name' => strtolower( $results_good['customers'][0]['first_name']),
                                        'last_name' => strtolower( $results_good['customers'][0]['last_name']),
                                        'email' => strtolower($results_good['customers'][0]['email']),
                                        'verified_email' => true,
                                        'phone' =>  $results_good['customers'][0]['phone'],
                                        'addresses' => [
                                            [
                                                'address1' => strtolower($results_good['customers'][0]['addresses'][0]['address1']),
                                                'city' => strtolower($results_good['customers'][0]['addresses'][0]['city']),
                                                'province' => '',

                                                "zip" => '',
                                                'first_name' => strtolower($results_good['customers'][0]['addresses'][0]['first_name']),
                                                'last_name' => strtolower($results_good['customers'][0]['addresses'][0]['first_name']),
                                                'country' => 'CO'
                                            ],
                                        ],
                                        "password" => $tercero->identificacion,
                                        "password_confirmation" => $tercero->identificacion,
                                        'send_email_invite' => false,
                                        'send_email_welcome' => false
                                    )
                                )
                            )
                        );

                        $headers =  $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                        $x = explode('/', $headers[0]);
                        $diferencia = $x[1] - $x[0];

                        if ($diferencia < 20) {

                            usleep(20000000);
                        }

                        $customer = json_decode($res->getBody(), true);

                        $b = DB::table('terceros_tiendas')
                            ->where('tercero_id', $tercero->id)
                            ->where('customer_id_good', $results_good['customers'][0]['id'])
                            ->where('customer_id_mercando', $customer['customer']['id'])
                            ->first();

                        if (count($b) == 0) {
                            DB::table('terceros_tiendas')->insertGetId(
                                [
                                    'tercero_id' => $tercero->id,
                                    'customer_id_good' =>  $results_good['customers'][0]['id'],
                                    'customer_id_mercando' =>  $customer['customer']['id'],
                                ]
                            );
                        }

                    } catch (ClientException $e) {

                        return $err = json_decode(($e->getResponse()->getBody()), true);
                    }
                }
            }
        }
        $this->info('Los usuarios han sido migrados por completo.');
    }
}
