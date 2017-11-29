<?php

namespace App\Console\Commands;

use App\Product;
use App\Variant;
use Illuminate\Console\Command;

class GetProductsMercando extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:products-mercando';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para descargar la lista de productos de mercando';

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
        $api_url = 'https://'. env('API_KEY_MERCANDO') . ':' . env('API_PASSWORD_MERCANDO') . '@' . env('API_SHOP_MERCANDO');
        $client = new \GuzzleHttp\Client();
        $result = true;
        $h = 1;

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

                $response = Product::where('shop', 'mercando')
                    ->where('id', $product['id'])
                    ->first();

                if(count($response) == 0) {

                    Product::createProduct($product, 'nacional', 'mercando');

                    foreach ($product['variants'] as $variant) {

                        Variant::createVariant($variant, 0, 'mercando');

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

                if (count($response) > 0 ) {

                    foreach ($product['variants'] as $variant) {

                        Variant::updateVariant($variant, 'mercando');
                    }

                    $update = Product::find($response->id);

                    $update->image = $product['image'];
                    $update->images = $product['images'];
                    $update->vendor = $product['vendor'];
                    $update->save();

                    return response()->json(['status' => 'The resource is updated successfully'], 200);
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
