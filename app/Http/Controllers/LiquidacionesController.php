<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Session;
use Carbon\Carbon;
use App\Entities\Liquidaciones;
use App\Order;
use App\Entities\LiquidacionDetalle;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;


use Excel;

class LiquidacionesController extends Controller {

    public function get_liquidar() {
        return view('admin.liquidaciones.liquidar');
    }

    public function post_liquidar(Request $request) {

    if ($request->has('liquidar')){

        ini_set("memory_limit", "-1");

        $liquidar = new Liquidaciones();
        $liquidar->usuario_id = currentUser()->id;
        $liquidar->fecha_inicio = $request->fecha_inicio;
        $liquidar->fecha_final = $request->fecha_final;
        $liquidar->fecha_liquidacion = Carbon::now();
        $liquidar->created_at = Carbon::now();
        $liquidar->updated_at = Carbon::now();
        $liquidar->save();

        $liquidacion_id = $liquidar->id;


        $id_primer_nivel = array();
        $id_dos_nivel = array();
        $id_tres_nivel = array();


        $id_primer_nivel_amparado = array();
        $id_dos_nivel_amparado = array();
        $id_tres_nivel_amparado = array();



        $level_uno = 0;
        $level_dos = 0;
        $level_tres = 0;

        $my_points = 0;

        $gente_nivel_1 = array();
        $count_add=0;
        $vendedores_liquidados = array();
        $id_vendedores = array();
        $id_vendedores_tipo = array();


        $puntos = DB::raw("(select fpl_dir(t.id::integer,0)) as puntos_propios");

//->where('t.id', 41)
//->limit(41)
    $vendedores = DB::table('terceros as t')->where('t.tipo_cliente_id', 83)->where('t.state', true)
   // ->limit(41)
    ->select('t.id', 't.tipo_id', $puntos)->orderByRaw('id ASC')->get();

    foreach ($vendedores as $value_vendedor) {
        
        if($value_vendedor->puntos_propios >= 1){
            
            $points_level_1 = 0;
            $points_level_2 = 0;
            $points_level_3 = 0;

            $comision_valor_1 = 0;
            $comision_valor_2 = 0;
            $comision_valor_3 = 0;

            $id_detalle_1 = 0;
            $id_detalle_2 = 0;
            $id_detalle_3 = 0;


            /*   ----------------------------------------------------------------------------------------------------------------------------------------  */
            /*                                                    reglas   inicio     ------------------------                                             */
            /*   ----------------------------------------------------------------------------------------------------------------------------------------  */
            $rules = DB::table('rules')->where('tipo_id', $value_vendedor->tipo_id)->join('rules_details', 'rules_details.rule_id', '=', 'rules.id')
            ->select('nivel','comision_puntos','rules_details.id')->get();
            foreach ($rules as $rules_details_value) { 
                    if($rules_details_value->nivel == 1){
                        $comision_valor_1 = $rules_details_value->comision_puntos;
                        $id_detalle_1 = $rules_details_value->id;
                    }
                    if($rules_details_value->nivel == 2){
                        $comision_valor_2 = $rules_details_value->comision_puntos;
                        $id_detalle_2 = $rules_details_value->id;
                    }
                    if($rules_details_value->nivel == 3){
                        $comision_valor_3 = $rules_details_value->comision_puntos;
                        $id_detalle_3 = $rules_details_value->id;
                    } 
            }
            
            $id_vendedores[] = array($value_vendedor->id);
            $id_vendedores_tipo[$value_vendedor->id] = array('comision_valor_1' => $comision_valor_1, 'id_detalle_1' => $id_detalle_1, 
                                                            'comision_valor_2' => $comision_valor_2, 'id_detalle_2' => $id_detalle_2, 
                                                            'comision_valor_3' => $comision_valor_3, 'id_detalle_3' => $id_detalle_3);
            /*   ----------------------------------------------------------------------------------------------------------------------------------------  */
            /*                                                    reglas     fin                                                                           */
            /*   ----------------------------------------------------------------------------------------------------------------------------------------  */       
        }
    }


            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */
            /*                                                     terceros y ordenes del nivel uno con sus amparados    inicio  --------------------------                  */
            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */

            $uno = DB::table('terceros as t')
                ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                ->join('terceros as t2', 't2.id', '=', 'tk.customer_id')
                ->leftjoin('orders', 'orders.tercero_id', '=', 't2.id')
                ->whereIn('tk.padre_id', $id_vendedores)->where('t.state', true)->where('t2.state', true)->where('t2.tipo_cliente_id', '<>', 85)
                ->select('tk.padre_id as padre', 't2.id', 't2.email', 't2.nombres', 't2.apellidos', 't2.tipo_cliente_id','points', 'orders.id as orden_id',
                    'orders.financial_status', 'orders.cancelled_at', 'orders.comisionada', 'orders.liquidacion_id')->get();

            if (count($uno) > 0) {
                $level_uno = $level_uno + count($uno);
                foreach ($uno as $n) {

                    $id_primer_nivel[] = array($n->orden_id);

                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */
                    /*                                                     ordenes del nivel uno con sus amparados   inicio     ------------------------           */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */

                    $points_level_vendedor_1 = 0;
                    /*
                       $uno_amparados_total = 0;
                       $uno_amparados = DB::table('terceros as t')->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')->where('tk.padre_id', $n->id)
                       ->where('t.state', true)->where('t.tipo_cliente_id', 85)
                       ->select('t.id', 't.email', 't.nombres', 't.apellidos', 't.tipo_cliente_id')->get();
                       foreach ($uno_amparados as $uno_amparados_value) {
                           $uno_amparados_total++;
                           $uno_amparados_orders = DB::table('orders')->where('tercero_id', $uno_amparados_value->id)
                           ->where('financial_status', 'paid')
                           ->where('cancelled_at', null)
                           ->where('comisionada', null)
                           ->where('liquidacion_id', null)
                           ->select('points', 'orders.id as orden_id')->get();
                           foreach ($uno_amparados_orders as $uno_amparados_orders_value) {
                               $points_level_1 += $uno_amparados_orders_value->points;
                               $points_level_vendedor_1 += $uno_amparados_orders_value->points;

                               DB::table('liquidaciones_detalles')->insert([
                               'liquidacion_id' => $liquidacion_id,
                               'tercero_id' => $n->id,
                               'hijo_id' => $uno_amparados_value->id,
                               'nivel' => 1,
                               'order_id' => $uno_amparados_orders_value->orden_id,
                               'regla_detalle_id' => $id_detalle_1,
                               'valor_comision' => ($comision_valor_1 * $uno_amparados_orders_value->points),
                               'puntos' => ($uno_amparados_orders_value->points),
                               'comision_puntos' => ($comision_valor_1),
                               'created_at' => Carbon::now(),
                               'updated_at' => Carbon::now()
                               ]);

                               $id_primer_nivel_amparado[] = array($n->orden_id);

                           }
                       }
                       */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */
                    /*                                                     ordenes del nivel uno con sus amparados    fin                                          */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */

                    if($n->financial_status == 'paid' && $n->cancelled_at == '' && $n->comisionada == '' && $n->liquidacion_id == ''){

                        $points_level_1 +=  $n->points;
                        $points_level_vendedor_1 += $n->points;
                        
                        $id_detalle_1 = $id_vendedores_tipo[$n->padre]['id_detalle_1'];
                        $comision_valor_1 = $id_vendedores_tipo[$n->padre]['comision_valor_1'];

                        DB::table('liquidaciones_detalles')->insert([
                            'liquidacion_id' => $liquidacion_id,
                            'tercero_id' => $n->padre,
                            'hijo_id' => $n->id,
                            'nivel' => 1,
                            'order_id' => $n->orden_id,
                            'regla_detalle_id' => $id_detalle_1,
                            'valor_comision' => ($comision_valor_1 * $n->points),
                            'puntos' => ($n->points),
                            'comision_puntos' => ($comision_valor_1),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                    }


                    //  $gente_nivel_1[] = array('id' => $n->id);
                }
            }
 
            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */
            /*                                                     terceros y ordenes del nivel uno con sus amparados    fin                                                 */
            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */

            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */
            /*                                                     terceros y ordenes del nivel dos con sus amparados    inicio ------------------                          */
            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */

            $dos = DB::table('terceros as t')
                ->join('terceros_networks as tk', 'tk.padre_id', '=', 't.id')
                ->join('terceros as t2', 't2.id', '=', 'tk.customer_id')
                ->join('terceros_networks as tk2', 'tk2.padre_id', '=', 't2.id')
                ->join('terceros as t3', 't3.id', '=', 'tk2.customer_id')
                ->leftjoin('orders', 'orders.tercero_id', '=', 't3.id')
                ->whereIn('t.id', $id_vendedores)->where('t.state', true)->where('t3.state', true)->where('t3.tipo_cliente_id', '<>', 85)
                ->select('t.id as padre', 't3.id', 't3.email', 't3.nombres', 't3.apellidos', 't3.tipo_cliente_id','points', 'orders.id as orden_id',
                    'orders.financial_status', 'orders.cancelled_at', 'orders.comisionada', 'orders.liquidacion_id')->get();

            if (count($dos) > 0) {

                $level_dos = $level_dos + count($dos);
                $gente_nivel_2 = array();
                $count_add=0;
                foreach ($dos as $d) {  $count_add++;

                    $id_dos_nivel[] = array($d->orden_id);

                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */
                    /*                                                     ordenes del nivel dos con sus amparados   inicio     ------------------------           */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */

                    $points_level_vendedor_2 = 0;
                    /*
                        $dos_amparados_total = 0;
                        $dos_amparados = DB::table('terceros as t')->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')->where('tk.padre_id', $d->id)
                        ->where('t.state', true)->where('t.tipo_cliente_id', 85)->select('t.id', 't.email', 't.nombres', 't.apellidos', 't.tipo_cliente_id')->get();
                        foreach ($dos_amparados as $dos_amparados_value) {
                            $dos_amparados_total++;
                            $dos_amparados_orders = DB::table('orders')->where('tercero_id', $dos_amparados_value->id)
                            ->where('financial_status', 'paid')
                            ->where('cancelled_at', null)
                            ->where('comisionada', null)
                            ->where('liquidacion_id', null)
                            ->select('points', 'orders.id as orden_id')->get();
                            foreach ($dos_amparados_orders as $dos_amparados_orders_value) {
                                $points_level_2 += $dos_amparados_orders_value->points;
                                $points_level_vendedor_2 += $dos_amparados_orders_value->points;

                                DB::table('liquidaciones_detalles')->insert([
                                'liquidacion_id' => $liquidacion_id,
                                'tercero_id' => $d->id,
                                'hijo_id' => $dos_amparados_value->id,
                                'nivel' => 2,
                                'order_id' => $dos_amparados_orders_value->orden_id,
                                'regla_detalle_id' => $id_detalle_2,
                                'valor_comision' => ($comision_valor_2 * $dos_amparados_orders_value->points),
                                'puntos' => ($dos_amparados_orders_value->points),
                                'comision_puntos' => ($comision_valor_2),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                                ]);

                                $id_dos_nivel_amparado[] = array($n->orden_id);

                            }
                        }
                         */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */
                    /*                                                     ordenes del nivel dos con sus amparados    fin                                          */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */

                    if($d->financial_status == 'paid' && $d->cancelled_at == '' && $d->comisionada == '' && $d->liquidacion_id == ''){

                        $points_level_2 += $d->points;
                        $points_level_vendedor_2 += $d->points;
                        
                        $id_detalle_2 = $id_vendedores_tipo[$d->padre]['id_detalle_2'];
                        $comision_valor_2 = $id_vendedores_tipo[$d->padre]['comision_valor_2'];

                        DB::table('liquidaciones_detalles')->insert([
                            'liquidacion_id' => $liquidacion_id,
                            'tercero_id' => $d->padre,
                            'hijo_id' => $d->id,
                            'nivel' => 2,
                            'order_id' => $d->orden_id,
                            'regla_detalle_id' => $id_detalle_2,
                            'valor_comision' => ($comision_valor_2 * $d->points),
                            'puntos' => ($d->points),
                            'comision_puntos' => ($comision_valor_2),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                    }
                    //  $gente_nivel_2[] = array('nombre' => $d->id.'-'.$d->nombres.'-'.$d->apellidos.'-'.$d->email.'-'.$d->tipo_cliente_id.' amparados: '.$count_add.' puntos: '.$points_level_vendedor_2.'<br>sd');
                }
            }

            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */
            /*                                                     terceros y ordenes del nivel dos con sus amparados    fin                                                 */
            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */

            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */
            /*                                                     terceros y ordenes del nivel tres con sus amparados    inicio ------------------                          */
            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */

            $tres = DB::table('terceros as t')
                ->join('terceros_networks as tk', 'tk.padre_id', '=', 't.id')
                ->join('terceros as t2', 't2.id', '=', 'tk.customer_id')
                ->join('terceros_networks as tk2', 'tk2.padre_id', '=', 't2.id')
                ->join('terceros as t3', 't3.id', '=', 'tk2.customer_id')
                ->join('terceros_networks as tk3', 'tk3.padre_id', '=', 't3.id')
                ->join('terceros as t4', 't4.id', '=', 'tk3.customer_id')
                ->leftjoin('orders', 'orders.tercero_id', '=', 't4.id')
                ->whereIn('t.id', $id_vendedores)->where('t.state', true)->where('t4.state', true)->where('t4.tipo_cliente_id', '<>', 85)
                ->select('t.id as padre', 't4.id', 't4.email', 't4.nombres', 't4.apellidos', 't4.tipo_cliente_id','points', 'orders.id as orden_id',
                    'orders.financial_status', 'orders.cancelled_at', 'orders.comisionada', 'orders.liquidacion_id')->get();

            if (count($tres) > 0) {

                $level_tres = $level_tres + count($tres);
                $gente_nivel_3 = array();

                foreach ($tres as $t) {

                    $id_tres_nivel[] = array($t->orden_id);

                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */
                    /*                                                     ordenes del nivel tres con sus amparados   inicio     ------------------------          */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */

                    $points_level_vendedor_3 = 0;
                    /*
                        $tres_amparados_total = 0;
                        $tres_amparados = DB::table('terceros as t')->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')->where('tk.padre_id', $t->id)
                        ->where('t.state', true)->where('t.tipo_cliente_id', 85)->select('t.id', 't.email', 't.nombres', 't.apellidos', 't.tipo_cliente_id')->get();
                        foreach ($tres_amparados as $tres_amparados_value) {
                            $tres_amparados_total++;
                            $tres_amparados_orders = DB::table('orders')->where('tercero_id', $tres_amparados_value->id)
                            ->where('financial_status', 'paid')
                            ->where('cancelled_at', null)
                            ->where('comisionada', null)
                            ->where('liquidacion_id', null)
                            ->select('points', 'orders.id as orden_id')->get();
                            foreach ($tres_amparados_orders as $tres_amparados_orders_value) {
                                $points_level_3 += $tres_amparados_orders_value->points;
                                $points_level_vendedor_3 += $tres_amparados_orders_value->points;

                                DB::table('liquidaciones_detalles')->insert([
                                'liquidacion_id' => $liquidacion_id,
                                'tercero_id' => $t->id,
                                'hijo_id' => $tres_amparados_value->id,
                                'nivel' => 3,
                                'order_id' => $tres_amparados_orders_value->orden_id,
                                'regla_detalle_id' => $id_detalle_3,
                                'valor_comision' => ($comision_valor_3 * $tres_amparados_orders_value->points),
                                'puntos' => ($tres_amparados_orders_value->points),
                                'comision_puntos' => ($comision_valor_3),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                                ]);

                                $id_tres_nivel_amparado[] = array($n->orden_id);

                            }
                        }
                    */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */
                    /*                                                     ordenes del nivel tres con sus amparados    fin                                         */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */

                    if($t->financial_status == 'paid' && $t->cancelled_at == '' && $t->comisionada == '' && $t->liquidacion_id == ''){

                        $points_level_3 += $t->points;
                        $points_level_vendedor_3 += $t->points;
 
                        $id_detalle_3 = $id_vendedores_tipo[$t->padre]['id_detalle_3'];
                        $comision_valor_3 = $id_vendedores_tipo[$t->padre]['comision_valor_3'];

                        DB::table('liquidaciones_detalles')->insert([
                            'liquidacion_id' => $liquidacion_id,
                            'tercero_id' => $t->padre,
                            'hijo_id' => $t->id,
                            'nivel' => 3,
                            'order_id' => $t->orden_id,
                            'regla_detalle_id' => $id_detalle_3,
                            'valor_comision' => ($comision_valor_3 * $t->points),
                            'puntos' => ($t->points),
                            'comision_puntos' => ($comision_valor_3),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                    }
                    //  $gente_nivel_3[] = array('nombre' => $t->id.'-'.$t->nombres.'-'.$t->apellidos.'-'.$t->email.'-'.$t->tipo_cliente_id.' amparados: '.$tres_amparados_total.' puntos: '.$points_level_vendedor_3.'<br>');
                }
            }


            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */
            /*                                                     terceros y ordenes del nivel tres con sus amparados    fin                                         */
            /*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */

            //echo $value_vendedor->id.' - puntos: '.$points_level_1.' - comision: '.$comision_valor_1.' - puntos: '.$points_level_2.' - comision: '.$comision_valor_2.' - puntos:  '.$points_level_3.' - comision: '.$comision_valor_3.'<br>';


        DB::table('orders')->whereIn('id', $id_primer_nivel)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);
        DB::table('orders')->whereIn('id', $id_dos_nivel)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);
        DB::table('orders')->whereIn('id', $id_tres_nivel)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);

        DB::table('orders')->whereIn('id', $id_primer_nivel_amparado)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);
        DB::table('orders')->whereIn('id', $id_dos_nivel_amparado)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);
        DB::table('orders')->whereIn('id', $id_tres_nivel_amparado)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);

        $fecha_hoy = Carbon::now();

        $liquidaciones_detalles = DB::table('liquidaciones_detalles as ld')
        ->join('terceros as t', 't.id', '=', 'ld.tercero_id')
        ->join('tipos as t2', 't2.id', '=', 't.tipo_id')
        ->where('ld.liquidacion_id', $liquidacion_id)
        ->select('ld.tercero_id', DB::raw('sum(ld.valor_comision) as valor_comision'), DB::raw("(select count(*) from terceros_prime where '".$fecha_hoy."' <= fecha_final and terceros_prime.tercero_id = ld.tercero_id ) as prime"), 't2.nombre', 't2.id', 't2.comision_maxima','ld.liquidacion_id')
        ->groupBy('ld.tercero_id', 't.identificacion', 't.nombres', 't.apellidos', 't.email', 't.telefono', 't2.nombre', 't2.id', 'ld.liquidacion_id')
        ->get();

        $parametros = DB::table('parametros')->select('rete_fuente','rete_ica','prime','prime_iva','transferencia','extracto','administrativo')->where('id', 1)->first();

        foreach ($liquidaciones_detalles as $value) {
             
             $valor_comision = 0; $valor_comision_descuento =  0;

                    if($value->valor_comision > $value->comision_maxima){ 
                        if($value->comision_maxima != 0){
                            $valor_comision = $value->comision_maxima; 
                        }
                        else{
                            $valor_comision = $value->valor_comision; 
                        }
                    }
                    else{   $valor_comision = $value->valor_comision;    }
                        
                        if($value->prime >= 1){
                            $prime = $parametros->prime;
                            $prime_iva = $parametros->prime_iva;
                        }
                        else {
                            $prime = 0;
                            $prime_iva = 0;
                        }

                         $valor_comision_descuento = $valor_comision - ($valor_comision * $parametros->rete_fuente) - ($valor_comision * $parametros->rete_ica) - ($prime) - ($prime * $prime_iva)  - ($parametros->transferencia) - $parametros->extracto - $parametros->administrativo;

                           DB::table('liquidaciones_terceros')->insert([
                            'liquidacion_id' => $value->liquidacion_id,
                            'tercero_id' => $value->tercero_id,
                            'valor_comision' => $value->valor_comision,
                            'valor_comision_paga' => $valor_comision_descuento,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),

                            'rete_fuente' => $valor_comision * $parametros->rete_fuente,
                            'rete_ica' => $valor_comision * $parametros->rete_ica,
                            'prime' => $prime,
                            'prime_iva' => $prime * $prime_iva,
                            'transferencia' => $parametros->transferencia,
                            'extracto' => $parametros->extracto,
                            'administrativo' => $parametros->administrativo,
                            'virtual' => $valor_comision_descuento * 0.30,
                            'giro' => $valor_comision_descuento * 0.70,
                        ]);  
        }
    } 

            Session::flash('flash_msg', 'La liquidación se realizo correctamente');
            return redirect()->action('LiquidacionesController@get_liquidar');
    }

    public function liquidaciones_general() {
        return view('admin.liquidaciones.liquidaciones_general');
    }

    public function liquidaciones_datos() {

        $liquidaciones = DB::table('liquidaciones')
                ->select('liquidaciones.id as liqui_id','nombres', 'apellidos', DB::raw("DATE(fecha_inicio) AS fechainicio"), DB::raw("DATE(fecha_final) AS fechafinal"),'fecha_liquidacion')
                ->join('terceros', 'terceros.id', '=', 'liquidaciones.usuario_id')
                ->get();

        $send = collect($liquidaciones);

        return Datatables::of($send)
                        ->addColumn('id', function ($send) {
                            return '<div align=left>' . $send->liqui_id . '</div>';
                        })
                        ->addColumn('nombres', function ($send) {
                            return '<div align=left>' . $send->nombres.' '.$send->apellidos. '</div>';
                        })
                        ->addColumn('fecha_inicio', function ($send) {
                            return '<div align=left>' . $send->fechainicio . '</div>';
                        })
                        ->addColumn('fecha_final', function ($send) {
                            return '<div align=left>' . $send->fechafinal . '</div>';
                        })
                        ->addColumn('fecha_liquidacion', function ($send) {
                            return '<div align=left>' . $send->fecha_liquidacion . '</div>';
                        })
                        ->addColumn('excel', function ($send) {
                            return '<div align=left><a href="' . route('liquidacion.detalles_excel', $send->liqui_id) . '" target="_blank" class="btn btn-primary btn-xs">
                        Excel
                </a></div>';
                        })
                        ->make(true);
    }

    public function liquidaciones_extracto_comisiones($id=0) {
     //currentUser()->id
    	$usuario = currentUser()->id;
    	$liquidaciones = DB::table('liquidaciones')->select('fecha_liquidacion')->where('liquidaciones.id', $id)->first();
    	$liquidaciones_terceros = DB::table('liquidaciones_terceros')->select('estado_id', 'valor_comision_paga', 'rete_fuente','rete_ica','prime','prime_iva','transferencia','extracto','administrativo')->where('tercero_id', $usuario)->first();
        $parametros = DB::table('parametros')->select('rete_fuente','rete_ica','prime','prime_iva','transferencia','extracto','administrativo')->where('id', 1)->first();

        $mes = strtotime($liquidaciones->fecha_liquidacion);
        $mes = date("m", $mes);

    	$liquidaciones_detalles = $this->liquidaciones_extracto_comisiones_datos($usuario);
    	$mes = $this->nombremes($mes);

        return view('admin.liquidaciones.extracto_comisiones', compact('id','liquidaciones_detalles','mes','parametros','liquidaciones_terceros'));
        
    }

    public function nombremes($mes){        
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 
    	return $meses[$mes-1];
    } 

    public function liquidaciones_extracto_comisiones_datos($id=0) {

        $liquidaciones = DB::table('liquidaciones_detalles')
                ->select('nombres','apellidos', 'name', 'puntos', 'valor_comision','orders.created_at')
                ->where('liquidaciones_detalles.tercero_id', $id)
                ->join('terceros', 'terceros.id', '=', 'liquidaciones_detalles.hijo_id')
                ->join('orders', 'orders.id', '=', 'liquidaciones_detalles.order_id')
                ->orderByRaw('valor_comision DESC')
                ->get();

        return $liquidaciones;
/*
        $send = collect($liquidaciones);

        return Datatables::of($send)
                        ->addColumn('nombres', function ($send) {
                            return '<div align=left>' . $send->nombres . '</div>';
                        })
                        ->addColumn('apellidos', function ($send) {
                            return '<div align=left>' . $send->apellidos. '</div>';
                        })
                        ->addColumn('name', function ($send) {
                            return '<div align=left>' . $send->name . '</div>';
                        })
                        ->addColumn('puntos', function ($send) {
                            return '<div align=left>' . $send->puntos . '</div>';
                        })
                        ->addColumn('valor_comision', function ($send) {
                            return '<div align=left>' . number_format($send->valor_comision) . '</div>';
                        })
                        ->make(true);
 */
    }

    public function liquidaciones_terceros_estados($id=0) {
     //currentUser()->id 
        $parametros = DB::table('parametros')->select('rete_fuente','rete_ica','prime','prime_iva','transferencia','extracto','administrativo')->where('id', 1)->first();

        return view('admin.liquidaciones.liquidaciones_tercero_estado', compact('id','liquidaciones_detalles','mes','parametros'));
    }

    public function liquidaciones_cambiar_estado(Request $request) {
        if ($request->has('id')){ 
        	if($request->tipo == "estado"){
                DB::table('liquidaciones_terceros')->where('id', $request->id)->update(['estado_id' => $request->valor]); 
        	}
        	else{
                DB::table('liquidaciones_terceros')->where('id', $request->id)->update(['tipo_pendiente_id' => $request->valor]); 
        	}
        } 
            return response()->json(['val' => 'bien'], 200);
    }

    public function liquidaciones_terceros_estados_datos($id=0) {

        $liquidaciones = DB::table('liquidaciones_terceros')
        ->where('liquidaciones_terceros.liquidacion_id', 70)
        ->whereIn('liquidaciones_terceros.tercero_id', array(4,80))
        ->join('terceros', 'terceros.id', '=', 'liquidaciones_terceros.tercero_id')
        ->select('liquidaciones_terceros.id as liquitercero_id','tercero_id','identificacion', 'nombres', 'apellidos', 'telefono', 'email', 'valor_comision_paga','estado_id', DB::raw("(select estado from terceros_prime where terceros.id = terceros_prime.tercero_id limit 1) as prime"))
        ->orderByRaw('liquidaciones_terceros.valor_comision_paga ASC')->get();
     
        $tipos_estado_comision = DB::table('tipos')->where('padre_id', 86)->select('nombre','id')->orderByRaw('id ASC')->get();     
        $tipos_estado_pendiente = DB::table('tipos')->where('padre_id', 88)->select('nombre','id')->orderByRaw('id ASC')->get();   

        $datos = array();
        foreach ($liquidaciones as $send) {
            $select_1 = ''; $select_2 = '';
 
            		$prime = '';
                    if ($send->prime != '') {
                    	 $prime = 'Si';
                    }
                    else{
                        $prime = 'No';
                    }
            
            $select_1 = '<select class="tercero_tipo_'.$send->tercero_id.' form-control" onchange="cambio_estado('.$send->liquitercero_id.', this.options[this.selectedIndex].value,"estado")" id="'.$send->liquitercero_id.'">';
            foreach ($tipos_estado_comision as $value) {
             	$seleted = '';
             	if($send->estado_id == $value->id){  $seleted = 'selected';  }
                $select_1 .= '<option value="'.$value->id.'" '.$seleted.'>'.$value->nombre.'</option>';
            }
            $select_1 .= '</select>';

            $select_2 = '<select class="tercero_pendiente_'.$send->liquitercero_id.' pendiente form-control" onchange="cambio_estado('.$send->liquitercero_id.', this.options[this.selectedIndex].value,"pendiente")" id="'.$send->liquitercero_id.'">';
            foreach ($tipos_estado_pendiente as $value) {
             	$seleted = '';
             	if($send->estado_id == $value->id){  $seleted = 'selected';  }
                $select_2 .= '<option value="'.$value->id.'" '.$seleted.'>'.$value->nombre.'</option>';
            }
            $select_2 .= '</select>';
/*
            array_push($datos, array('identificacion' => $send->identificacion, 
                              'nombres' => $send->nombres, ));
*/
        	$datos[] = array('identificacion' => $send->identificacion, 
                              'nombres' => $send->nombres, 
                              'apellidos' => $send->apellidos, 
                              'telefono' => $send->telefono, 
                              'email' => $send->email, 
                              'valor_comision_paga' => $send->valor_comision_paga, 
                              'estado_id' => $select_1.''.$select_2, 
                              'prime' => $prime); 
        }
 
       $send = collect($datos);

        return Datatables::of($send)
                        ->addColumn('identificacion', function ($send) {
                            return '<div align=left>' . $send['identificacion'] . '</div>';
                        })
                        ->addColumn('nombres', function ($send) {
                            return '<div align=left>' . $send['nombres']. '</div>';
                        })
                        ->addColumn('apellidos', function ($send) {
                            return '<div align=left>' . $send['apellidos'] . '</div>';
                        })
                        ->addColumn('telefono', function ($send) {
                            return '<div align=left>' . $send['telefono'] . '</div>';
                        })
                        ->addColumn('email', function ($send) {
                            return '<div align=left>' . $send['email'] . '</div>';
                        })
                        ->addColumn('valor_comision_paga', function ($send) {
                            return '<div align=left>' . $send['valor_comision_paga'] . '</div>';
                        })
                        ->addColumn('prime', function ($send) { 
                            return '<div align=left>' . $send['prime']. '</div>';
                        })
                        ->addColumn('estado', function ($send) { 
                            return '<div align=left>' . $send['estado_id']. '</div>';
                        })
                        ->make(true); 
    }

    public function liquidaciones_detalles_excel($id=0) {  

    ini_set('memory_limit', '-1'); 

    $envios =  DB::table('liquidaciones_terceros')
        ->where('liquidaciones_terceros.liquidacion_id', $id)
        ->join('terceros', 'terceros.id', '=', 'liquidaciones_terceros.tercero_id')
        ->select('identificacion', 'nombres', 'apellidos', 'telefono', 'email', 'valor_comision_paga', DB::raw("(select estado from terceros_prime where terceros.id = terceros_prime.tercero_id limit 1) as prime"))
        ->orderByRaw('liquidaciones_terceros.valor_comision_paga ASC')->get();

        Excel::create('liquidaciones', function($excel) use ($envios) {
            $excel->sheet('liquidaciones', function($sheet) use ($envios)  {
            	$sheet->prependRow(1, array('Cedula', 'Nombres', 'Apellidos', 'Teléfono', 'Email', 'Valor comisión','Prime'));
            	foreach ($envios as $value) {

            		$prime = '';
                    if ($value->prime != '') {
                    	 $prime = 'Si';
                    }
                    else{
                        $prime = 'No';
                    }

                    $sheet->prependRow(2, array($value->identificacion, $value->nombres, $value->apellidos, $value->telefono, $value->email, $value->valor_comision_paga, $prime));
                }
            });
        })->export('xls');

    }

}