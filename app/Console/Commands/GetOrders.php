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
                    ->where('shop', 'good')
                    ->first();

                if ($order['cancelled_at'] != null || $order['cancel_reason'] != null) {

                    if(count($response) == 0) {

                        $tipo_orden = '';
                        $i = 0;
                        $n = 0;
                        $puntos = 0;

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $v = Variant::where('id', $item['variant_id'])
                                    ->where('shop', 'good')
                                    ->where('product_id', $item['product_id'])
                                    ->first();

                                if (count($v) > 0) {

                                    $puntos = $puntos + $v->points;

                                    $line_item = LineItems::where('line_item_id', $item['id'])
                                        ->where('shop', 'good')
                                        ->where('variant_id', $item['variant_id'])
                                        ->first();

                                    if (count($line_item) == 0) {

                                        LineItems::createLineItem($item, $order, $v->points, 'good');
                                    }

                                    $product = Product::find($item['product_id']);

                                    if ($product->tipo_producto == 'nacional') {
                                        $n++;
                                    }
                                    if ($product->tipo_producto == 'internacional') {
                                        $i++;
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

                        $order = Order::createOrder($order, 'good', $puntos, $tipo_orden);

                        $tipo_orden = '';

                    }
                }

                if ($order['cancelled_at'] == null && $order['cancel_reason'] == null) {

                    if(count($response) == 0) {

                        $tipo_orden = '';
                        $i = 0;
                        $n = 0;
                        $puntos = 0;

                        if (isset($order['line_items']) && count($order['line_items']) > 0) {

                            foreach ($order['line_items'] as $item) {

                                $v = Variant::where('id', $item['variant_id'])
                                    ->where('shop', 'good')
                                    ->where('product_id', $item['product_id'])
                                    ->first();

                                $this->info('Puntos: ' . $v->percentage . ' la variante: ' .  $v->title);


                                if (count($v) > 0) {

                                    $puntos = $puntos + $v->percentage * $item['quantity'];

                                    $line_item = LineItems::where('line_item_id', $item['id'])
                                        ->where('shop', 'good')
                                        ->where('variant_id', $item['variant_id'])
                                        ->first();

                                    if (count($line_item) == 0) {

                                        LineItems::createLineItem($item, $order, $v->percentage, 'good');
                                    }

                                    $product = Product::find($item['product_id']);

                                    if ($product->tipo_producto == 'nacional') {
                                        $n++;
                                    }
                                    if ($product->tipo_producto == 'internacional') {
                                        $i++;
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

                        $order_create = Order::createOrder($order, 'good', $puntos, $tipo_orden);

                        $tipo_orden = '';

                        if ($order['financial_status'] == "paid") {

                            if (isset($order['line_items']) && count($order['line_items']) > 0) {

                                foreach ($order['line_items'] as $item) {

                                    $variant = Variant::where('id', $item['variant_id'])
                                        ->where('product_id', $item['product_id'])
                                        ->where('shop', 'good')
                                        ->first();

                                    if (count($variant) > 0) {

                                        $this->info('Sumando: ' . $item['quantity']);

                                        DB::table('variants')
                                            ->where('id', $item['variant_id'])
                                            ->where('product_id', $item['product_id'])
                                            ->where('shop', 'good')
                                            ->update(['sold_units' => $variant->sold_units + $item['quantity']]);
                                    }
                                }
                            }
                        }

                        $tercero = Tercero::where('email', strtolower($order['email']))
                            ->where('state', true)
                            ->first();

                        if (count($tercero) > 0) {

                            $update = Tercero::with('networks', 'levels')->find($tercero->id);
                            $update->mispuntos = $update->mispuntos + $order_create->points;
                            $update->save();

                            if (count($update->networks) > 0) {

                                 $padre_uno = Tercero::with('networks', 'levels')->find($update->networks[0]['pivot']['padre_id']);

                                 if (count($padre_uno) > 0 && $padre_uno->state == true) {

                                     if (count($padre_uno->levels) == 0) {

                                         DB::table('terceros_niveles')->insertGetId(
                                             [
                                                 'tercero_id' => $padre_uno->id,
                                                 'nivel' =>  1,
                                                 'puntos' =>  $puntos,
                                             ]
                                         );

                                     } else {

                                         $result = DB::table('terceros_niveles')
                                             ->where('tercero_id', $padre_uno->id)
                                             ->where('nivel', 1)
                                             ->first();

                                         if (count($result) > 0) {

                                             DB::table('terceros_niveles')
                                                 ->where('tercero_id', $padre_uno->id)
                                                 ->where('nivel', 1)
                                                 ->update(['puntos' => $result->puntos + $puntos]);

                                         } else {

                                             DB::table('terceros_niveles')->insertGetId(
                                                 [
                                                     'tercero_id' => $padre_uno->id,
                                                     'nivel' =>  1,
                                                     'puntos' =>  $puntos,
                                                 ]
                                             );
                                         }
                                     }

                                     if (count($padre_uno->networks) > 0) {

                                         $padre_dos = Tercero::with('networks', 'levels')->find($padre_uno->networks[0]['pivot']['padre_id']);

                                         if (count($padre_dos) > 0 && $padre_dos->state == true) {

                                             if (count($padre_dos->levels) == 0) {

                                                 DB::table('terceros_niveles')->insertGetId(
                                                     [
                                                         'tercero_id' => $padre_dos->id,
                                                         'nivel' =>  2,
                                                         'puntos' =>  $puntos,
                                                     ]
                                                 );

                                             } else {

                                                 $result = DB::table('terceros_niveles')
                                                     ->where('tercero_id', $padre_dos->id)
                                                     ->where('nivel', 2)
                                                     ->first();

                                                 if (count($result) > 0) {

                                                     DB::table('terceros_niveles')
                                                         ->where('tercero_id', $padre_dos->id)
                                                         ->where('nivel', 2)
                                                         ->update(['puntos' => $result->puntos + $puntos]);

                                                 } else {

                                                     DB::table('terceros_niveles')->insertGetId(
                                                         [
                                                             'tercero_id' => $padre_dos->id,
                                                             'nivel' =>  2,
                                                             'puntos' =>  $puntos,
                                                         ]
                                                     );
                                                 }
                                             }

                                             if (count($padre_dos->networks) > 0) {

                                                 $padre_tres = Tercero::with('networks', 'levels')->find($padre_dos->networks[0]['pivot']['padre_id']);

                                                 if (count($padre_tres) > 0 && $padre_tres->state == true) {

                                                     if (count($padre_tres->levels) == 0) {

                                                         DB::table('terceros_niveles')->insertGetId(
                                                             [
                                                                 'tercero_id' => $padre_tres->id,
                                                                 'nivel' =>  3,
                                                                 'puntos' =>  $puntos,
                                                             ]
                                                         );

                                                     } else {

                                                         $result = DB::table('terceros_niveles')
                                                             ->where('tercero_id', $padre_tres->id)
                                                             ->where('nivel', 3)
                                                             ->first();

                                                         if (count($result) > 0) {

                                                             DB::table('terceros_niveles')
                                                                 ->where('tercero_id', $padre_tres->id)
                                                                 ->where('nivel', 3)
                                                                 ->update(['puntos' => $result->puntos + $puntos]);

                                                         } else {

                                                             DB::table('terceros_niveles')->insertGetId(
                                                                 [
                                                                     'tercero_id' => $padre_tres->id,
                                                                     'nivel' =>  3,
                                                                     'puntos' =>  $puntos,
                                                                 ]
                                                             );
                                                         }
                                                     }
                                                 }
                                             }
                                         }
                                     }
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
