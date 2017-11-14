<?php

namespace App\Http\Controllers;

use DB;
use App\Product;
use App\Variant;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;


class VariantsController extends Controller
{


    public function variants()
    {
        ini_set('memory_limit', '1000M');
        $variants = DB::table('variants')
            ->join('products', 'variants.product_id', '=', 'products.id')
            ->select('variants.id as id', 'variants.title as title', 'variants.price as price', 'variants.sold_units as sold_units', 'variants.percentage as percentage', 'products.title as product')
            //->where('variants.sold_units', '>', 0)
            ->get();

        $send = collect($variants);

        return Datatables::of($send)
            ->addColumn('id', function ($send) {
                return '<div align=left>' . $send->id . '</div>';
            })
            ->addColumn('title', function ($send) {

                if ($send->title == 'Default Title') {

                    return '<div align=left>' . $send->product . '</div>';

                } else {

                    return '<div align=left>' . $send->product . ' ' . $send->title . '</div>';
                }

            })
            ->addColumn('price', function ($send) {
                return '<div align=left>' . number_format($send->price) . '</div>';
            })
            ->addColumn('sold_units', function ($send) {
                return '<div align=left>' . number_format($send->sold_units) . '</div>';
            })
            ->addColumn('percentage', function ($send) {
                return '<div align=left><input id='. $send->id .' name='. $send->id .'  type="number" value="'.number_format($send->percentage).'"></div>';
            })
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.variants.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function update(Request $request)
    {
        $api_url = 'https://'. env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');
        $client = new \GuzzleHttp\Client();

        if (isset($request['value'])) {

            $datas = explode('&', $request['value']);

            foreach ($datas as $data) {

                $result = explode('=', $data);

                $variant = Variant::find($result[0]);

                if ($result[1] != "") {

                    $variant->percentage = $result[1];
                    $variant->save();




                }else {
                    $variant->percentage = null;
                    $variant->save();
                }
            }

            return response()->json(['data' => 'actualización terminada']);
        }
    }

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
