<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Validator;
use App\Customer;
use App\Entities\Network;
use Carbon\Carbon;
use App\Entities\Tercero;
use DB;

class GetCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para obtener todos los clientes de la API shopify';

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
        //$api_url = 'https://'. env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');
        $api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();
        $resa = $client->request('GET', $api_url . '/admin/customers/count.json');
        $countCustomers = json_decode($resa->getBody(), true);
        $this->info('Customers: ' . $countCustomers['count']);

        $result = true;
        $h = 1;

        do {
            $this->info('Entrando al do');

            $res = $client->request('GET', $api_url . '/admin/customers.json?limit=250&&page=' . $h);

            $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];
            if ($diferencia < 20) {

                usleep(10000000);
            }

            $results = json_decode($res->getBody(), true);

            foreach ($results['customers'] as $customer) {

                $this->info('Entrando al for');

                $response = Customer::where('network_id', 1)
                    ->where('customer_id', $customer['id'])
                    ->get();

                if(count($response) == 0) {

                    $this->info('Customer no existe');

                    Customer::createCustomer($customer);

                    $validator = Validator::make($customer, [
                        'email' => 'required|unique:terceros'
                    ]);

                    if ($validator->fails()) {

                        $this->info('Los clientes han sido descargados correctamente');

                    } else {

                        $result = Tercero::where('email', strtolower($customer['email']))->get();

                        if(count($result) == 0) {

                            $aux = explode('@', strtolower($customer['email']));
                            $tercero = new Tercero();
                            $tercero->nombres = (empty($customer['first_name']) || $customer['first_name'] == null || $customer['first_name'] == '') ? $customer['email'] : $customer['first_name'];
                            $tercero->apellidos = strtolower($customer['last_name']);
                            $tercero->email = strtolower($customer['email']);
                            $tercero->usuario = strtolower($customer['email']);
                            $tercero->contraseña = bcrypt($aux[0]);
                            $tercero->tipo_id = 2;
                            $tercero->customer_id = $customer['id'];
                            $tercero->network_id = 1;
                            $tercero->tipo_cliente_id = 1;
                            $tercero->save();

                        }
                    }
                }
            }

            $h++;

            if (count($results['customers']) < 1) {
                $result = false;
            }

            $this->info('Saliendo del do');

        } while($result);


        $terceros = Tercero::all();

        foreach ($terceros as $tercero) {

            $finder = Tercero::with('cliente')->where('email', strtolower($tercero->apellidos))->where('state', true)->first();

            $ok = Tercero::find($tercero->id);

            if (count($finder) > 0) {

                $res = DB::table('terceros_networks')
                    ->where('customer_id', $tercero->id)
                    ->where('padre_id', $finder->id)
                    ->get();

                if (count($res) == 0) {

                    if ($finder->id != $tercero->id && $finder->cliente->id == 1) {

                        $ok->networks()->attach(1, ['padre_id' => $finder->id]);
                        $father = Tercero::find($finder->id);
                        $father->numero_referidos = $father->numero_referidos +1;
                        $father->save();

                        $findcustomer = Customer::where('customer_id', $father->customer_id)->where('network_id', 1)->first();

                        if (count($findcustomer) > 0) {

                            /*$res = $client->request('get', $api_url . '/admin/customers/' . $father->customer_id . '/metafields.json');
                            $metafields = json_decode($res->getBody(), true);*/

                            /*if (isset($metafields['metafields']) && count($metafields['metafields']) == 0) {

                                $resd = $client->request('post', $api_url . '/admin/customers/' . $father->customer_id  . '/metafields.json', array(
                                    'form_params' => array(
                                        'metafield' => array(
                                            'namespace' => 'customers',
                                            'key' => 'referidos',
                                            'value' => ($father->numero_referidos == null) ? 0 : $father->numero_referidos,
                                            'value_type' => 'integer'
                                        )
                                    )
                                ));

                                $headers = $resd->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                $x = explode('/', $headers[0]);
                                $diferencia = $x[1] - $x[0];
                                if ($diferencia < 10) {
                                    usleep(10000000);
                                }
                            }

                            if (isset($metafields['metafields']) && count($metafields['metafields']) > 0) {

                                foreach ($metafields['metafields'] as $metafield) {

                                    if ($metafield['key'] === 'referidos') {
                                        $res = $client->request('put', $api_url . '/admin/customers/' . $father->customer_id  . '/metafields/' . $metafield['id'] . '.json', array(
                                                'form_params' => array(
                                                    'metafield' => array(
                                                        'namespace' => 'customers',
                                                        'key' => 'referidos',
                                                        'value' => ($father->numero_referidos == null || $father->numero_referidos == 0) ? 0 : $father->numero_referidos,
                                                        'value_type' => 'integer'
                                                    )
                                                )
                                            )
                                        );
                                        $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                        $x = explode('/', $headers[0]);
                                        $diferencia = $x[1] - $x[0];
                                        if ($diferencia < 10) {
                                            usleep(10000000);
                                        }

                                    }


                                }
                            }*/
                        }
                    }

                    if ($finder->id != $tercero->id && $finder->cliente->id != 1) {

                        $a = DB::table('terceros_networks')
                            ->where('customer_id', $tercero->id)
                            ->where('padre_id', 1)
                            ->get();
                        if (count($a) == 0) {
                            $ok->networks()->attach(1, ['padre_id' => 1]);
                            $father = Tercero::find(1);
                            $father->numero_referidos = $father->numero_referidos + 1;
                            $father->save();
                        }
                    }

                    if ($finder->id == $tercero->id) {

                        $b = DB::table('terceros_networks')
                            ->where('customer_id', $tercero->id)
                            ->where('padre_id', 1)
                            ->get();
                        if (count($b) == 0) {
                            $ok->networks()->attach(1, ['padre_id' => 1]);
                            $father = Tercero::find(1);
                            $father->numero_referidos = $father->numero_referidos + 1;
                            $father->save();
                        }

                    }
                }



            } else {

                $res = DB::table('terceros_networks')
                    ->where('customer_id', $tercero->id)
                    ->where('padre_id', 1)
                    ->get();

                if (count($res) == 0){

                    if ($tercero->id != 1 ) {
                        $ok->networks()->attach(1, ['padre_id' => 1]);
                        $father = Tercero::find(1);
                        $father->numero_referidos = $father->numero_referidos + 1;
                        $father->save();
                    }
                }
            }
        }

        $this->info('Los clientes han sido descargados correctamente');
    }
}
