<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Order;
use App\Entities\Tercero;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use MP;
use App\Commision;
use App\Logorder;
use App\Product;
use App\Entities\Network;

class CustomersController extends Controller
{

    public function verify_webhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, 'afc86df7e11dcbe0ab414fa158ac1767', true));
        return hash_equals($hmac_header, $calculated_hmac);
    }

    public function create()
    {
        ini_set('memory_limit', '300M');

        $input = file_get_contents('php://input');
        $customer = json_decode($input, true);
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($customer), $hmac_header);
        $resultapi = error_log('Webhook verified: ' . var_export($verified, true));

        if ($resultapi == 'true') {

            $result = Customer::where('customer_id', $customer['id'])
                ->where('email', strtolower($customer['email']))
                ->where('network_id', 1)
                ->get();

            if (count($result) == 0) {

                Customer::createCustomer($customer);

                $validator = Validator::make($customer, [
                    'email' => 'required|unique:terceros'
                ]);

                if ($validator->fails()) {

                    $this->info('Los clientes han sido descargados correctamente');

                } else {

                    $result = Tercero::where('email', $customer['email'])->get();

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

                        $finder = Tercero::with('cliente')->where('email', strtolower($customer['last_name']))->where('state', true)->first();

                        if (count($finder) > 0) {

                            if ($finder->id != $tercero->id && $finder->cliente->id == 1) {
                                $tercero->networks()->attach(1, ['padre_id' => $finder->id]);
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
                                $tercero->networks()->attach(1, ['padre_id' => 1]);
                                $father = Tercero::find(1);
                                $father->numero_referidos = $father->numero_referidos + 1;
                                $father->save();
                            }

                            if ($finder->id == $tercero->id) {

                                $tercero->networks()->attach(1, ['padre_id' => 1]);
                                $father = Tercero::find(1);
                                $father->numero_referidos = $father->numero_referidos + 1;
                                $father->save();
                            }

                        } else {

                            if ($tercero->id != 1) {
                                $tercero->networks()->attach(1, ['padre_id' => 1]);
                                $father = Tercero::find(1);
                                $father->numero_referidos = $father->numero_referidos + 1;
                                $father->save();
                            }
                        }
                    }
                }

            } else {

                return response()->json(['status' => 'The resource exist'], 200);
            }
        }
    }
    
}