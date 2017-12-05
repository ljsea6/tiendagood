<?php

namespace App\Console\Commands;


use App\Product;
use App\Variant;
use Illuminate\Console\Command;

class GetProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para obtener todos los productos de la API shopify';

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

        $r = $client->request('GET', $api_url . '/admin/products/count.json');
        $count = json_decode($r->getBody(), true);


        do {

            $resa = $client->request('GET', $api_url . '/admin/products.json?limit=250&&page=' . $h);



            $headers = $resa->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];
            if ($diferencia < 20) {

                usleep(20000000);
            }

            $results = json_decode($resa->getBody(), true);

            foreach ($results['products'] as  $product) {

                $response = Product::where('shop', 'good')
                    ->where('id', $product['id'])
                    ->first();

                if(count($response) == 0) {


                    $a = $client->request('GET', $api_url . '/admin/collects.json?product_id=' . $product['id']);
                    $headers = $a->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                    $x = explode('/', $headers[0]);
                    $diferencia = $x[1] - $x[0];
                    if ($diferencia < 20) {

                        usleep(20000000);
                    }

                    $collections = json_decode($a->getBody(), true);

                    if (count($collections['collects']) > 0) {

                        foreach ($collections['collects'] as $collect) {

                            if ($collect['collection_id'] == 25960513573) {

                                Product::createProduct($product, 'nacional', 'good');

                                foreach ($product['variants'] as $variant) {

                                    Variant::createVariant($variant, 0, 'good');

                                    try {

                                        $resb = $client->request('get', $api_url . '/admin/variants/'. $variant['id'] .'/metafields.json');

                                        $rs = json_decode($resb->getBody(), true);

                                        if (count($rs['metafields']) > 0) {

                                            foreach ($rs['metafields'] as $r) {

                                                if ($r['key'] == 'points' && $r['namespace'] == 'variants') {

                                                    try {

                                                        $resc = $client->request('put', $api_url . '/admin/variants/'. $variant['id'] .'/metafields/' . $r['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'variants',
                                                                        'key' => 'points',
                                                                        'value' => 0,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );

                                                        $headers = $resc->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                                        $x = explode('/', $headers[0]);
                                                        $diferencia = $x[1] - $x[0];
                                                        if ($diferencia < 10) {
                                                            usleep(10000000);
                                                        }

                                                    } catch (ClientException $e) {

                                                        return json_decode(($e->getResponse()->getBody()), true);
                                                    }
                                                }
                                            }

                                        } else {

                                            try {

                                                $resd = $client->request('post', $api_url . '/admin/variants/'. $variant['id'] .'/metafields.json', array(
                                                    'form_params' => array(
                                                        'metafield' => array(
                                                            'namespace' => 'variants',
                                                            'key' => 'points',
                                                            'value' => 0,
                                                            'value_type' => 'integer'
                                                        )
                                                    )
                                                ));

                                                $headers = $resd->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                                $x = explode('/', $headers[0]);
                                                $diferencia = $x[1] - $x[0];
                                                if ($diferencia < 20) {

                                                    usleep(10000000);
                                                }

                                            } catch (ClientException $e) {

                                                return json_decode(($e->getResponse()->getBody()), true);
                                            }
                                        }

                                    } catch (ClientException $e) {

                                        return json_decode(($e->getResponse()->getBody()), true);
                                    }
                                }

                            } else {

                                Product::createProduct($product, 'internacional', 'good');

                                foreach ($product['variants'] as $variant) {


                                    // variable para asignar puntos a las variantes
                                    $puntos = 0;
                                    // Asignamos a p los puntos asignados por cada 1000 pesos
                                    $p = $variant['price']/1000;
                                    // Partimos en dos después del . para redondear por el valor menor
                                    $partir = explode('.', $p);
                                    // Si tiene dos partes asignamos lo que hay en la posición 0
                                    if (count($partir) > 1) {
                                        $puntos = $puntos +  (int)$partir[0];
                                    }
                                    // Si tiene una partes asignamos lo que hay en la variable $partir
                                    if (count($partir) == 1) {
                                        $puntos = $puntos +  (int)$partir;
                                    }

                                    Variant::createVariant($variant, $puntos, 'good');

                                    try {

                                        $resb = $client->request('get', $api_url . '/admin/variants/'. $variant['id'] .'/metafields.json');

                                        $rs = json_decode($resb->getBody(), true);

                                        if (count($rs['metafields']) > 0) {

                                            foreach ($rs['metafields'] as $r) {

                                                if ($r['key'] == 'points' && $r['namespace'] == 'variants') {

                                                    try {

                                                        $resc = $client->request('put', $api_url . '/admin/variants/'. $variant['id'] .'/metafields/' . $r['id'] . '.json', array(
                                                                'form_params' => array(
                                                                    'metafield' => array(
                                                                        'namespace' => 'variants',
                                                                        'key' => 'points',
                                                                        'value' => 0,
                                                                        'value_type' => 'integer'
                                                                    )
                                                                )
                                                            )
                                                        );

                                                        $headers = $resc->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                                        $x = explode('/', $headers[0]);
                                                        $diferencia = $x[1] - $x[0];
                                                        if ($diferencia < 10) {
                                                            usleep(10000000);
                                                        }

                                                    } catch (ClientException $e) {

                                                        return json_decode(($e->getResponse()->getBody()), true);
                                                    }
                                                }
                                            }

                                        } else {

                                            try {

                                                $resd = $client->request('post', $api_url . '/admin/variants/'. $variant['id'] .'/metafields.json', array(
                                                    'form_params' => array(
                                                        'metafield' => array(
                                                            'namespace' => 'variants',
                                                            'key' => 'points',
                                                            'value' => 0,
                                                            'value_type' => 'integer'
                                                        )
                                                    )
                                                ));

                                                $headers = $resd->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                                $x = explode('/', $headers[0]);
                                                $diferencia = $x[1] - $x[0];
                                                if ($diferencia < 20) {

                                                    usleep(10000000);
                                                }

                                            } catch (ClientException $e) {

                                                return json_decode(($e->getResponse()->getBody()), true);
                                            }
                                        }

                                    } catch (ClientException $e) {

                                        return json_decode(($e->getResponse()->getBody()), true);
                                    }
                                }
                            }
                        }

                    }


                }

                if (count($response) > 0 ) {

                    foreach ($product['variants'] as $variant) {

                        Variant::updateVariant($variant, 'good');
                    }

                    $update = Product::find($response->id);

                    $update->image = $product['image'];
                    $update->images = $product['images'];
                    $update->vendor = $product['vendor'];
                    $update->save();

                    //return response()->json(['status' => 'The resource is updated successfully'], 200);
                }
            }

            $h++;

            if (count($results['products']) < 1) {
                $result = false;
            }

        } while($result);


        $this->info('Los productos han sido descargados correctamente');
    }
}
