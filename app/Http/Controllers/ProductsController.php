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
        $products = Product::select('id', 'title', 'tipo_producto')
            ->get();

        $send = collect($products);

        return Datatables::of($send)

            ->addColumn('id', function ($send) {
                return '<div align=left>' . $send['id'] . '</div>';
            })
            ->addColumn('title', function ($send) {
                return '<div align=left>' . $send['title'] . '</div>';
            })
            ->addColumn('tipo_producto', function ($send) {
                return '<div align=left>' . $send->tipo_producto . '</div>';
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

        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($input), $hmac_header);
        $resultapi = error_log('Webhook verified: '.var_export($verified, true));

        if ($resultapi == 'true') {

            $product = json_decode($input, true);

            $response = Product::find($product['id']);

            if(count($response) == 0) {
                $tipo_producto = '';
                if (strtolower($product['vendor']) == 'nacional'  || strtolower($product['vendor']) == 'a-nacional') {
                    $tipo_producto .= 'nacional';
                } else {
                    $tipo_producto .= 'internacional';
                }

                Product::createProduct($product, $tipo_producto);

                $tipo_producto = '';

                foreach ($product['variants'] as $variant) {

                    Variant::createVariant($variant);
                }

                return response()->json(['status' => 'The resource is created successfully'], 200);

            } else {

                foreach ($product['variants'] as $variant) {
                    Variant::updateVariant($variant);
                }

                return response()->json(['status' => 'The resource has been updated successfully'], 200);
            }

        }

    }

    public function update()
    {
        $input = file_get_contents('php://input');

        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        $verified = $this->verify_webhook(collect($input), $hmac_header);
        $resultapi = error_log('Webhook verified: '.var_export($verified, true));

        if ($resultapi == 'true') {

            $product = json_decode($input, true);

            $response = Product::find($product['id']);

            if(count($response) == 0) {

                $tipo_producto = '';

                if (strtolower($product['vendor']) == 'nacional'  || strtolower($product['vendor']) == 'a-nacional') {
                    $tipo_producto .= 'nacional';
                } else {
                    $tipo_producto .= 'internacional';
                }

                Product::createProduct($product, $tipo_producto);

                $tipo_producto = '';

                foreach ($product['variants'] as $variant) {

                    Variant::createVariant($variant);
                }

                return response()->json(['status' => 'The resource is created successfully'], 200);

            } else {

                $response->body_html = $product['body_html'];
                $response->created_at = Carbon::parse($product['created_at']);
                $response->handle = $product['handle'];
                $response->image = $product['image'];
                $response->images = $product['images'];
                $response->options = $product['options'];
                $response->product_type = $product['product_type'];
                $response->published_at = Carbon::parse($product['published_at']);
                $response->published_scope = $product['published_scope'];
                $response->tags = $product['tags'];
                $response->template_suffix = ($product['template_suffix'] !== null ) ? $product['template_suffix'] : null;
                $response->title = $product['title'];
                $response->metafields_global_title_tag = (isset($product['metafields_global_title_tag'])) ? $product['metafields_global_title_tag'] : null;
                $response->metafields_global_description_tag = (isset($product['metafields_global_description_tag'])) ? $product['metafields_global_description_tag'] : null;
                $response->updated_at = Carbon::parse($product['updated_at']);
                $response->variants = $product['variants'];
                $response->vendor = $product['vendor'];
                $response->save();

                foreach ($response->variants as $variant) {

                    Variant::updateVariant($variant);
                }

                return response()->json(['status' => 'The resource has been updated successfully'], 200);
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
