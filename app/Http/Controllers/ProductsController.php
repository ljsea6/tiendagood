<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Product;
use Carbon\Carbon;
use App\Variant;


class ProductsController extends Controller
{
    /**
     * Undocumented function
     *
     * @return Products
     */
    function verify_webhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, 'afc86df7e11dcbe0ab414fa158ac1767', true));
        return hash_equals($hmac_header, $calculated_hmac);
    }

    public function welcome()
    {
        return view('admin.reportes.products');
    }

    public function index()
    {
        return view('admin.productos.home');
    }

    public function anyData()
    {
        $products = Product::select('id', 'title', 'tipo_producto', 'shop')
            ->get();

        $send = collect($products);

        return Datatables::of($send)

            ->addColumn('id', function ($send) {
                return '<div align=left>' . $send['id'] . '</div>';
            })
            ->addColumn('title', function ($send) {
                return '<div align=left>' . ucwords($send['title']) . '</div>';
            })
            ->addColumn('shop', function ($send) {
                return '<div align=left>' . ucwords($send['shop']) . '</div>';
            })
            ->addColumn('tipo_producto', function ($send) {
                return '<div align=left>' . ucwords($send->tipo_producto) . '</div>';
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $input = file_get_contents('php://input');
        $api_url = 'https://'. env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');
        $client = new \GuzzleHttp\Client();

        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($input), $hmac_header);
        $resultapi = error_log('Webhook verified: '.var_export($verified, true));

        if ($resultapi == 'true') {

            $product = json_decode($input, true);

            $response = Product::where('shop', 'good')
                ->where('id', $product['id'])
                ->first();

            if (count($response) == 0) {

                Product::createProduct($product, 'internacional', 'good');

                foreach ($product['variants'] as $variant) {

                    // variable para asignar puntos a las variantes
                    $puntos = 0;

                    // Asignamos a p los puntos asignados por cada 1000 pesos
                    $p = $variant['price'] / 1000;

                    // Partimos en dos después del . para redondear por el valor menor
                    $partir = explode('.', $p);

                    // Si tiene dos partes asignamos lo que hay en la posición 0
                    if (count($partir) > 1) {
                        $puntos = $puntos + (int)$partir[0];
                    }

                    // Si tiene una partes asignamos lo que hay en la variable $partir
                    if (count($partir) == 1) {
                        $puntos = $puntos + (int)$partir;
                    }


                    // Método para crear una Variante con sus puntos
                    Variant::createVariant($variant, $puntos, 'good');

                    try {

                        $resb = $client->request('get', $api_url . '/admin/variants/' . $variant['id'] . '/metafields.json');

                        $rs = json_decode($resb->getBody(), true);

                        if (count($rs['metafields']) > 0) {

                            foreach ($rs['metafields'] as $r) {

                                if ($r['key'] == 'points' && $r['namespace'] == 'variants') {

                                    try {

                                        $resc = $client->request('put', $api_url . '/admin/variants/' . $variant['id'] . '/metafields/' . $r['id'] . '.json', array(
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

                                $resd = $client->request('post', $api_url . '/admin/variants/' . $variant['id'] . '/metafields.json', array(
                                    'form_params' => array(
                                        'metafield' => array(
                                            'namespace' => 'variants',
                                            'key' => 'points',
                                            'value' => $puntos,
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

                return response()->json(['status' => 'The resource is created successfully'], 200);

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

                return response()->json(['status' => 'The resource is updated successfully'], 200);
            }
        }

    }

    public function update()
    {
        $input = file_get_contents('php://input');
        $api_url = 'https://'. env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');
        $client = new \GuzzleHttp\Client();


        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($input), $hmac_header);
        $resultapi = error_log('Webhook verified: '.var_export($verified, true));

        if ($resultapi == 'true') {

            $product = json_decode($input, true);

            $response = Product::where('shop', 'good')
                ->where('id', $product['id'])
                ->first();

            if (count($response) == 0) {

                Product::createProduct($product, 'internacional', 'good');

                foreach ($product['variants'] as $variant) {

                    // variable para asignar puntos a las variantes
                    $puntos = 0;

                    // Asignamos a p los puntos asignados por cada 1000 pesos
                    $p = $variant['price'] / 1000;

                    // Partimos en dos después del . para redondear por el valor menor
                    $partir = explode('.', $p);

                    // Si tiene dos partes asignamos lo que hay en la posición 0
                    if (count($partir) > 1) {
                        $puntos = $puntos + (int)$partir[0];
                    }

                    // Si tiene una partes asignamos lo que hay en la variable $partir
                    if (count($partir) == 1) {
                        $puntos = $puntos + (int)$partir;
                    }


                    // Método para crear una Variante con sus puntos
                    Variant::createVariant($variant, $puntos, 'good');

                    try {

                        $resb = $client->request('get', $api_url . '/admin/variants/' . $variant['id'] . '/metafields.json');

                        $rs = json_decode($resb->getBody(), true);

                        if (count($rs['metafields']) > 0) {

                            foreach ($rs['metafields'] as $r) {

                                if ($r['key'] == 'points' && $r['namespace'] == 'variants') {

                                    try {

                                        $resc = $client->request('put', $api_url . '/admin/variants/' . $variant['id'] . '/metafields/' . $r['id'] . '.json', array(
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

                                $resd = $client->request('post', $api_url . '/admin/variants/' . $variant['id'] . '/metafields.json', array(
                                    'form_params' => array(
                                        'metafield' => array(
                                            'namespace' => 'variants',
                                            'key' => 'points',
                                            'value' => $puntos,
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

                return response()->json(['status' => 'The resource is created successfully'], 200);

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

                return response()->json(['status' => 'The resource is updated successfully'], 200);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
