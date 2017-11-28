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
        //$api_url = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';
        $api_url = 'https://'. env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $api_url . '/admin/products/count.json');
        $countProducts = json_decode($res->getBody(), true);

        $this->info('Cantidad Productos' . $countProducts['count']);

        $result = true;
        $h = 1;

        do {
            $this->info('Entrando al do');

            $res = $client->request('GET', $api_url . '/admin/products.json?limit=250&&page=' . $h);

            $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];
            if ($diferencia < 20) {
                $this->info('Durmiendo');
                usleep(20000000);
            }

            $results = json_decode($res->getBody(), true);

            $this->info(count($results['products']));

            foreach ($results['products'] as  $product) {

                $this->info('entrando al ciclo');

                $response = Product::find($product['id']);

                if(count($response) == 0) {

                    $this->info('creando producto');

                    $tipo_producto = '';
                    if (strtolower($product['vendor']) == 'nacional'  || strtolower($product['vendor']) == 'a-nacional') {
                        $tipo_producto .= 'nacional';
                    } else {
                        $tipo_producto .= 'internacional';
                    }

                    Product::createProduct($product, $tipo_producto);

                    foreach ($product['variants'] as $variant) {

                        $puntos = 0;

                        if ($tipo_producto == 'internacional') {

                            $puntos = $puntos + 1 * $variant['price'];

                            Variant::createVariant($variant, $puntos);


                            try {

                                $res = $client->request('get', $api_url . '/admin/variants/'. $variant['id'] .'/metafields.json');

                                $results = json_decode($res->getBody(), true);

                                if (count($results['metafields']) > 0) {

                                    foreach ($results['metafields'] as $result) {

                                        if ($result['key'] == 'points' && $result['namespace'] == 'variants') {

                                            try {

                                                $res = $client->request('put', $api_url . '/admin/variants/'. $variant['id'] .'/metafields/' . $result['id'] . '.json', array(
                                                        'form_params' => array(
                                                            'metafield' => array(
                                                                'namespace' => 'variants',
                                                                'key' => 'points',
                                                                'value' => $puntos,
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

                                                //return json_decode($res->getBody(), true);

                                            } catch (ClientException $e) {

                                                return json_decode(($e->getResponse()->getBody()), true);
                                            }
                                        }
                                    }

                                } else {

                                    try {

                                        $res = $client->request('post', $api_url . '/admin/variants/'. $variant['id'] .'/metafields.json', array(
                                            'form_params' => array(
                                                'metafield' => array(
                                                    'namespace' => 'variants',
                                                    'key' => 'points',
                                                    'value' => $puntos,
                                                    'value_type' => 'integer'
                                                )
                                            )
                                        ));

                                        $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                                        $x = explode('/', $headers[0]);
                                        $diferencia = $x[1] - $x[0];
                                        if ($diferencia < 20) {

                                            usleep(10000000);
                                        }

                                        $result = json_decode($res->getBody(), true);



                                    } catch (ClientException $e) {

                                        return json_decode(($e->getResponse()->getBody()), true);
                                    }



                                }

                            } catch (ClientException $e) {

                                return json_decode(($e->getResponse()->getBody()), true);
                            }


                        } else {

                            Variant::createVariant($variant, 0);
                        }


                    }

                    $tipo_producto = '';


                    $this->info('saliendo creando producto');
                }

                if (count($response) > 0 && count($response->image) == 0 ) {
                    $this->info('actualizando producto');

                    foreach ($product['variants'] as $variant) {

                        Variant::updateVariant($variant);
                    }

                    $response->image = $product['image'];
                    $response->images = $product['images'];
                    $response->vendor = $product['vendor'];
                    $response->save();

                    $this->info('saliendo actualizando producto');
                }
                $this->info('saliendo el ciclo');
            }

            $h++;

            if (count($results['products']) < 1) {
                $result = false;
            }

            $this->info('Saliendo del do');

        } while($result);

        $this->info('Los productos han sido descargados correctamente');
    }
}
