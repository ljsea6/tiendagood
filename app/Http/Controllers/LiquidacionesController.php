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

class LiquidacionesController extends Controller {

    
    public function get_liquidar() {

        return view('admin.liquidaciones.liquidar');
    }

    public function post_liquidar() {
       ini_set("memory_limit", "-1");
        

            $liquidar = new Liquidaciones();
            $liquidar->usuario_id = currentUser()->id;
            $liquidar->fecha_inicio = Carbon::now();
            $liquidar->fecha_final = Carbon::now();
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




//->where('t.id', 41)
//->limit(41)
        $vendedores = DB::table('terceros as t')->where('t.tipo_cliente_id', 83)->where('t.state', true)->select('t.id', 't.tipo_id')->where('t.id', 522)->orderByRaw('id ASC')->get();
                    

        foreach ($vendedores as $value_vendedor) { 

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
                $rules = DB::table('rules')->where('tipo_id', $value_vendedor->tipo_id)->select('id')->get();
                foreach ($rules as $rules_value) {
                        $rules_details = DB::table('rules_details')->where('rule_id', $rules_value->id)->select('nivel','comision_puntos','id')->get();
                        foreach ($rules_details as $rules_details_value) {
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
                }            
                        
                /*   ----------------------------------------------------------------------------------------------------------------------------------------  */ 
                /*                                                    reglas     fin                                                                           */
                /*   ----------------------------------------------------------------------------------------------------------------------------------------  */


/*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */ 
/*                                                     terceros y ordenes del nivel uno con sus amparados    inicio  --------------------------                  */
/*   ----------------------------------------------------------------------------------------------------------------------------------------------------------  */

        $uno = DB::table('terceros as t')
        ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
        ->join('terceros as t2', 't2.id', '=', 'tk.customer_id')
        ->leftjoin('orders', 'orders.tercero_id', '=', 't2.id')
        ->where('tk.padre_id', $value_vendedor->id)->where('t.state', true)->where('t2.state', true)->where('t2.tipo_cliente_id', '<>', 85)
        ->select('t2.id', 't2.email', 't2.nombres', 't2.apellidos', 't2.tipo_cliente_id','points', 'orders.id as orden_id',
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

                    DB::table('liquidaciones_detalles')->insert([
                    'liquidacion_id' => $liquidacion_id,
                    'tercero_id' => $value_vendedor->id,
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
        ->where('t.id', $value_vendedor->id)->where('t.state', true)->where('t3.state', true)->where('t3.tipo_cliente_id', '<>', 85)
        ->select('t3.id', 't3.email', 't3.nombres', 't3.apellidos', 't3.tipo_cliente_id','points', 'orders.id as orden_id',
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

                    DB::table('liquidaciones_detalles')->insert([
                    'liquidacion_id' => $liquidacion_id,
                    'tercero_id' => $value_vendedor->id,
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
        ->where('t.id', $value_vendedor->id)->where('t.state', true)->where('t4.state', true)->where('t4.tipo_cliente_id', '<>', 85)   
        ->select('t4.id', 't4.email', 't4.nombres', 't4.apellidos', 't4.tipo_cliente_id','points', 'orders.id as orden_id',
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

                    DB::table('liquidaciones_detalles')->insert([
                    'liquidacion_id' => $liquidacion_id,
                    'tercero_id' => $value_vendedor->id,
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

          //echo $value_vendedor->id.' - puntos: '.$points_level_1.' - comision: '.$comision_valor_1.' - puntos: '.$points_level_2.' - comision: '.$comision_valor_2.' - puntos: '.$points_level_3.' - comision: '.$comision_valor_3.'<br>';
        }

        DB::table('orders')->whereIn('id', $id_primer_nivel)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);
        DB::table('orders')->whereIn('id', $id_dos_nivel)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);
        DB::table('orders')->whereIn('id', $id_tres_nivel)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);

        DB::table('orders')->whereIn('id', $id_primer_nivel_amparado)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);
        DB::table('orders')->whereIn('id', $id_dos_nivel_amparado)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);
        DB::table('orders')->whereIn('id', $id_tres_nivel_amparado)->update(['comisionada' => Carbon::now(), 'liquidacion_id' => $liquidacion_id]);

    }

}
