<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Order;
use Carbon\Carbon;
use App\Entities\Network;
use DB;
use App\Customer;
use App\Entities\Tercero;
use App\Product;
use App\Logorder;
use App\LineItems;
use App\Variant;

class GetOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para obtener todos las ordenes de la API shopify';

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
        $api_url = 'https://'. env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');
        //$api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $client = new \GuzzleHttp\Client();

        $result = true;
        $h = 1;

        do {

            $res = $client->request('GET', $api_url . '/admin/orders.json?limit=250&&status=any&&page=' . $h);

            $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];
            if ($diferencia < 20) {

                usleep(20000000);
            }

            $results = json_decode($res->getBody(), true);

            foreach ($results['orders'] as $order) {

                $response = Order::where('network_id', 1)
                    ->where('name', $order['name'])
                    ->where('order_id', $order['id'])
                    ->first();

                if ($order['cancelled_at'] != null && $order['cancel_reason'] != null) {

                    if(count($response) == 0) {

                        $tipo_orden = '';
                        $i = 0;
                        $n = 0;

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $variant = Variant::find($item['variant_id']);

                                $search = Order::where('name', $order['name'])->first();

                                if(count($search) > 0) {

                                    $update = Order::find($search->id);
                                    $update->points = $update->points + $variant->percentage;
                                    $update->save();
                                }



                                $line_item = LineItems::find($item['id']);

                                if (count($line_item) == 0) {

                                    LineItems::createLineItem($item, $order, (isset($variant->percentage)) ? $variant->percentage : 0);
                                }

                                $product = Product::find($item['product_id']);

                                if (strtolower($item['vendor'])  == 'nacional' || strtolower($item['vendor'])  == 'a - nacional') {
                                    $n++;

                                    if (count($product) > 0) {
                                        $product->tipo_producto = 'nacional';
                                        $product->save();
                                    }
                                }
                                if (strtolower($item['vendor'])  != 'nacional' && strtolower($item['vendor'])  != 'a - nacional') {
                                    $i++;

                                    if (count($product) > 0) {
                                        $product->tipo_producto = 'internacional';
                                        $product->save();
                                    }
                                }
                            }
                        }

                        if ($i > 0 && $n > 0) {
                            $tipo_orden .= 'nacional/internacional';
                            $i = 0;
                            $n = 0;
                        }
                        if ($i > 0 && $n == 0) {
                            $tipo_orden .= 'internacional';
                            $i = 0;
                            $n = 0;
                        }
                        if ($i == 0 && $n > 0) {
                            $tipo_orden .= 'nacional';
                            $i = 0;
                            $n = 0;
                        }

                        $order = Order::createOrder($order, $tipo_orden);

                        $tipo_orden = '';

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $variant = Variant::find($item['variant_id']);
                                $line_item = LineItems::find($item['id']);

                                if (count($variant) > 0) {

                                    $update = Order::find($order->id);
                                    $update->points = $update->points + $variant->percentage;
                                    $update->save();


                                    if (count($line_item) == 0) {

                                        LineItems::createLineItem($item, $order, (isset($variant->percentage)) ? $variant->percentage : 0);
                                    }
                                } else {

                                    $update = Order::find($order->id);
                                    $update->points = $update->points + 0;
                                    $update->save();

                                    if (count($line_item) == 0) {

                                        LineItems::createLineItem($item, $order, 0);
                                    }

                                }
                            }
                        }
                    }
                }

                if ($order['cancelled_at'] == null && $order['cancel_reason'] == null) {


                    if(count($response) == 0) {


                        $tipo_orden = '';
                        $i = 0;
                        $n = 0;


                        if ($i > 0 && $n > 0) {
                            $tipo_orden .= 'nacional/internacional';
                            $i = 0;
                            $n = 0;
                        }
                        if ($i > 0 && $n == 0) {
                            $tipo_orden .= 'internacional';
                            $i = 0;
                            $n = 0;
                        }
                        if ($i == 0 && $n > 0) {
                            $tipo_orden .= 'nacional';
                            $i = 0;
                            $n = 0;
                        }

                        $order = Order::createOrder($order, $tipo_orden);

                        $tipo_orden = '';

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $variant = Variant::find($item['variant_id']);
                                $line_item = LineItems::find($item['id']);

                                if (count($variant) > 0) {

                                    $update = Order::find($order->id);
                                    $update->points = $update->points + $variant->percentage;
                                    $update->save();


                                    if (count($line_item) == 0) {

                                        LineItems::createLineItem($item, $order, (isset($variant->percentage)) ? $variant->percentage : 0);
                                    }
                                } else {

                                    $update = Order::find($order->id);
                                    $update->points = $update->points + 0;
                                    $update->save();

                                    if (count($line_item) == 0) {

                                        LineItems::createLineItem($item, $order, 0);
                                    }

                                }


                                $product = Product::find($item['product_id']);

                                if (strtolower($item['vendor'])  == 'nacional' || strtolower($item['vendor'])  == 'a - nacional') {
                                    $n++;

                                    if (count($product) > 0) {
                                        $product->tipo_producto = 'nacional';
                                        $product->save();
                                    }
                                }
                                if (strtolower($item['vendor'])  != 'nacional' && strtolower($item['vendor'])  != 'a - nacional') {
                                    $i++;

                                    if (count($product) > 0) {
                                        $product->tipo_producto = 'internacional';
                                        $product->save();
                                    }
                                }
                            }
                        }

                        if ($order['financial_status'] == "paid") {

                            if (isset($order['line_items']) && count($order['line_items']) > 0) {

                                foreach ($order['line_items'] as $item) {

                                    $variant = Variant::find($item['variant_id']);

                                    if (count($variant) > 0) {
                                        $variant->sold_units = $variant->sold_units + $item['quantity'];
                                        $variant->save();
                                    }
                                }
                            }

                            $tercero = Tercero::with('networks')->where('email', $order['email'])->first();

                            if (count($tercero) > 0) {

                                if (isset($tercero->networks) && isset($tercero->networks[0]) && isset($tercero->networks[0]['pivot']) && count($tercero->networks[0]['pivot']['padre_id']) > 0 && $tercero->state == true) {

                                    $padre = Tercero::where('id', $tercero->networks[0]['pivot']['padre_id'])->first();

                                    if (count($padre) > 0) {

                                        if ($padre->state) {

                                            $find = Tercero::find($padre->id);
                                            $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                            $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                            $find->ganacias = $find->total_price_orders * 0.05;
                                            $find->save();

                                            $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                            if (count($customer) > 0) {
                                                $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                                $metafields = json_decode($res->getBody(), true);


                                                if (count($metafields['metafields']) > 0) {

                                                    foreach ($metafields['metafields'] as $metafield) {
                                                        if ($metafield['key'] === 'referidos') {
                                                            $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                    'form_params' => array(
                                                                        'metafield' => array(
                                                                            'namespace' => 'customers',
                                                                            'key' => 'referidos',
                                                                            'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                            'value_type' => 'integer'
                                                                        )
                                                                    )
                                                                )
                                                            );

                                                        }

                                                        if ($metafield['key'] === 'compras') {
                                                            $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                    'form_params' => array(
                                                                        'metafield' => array(
                                                                            'namespace' => 'customers',
                                                                            'key' => 'compras',
                                                                            'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                            'value_type' => 'integer'
                                                                        )
                                                                    )
                                                                )
                                                            );

                                                        }

                                                        if ($metafield['key'] === 'valor') {
                                                            $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                    'form_params' => array(
                                                                        'metafield' => array(
                                                                            'namespace' => 'customers',
                                                                            'key' => 'valor',
                                                                            'value' => '' . ($find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                            'value_type' => 'string'
                                                                        )
                                                                    )
                                                                )
                                                            );
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            } else {

                                $padre = Tercero::where('email', strtolower($order['billing_address']['last_name']))->first();

                                if (count($padre) > 0) {

                                    if ($padre->state) {

                                        $find = Tercero::find($padre->id);
                                        $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                        $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                        $find->ganacias = $find->total_price_orders * 0.05;
                                        $find->save();

                                        $customer = Customer::where('customer_id', $padre->customer_id)->where('network_id', 1)->first();

                                        if (count($customer) > 0) {
                                            $res = $client->request('get', $api_url . '/admin/customers/' . $find->customer_id . '/metafields.json');
                                            $metafields = json_decode($res->getBody(), true);


                                            if (count($metafields['metafields']) > 0) {

                                                foreach ($metafields['metafields'] as $metafield) {
                                                    if ($metafield['key'] === 'referidos') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'referidos',
                                                                        'value' => ($find->numero_referidos == null || $find->numero_referidos == 0) ? 0 : $find->numero_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                    }

                                                    if ($metafield['key'] === 'compras') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'compras',
                                                                        'value' => ($find->numero_ordenes_referidos == null || $find->numero_ordenes_referidos == 0) ? 0 : $find->numero_ordenes_referidos,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );

                                                    }

                                                    if ($metafield['key'] === 'valor') {
                                                        $res = $client->request('put', $api_url . '/admin/customers/' . $find->customer_id . '/metafields/' . $metafield['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'customers',
                                                                        'key' => 'valor',
                                                                        'value' => '' . ($find->ganacias == null || $find->ganacias == 0) ? 0 : number_format($find->ganacias) . '',
                                                                        'value_type' => 'string'
                                                                    )
                                                                )
                                                            )
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }

                                } else {

                                    $find = Tercero::find(1);
                                    $find->numero_ordenes_referidos = $find->numero_ordenes_referidos + 1;
                                    $find->total_price_orders = $find->total_price_orders + $order['total_price'];
                                    $find->ganacias = $find->total_price_orders * 0.05;
                                    $find->save();
                                }
                            }
                        }
                    }
                }


            }

            $h++;

            if (count($results['orders']) < 1) {
                $result = false;
            }

        } while($result);

        $this->info('Las ordenes han sido descargados correctamente');
    }
}
