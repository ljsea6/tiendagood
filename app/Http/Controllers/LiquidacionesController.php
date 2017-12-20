<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use App\Order;
use App\Entities\Network;
use App\Entities\Tercero;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class LiquidacionesController extends Controller {

    public function liquidar() {
        $level_uno = 0;
        $level_dos = 0;
        $level_tres = 0;

        $my_points = 0;

 $gente_nivel_1 = array();
 $count_add=0; 
 $vendedores_liquidados = array(); 


        $vendedores = DB::table('terceros as t')->where('t.tipo_cliente_id', 83)
                ->select('t.id')->limit(10)->orderByRaw('id ASC')->get();
                    
            foreach ($vendedores as $value_vendedor) { 
                  // $vendedores_liquidados[] = array($n->id); 

        $points_level_1 = 0;
        $points_level_2 = 0;
        $points_level_3 = 0;

 $id_primer_nivel = array(); 
 $id_dos_nivel = array(); 
 $id_tres_nivel = array(); 

        $uno = DB::table('terceros as t')->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')->leftjoin('orders', 'orders.tercero_id', '=', 't.id')
                ->where('tk.padre_id', $value_vendedor->id)->where('financial_status', 'paid')->where('t.state', true)->orWhere('t.tipo_cliente_id', 83)->orWhere('t.tipo_cliente_id', 84)
                ->select('t.id', 't.email', 't.nombres', 't.apellidos', 't.tipo_cliente_id','points')->get();
                  
        if (count($uno) > 0) {
            $level_uno = $level_uno + count($uno); 
            foreach ($uno as $n) { 
 
                $id_primer_nivel[] = array($n->id); 
             
                if( $n->points >= 1){
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */ 
                    /*                                                     ordenes del nivel uno con sus amparados   inicio     ------------------------           */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */  
                        $points_level_vendedor_1 = 0; 
                            $points_level_1 +=  $n->points; 
                            $points_level_vendedor_1 += $n->points; 

                        $uno_amparados_total = 0;
                        $uno_amparados = DB::table('terceros as t')->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')->where('tk.padre_id', $n->id)
                        ->where('t.state', true)->where('t.tipo_cliente_id', 85)->select('t.id', 't.email', 't.nombres', 't.apellidos', 't.tipo_cliente_id')->get();
                        foreach ($uno_amparados as $uno_amparados_value) { 
                            $uno_amparados_total++;
                            $uno_amparados_orders = DB::table('orders')->where('tercero_id', $uno_amparados_value->id)->where('financial_status', 'paid')->select('points')->get();
                            foreach ($uno_amparados_orders as $uno_amparados_orders_value) {
                               // $points_level_1 += $uno_amparados_orders_value->points;
                               // $points_level_vendedor_1 += $uno_amparados_orders_value->points;
                            }                            
                        }
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */ 
                    /*                                                     ordenes del nivel uno con sus amparados    fin                                          */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */  

                    $gente_nivel_1[] = array('id' => $n->id);
                }
            }
        }


                $dos = DB::table('terceros as t')->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')->join('orders', 'orders.tercero_id', '=', 't.id')
                ->whereIn('tk.padre_id', $id_primer_nivel)->where('financial_status', 'paid')->where('points', '>=','1')->where('t.state', true)->where('t.tipo_cliente_id', 83)->orWhere('t.tipo_cliente_id', 84)
                ->select('t.id', 't.email', 't.nombres', 't.apellidos', 't.tipo_cliente_id','points')->get();

                if (count($dos) > 0) {

                    $level_dos = $level_dos + count($dos);
                    $gente_nivel_2 = array();
                    $count_add=0; 
                    foreach ($dos as $d) {  $count_add++; 

                    $id_dos_nivel[] = array($d->id); 
       
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */ 
                    /*                                                     ordenes del nivel dos con sus amparados   inicio     ------------------------           */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */  
                        $points_level_vendedor_2 = 0; 
                            $points_level_2 += $d->points;
                            $points_level_vendedor_2 += $d->points; 

                        $dos_amparados_total = 0;
                        $dos_amparados = DB::table('terceros as t')->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')->where('tk.padre_id', $d->id)
                        ->where('t.state', true)->where('t.tipo_cliente_id', 85)->select('t.id', 't.email', 't.nombres', 't.apellidos', 't.tipo_cliente_id')->get();
                        foreach ($dos_amparados as $dos_amparados_value) { 
                            $dos_amparados_total++;
                            $dos_amparados_orders = DB::table('orders')->where('tercero_id', $dos_amparados_value->id)->where('financial_status', 'paid')->select('points')->get();
                            foreach ($dos_amparados_orders as $dos_amparados_orders_value) {
                                //$points_level_2 += $dos_amparados_orders_value->points;
                                //$$points_level_vendedor_2 += $dos_amparados_orders_value->points;
                            }                            
                        }
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */ 
                    /*                                                     ordenes del nivel dos con sus amparados    fin                                          */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */ 
 
                            $gente_nivel_2[] = array('nombre' => $d->id.'-'.$d->nombres.'-'.$d->apellidos.'-'.$d->email.'-'.$d->tipo_cliente_id.' amparados: '.$count_add.' puntos: '.$points_level_vendedor_2.'<br>sd');
                }
            }


                $tres = DB::table('terceros as t')->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')->join('orders', 'orders.tercero_id', '=', 't.id')
                        ->whereIn('tk.padre_id', $id_dos_nivel)->where('financial_status', 'paid')->where('points', '>=','1')->where('t.state', true)->where('t.tipo_cliente_id', 83)->orWhere('t.tipo_cliente_id', 84)
                                ->select('t.id', 't.email', 't.nombres', 't.apellidos', 't.tipo_cliente_id','points')->get();

            if (count($tres) > 0) {

                            $level_tres = $level_tres + count($tres);                            
                            $gente_nivel_3 = array();
                foreach ($tres as $t) {
       
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */ 
                    /*                                                     ordenes del nivel tres con sus amparados   inicio     ------------------------          */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */  
                        $points_level_vendedor_3 = 0; 
                            $points_level_3 += $t->points;
                            $points_level_vendedor_3 += $t->points; 

                        $tres_amparados_total = 0;
                        $tres_amparados = DB::table('terceros as t')->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')->where('tk.padre_id', $t->id)
                        ->where('t.state', true)->where('t.tipo_cliente_id', 85)->select('t.id', 't.email', 't.nombres', 't.apellidos', 't.tipo_cliente_id')->get();
                        foreach ($tres_amparados as $tres_amparados_value) { 
                            $tres_amparados_total++;
                            $tres_amparados_orders = DB::table('orders')->where('tercero_id', $tres_amparados_value->id)->where('financial_status', 'paid')->select('points')->get();
                            foreach ($tres_amparados_orders as $tres_amparados_orders_value) {
                                //$$points_level_2 += $tres_amparados_orders_value->points;
                                //$$points_level_vendedor_2 += $tres_amparados_orders_value->points;
                            }                            
                        }
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */ 
                    /*                                                     ordenes del nivel tres con sus amparados    fin                                         */
                    /*   ----------------------------------------------------------------------------------------------------------------------------------------  */  

                            $gente_nivel_3[] = array('nombre' => $t->id.'-'.$t->nombres.'-'.$t->apellidos.'-'.$t->email.'-'.$t->tipo_cliente_id.' amparados: '.$tres_amparados_total.' puntos: '.$points_level_vendedor_3.'<br>');
                }
            }

            echo $value_vendedor->id.' '.$points_level_1.' '.$points_level_2.' '.$points_level_3.'<br>'; 

    }

foreach ($gente_nivel_1 as $value) {
   //echo $value['nombre'].'<br>';
} 


exit();
        $send = Tercero::with('cliente', 'levels', 'networks', 'primes', 'tipo')->find(currentUser()->id);
        $patrocinador = '';  $nombre_completo = '';  $email = '';   $telefono = '';   $tipo_nombre = '';
        if (count($send->networks) > 0) {
           $patrocinador = Tercero::with('cliente', 'levels')->find($send->networks['0']['pivot']['padre_id']);
           $nombre_completo = $patrocinador['nombres'].' '.$patrocinador['apellidos']; 
           $email = $patrocinador['email'];
           $telefono = $patrocinador['telefono'];
        }          
 
        $fecha_inicio =  '';   $fecha_final = ''; 

        foreach ($send->primes as $value) {
            if($value->estado == true){
                $fecha_inicio = $value->fecha_inicio;
                $fecha_final = $value->fecha_final;
            }
        }

        $fecha_inicio = $fecha_inicio;  $fecha_inicio = strtotime($fecha_inicio);  $fecha_inicio =  date("Y-m-d", $fecha_inicio);  
        $fecha_final = $fecha_final;    $fecha_final = strtotime($fecha_final);    $fecha_final =  date("Y-m-d", $fecha_final); 
 
        return view('admin.liquidaciones.index')->with(['send' => $send, 'uno' => $level_uno, 'dos' => $level_dos, 'tres' => $level_tres,
                    'points_level_1' => $points_level_1, 'points_level_2' => $points_level_2, 'points_level_3' => $points_level_3,
                     'my_points' => $my_points, 'nombre_completo' => $nombre_completo, 'email' => $email, 'telefono' => $telefono,
                    'fecha_inicio' => $fecha_inicio, 'fecha_final' => $fecha_final, 'tipo_nombre' => $send->tipo['nombre']]);
    }

    public function send(Request $request) {
        $data = $request->all();

        if (isset($data['email']) && $data['email'] !== '') {

            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email);
            });
        }

        if (isset($data['emailone']) && $data['emailone'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->emailone);
            });
        }

        if (isset($data['emailtwo']) && $data['emailtwo'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->emailtwo);
            });
        }

        if (isset($data['email1']) && $data['email1'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email1);
            });
        }

        if (isset($data['email2']) && $data['email2'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email2);
            });
        }

        if (isset($data['email3']) && $data['email3'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email3);
            });
        }

        if (isset($data['email4']) && $data['email4'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email4);
            });
        }

        if (isset($data['email5']) && $data['email5'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email2);
            });
        }

        if (isset($data['email6']) && $data['email6'] !== '') {
            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email3);
            });
        }

        if (isset($data['email7']) && $data['email7'] !== '') {

            Mail::send('admin.send.message', $data, function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email4);
            });
        }

        return view('admin.send.success');
    }

    public function buscar(Request $request) {
        if ($request->has('email')) {

            $tercero = Tercero::with('networks')->where('email', strtolower($request['email']))->get();

            return view('admin.search.index', compact('tercero'));
        }
    }

    public function search() {
        return view('admin.search');
    }

    public function finder(Request $request) {
        if ($request->has('email') && $request->has('id')) {

            $campo = trim(str_replace(" ", "%", $request['email']));

            $results = Tercero::with('networks')
                    ->where('email', '=', "" . strtolower($campo) . "")
                    ->orWhere(DB::raw("(nombres)"), 'like', "%" . strtolower($campo) . "%")
                    ->orWhere(DB::raw("(apellidos)"), 'like', "%" . strtolower($campo) . "%")
                    //->orWhere(DB::raw("nombres like '%".strtolower($campo)."%' "))
                    ->where('state', true)
                    ->first();

            $level = '';

            if (count($results) > 0 && $results->state == true) {
                if (count($results->networks) > 0) {
                    $uno = $results->networks[0]->pivot->padre_id;
                    if ($uno == $request->id) {
                        $level = 1;
                        // return view('admin.find')->with(['results' => $results, 'level' => $level]);
                        $data[] = array('label' => 'Nombre: ' . $results['nombres'] . ' ' . $results['apellidos'] . ' Correo: ' . $results['email'] . ' Nivel:' . $level,
                            'nombre' => $results['nombres'] . ' ' . $results['apellidos'], 'correo' => $results['email'], 'nivel' => $level);
                        echo json_encode($data);
                    } else {
                        $results_dos = Tercero::with('networks')->find($uno);
                        if (count($results_dos) > 0 && $results_dos->state == true) {
                            if (count($results_dos->networks) > 0) {
                                $dos = $results_dos->networks[0]->pivot->padre_id;
                                if ($dos == $request->id) {
                                    $level = 2;
                                    // return view('admin.find')->with(['results' => $results, 'level' => $level]);
                                    $data[] = array('label' => 'Nombre: ' . $results['nombres'] . ' ' . $results['apellidos'] . ' Correo: ' . $results['email'] . ' Nivel:' . $level,
                                        'nombre' => $results['nombres'] . ' ' . $results['apellidos'], 'correo' => $results['email'], 'nivel' => $level);
                                    echo json_encode($data);
                                } else {
                                    $results_tres = Tercero::with('networks')->find($dos);
                                    if (count($results_tres) > 0 && $results_tres->state == true) {
                                        if (count($results_tres->networks) > 0) {
                                            $tres = $results_tres->networks[0]->pivot->padre_id;
                                            if ($tres == $request->id) {
                                                $level = 3;
                                                // return view('admin.find')->with(['results' => $results, 'level' => $level]);
                                                $data[] = array('label' => 'Nombre: ' . $results['nombres'] . ' ' . $results['apellidos'] . ' Correo: ' . $results['email'] . ' Nivel:' . $level,
                                                    'nombre' => $results['nombres'] . ' ' . $results['apellidos'], 'correo' => $results['email'], 'nivel' => $level);
                                                echo json_encode($data);
                                            } else {
                                                $err = 'No está en su lista de referidos';
                                                //return view('admin.find', compact('err'));
                                                $data[] = array('label' => $err, 'nivel' => 0);
                                                echo json_encode($data);
                                            }
                                        }
                                    } else {
                                        $err = 'No está en su lista de referidos';
                                        //return view('admin.find', compact('err'));
                                        $data[] = array('label' => $err, 'nivel' => 0);
                                        echo json_encode($data);
                                    }
                                }
                            }
                        } else {
                            $err = 'No está en su lista de referidos';
                            //return view('admin.find', compact('err'));
                            $data[] = array('label' => $err, 'nivel' => 0);
                            echo json_encode($data);
                        }
                    }
                } else {
                    $err = 'No está en su lista de referidos';
                    //return view('admin.find', compact('err'));
                    $data[] = array('label' => $err, 'nivel' => 0);
                    echo json_encode($data);
                }
            } else {
                $err = 'No está en su lista de referidos';
                //return view('admin.find', compact('err'));
                $data[] = array('label' => $err, 'nivel' => 0);
                echo json_encode($data);
            }

            /* if(currentUser()->tipo_id == 2) {
              $results = Tercero::where('email', 'like', '%' .strtolower($request['email']) . '%')->get();

              return view('admin.find', compact('results'));
              } */

            /* if (currentUser()->tipo_id != 2 && currentUser()->tipo_id != 1) {

              $results = Tercero::where('email', '=', $request['email'])->get();

              return view('admin.find', compact('results'));
              }

              if (currentUser()->tipo_id == 1) {

              $results  = Tercero::where('email', '=', $request['email'])->first();

              if (count($results) > 0) {
              $find = DB::table('terceros_networks')->where('customer_id', $results['id'])->first();

              if (count($find) > 0 && $find->padre_id == currentUser()->id) {

              return view('admin.find', compact('results'));

              } else {

              $err = 'No está en su lista de referidos';
              return view('admin.find', compact('err'));
              }

              } else {
              $err = 'No está en su lista de referidos';
              return view('admin.find', compact('err'));
              }

              } */
        }
    }

    public function network() {
        $tercero = Tercero::find(currentUser()->id);
        $referidos = DB::table('terceros')
                ->where('apellidos', $tercero['email'])
                ->select('id', 'nombres', 'email')
                ->get();

        $orders = Order::where('email', $tercero['email'])->get();
        $total = 0;

        foreach ($referidos as $referido) {
            $results = Order::where('email', $referido->email)->get();
            if (count($results) > 0) {
                foreach ($results as $result) {
                    $total = $total + (double) $result['total_price'];
                }
            }
        }

        $totalPrice = number_format($total, 0);
        $networks = Network::all();
        $terceros = [];

        foreach ($networks as $network) {
            $results = DB::table('terceros')
                            ->join('networks', 'terceros.network_id', '=', 'networks.id')
                            ->where('terceros.apellidos', $tercero['email'])
                            ->where('networks.id', $network['id'])
                            ->select('terceros.id', 'terceros.nombres', 'terceros.apellidos', 'terceros.email', 'terceros.network_id')
                            ->take(10)->get();
            foreach ($results as $result) {
                array_push($terceros, (array) $result);
            }
        }

        $send = [
            'referidos' => number_format(count($referidos)),
            'orders' => number_format(count($orders)),
            'total' => $totalPrice,
            'terceros' => collect($terceros),
            'tercero' => $tercero,
            'redes' => $networks
        ];

        return view('admin.network', compact('send'));
    }

    public function index() {


    }

    public function carga() {
        $level_uno = 0;
        $level_dos = 0;
        $level_tres = 0;
        $uno = DB::table('terceros as t')
                ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                ->where('tk.padre_id', 41)
                ->where('t.state', true)
                ->select('t.id')
                ->get();

        $results = array();

        if (count($uno) > 0) {

            $level_uno = $level_uno + count($uno);

            foreach ($uno as $n) {

                $dos = DB::table('terceros as t')
                        ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                        ->where('tk.padre_id', $n->id)
                        ->where('t.state', true)
                        ->select('t.id')
                        ->get();

                if (count($dos) > 0) {

                    $level_dos = $level_dos + count($dos);

                    foreach ($dos as $d) {

                        $tres = DB::table('terceros as t')
                                ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                                ->where('tk.padre_id', $d->id)
                                ->where('t.state', true)
                                ->select('t.id', 't.nombres', 't.email')
                                ->get();

                        if (count($tres) > 0) {

                            $level_tres = $level_tres + count($tres);

                            foreach ($tres as $t) {

                                array_push($results, $t);
                            }
                        }
                    }
                }
            }
        }

        $temporal = DB::table('trafico')->increment('visitas');

        //$send = Tercero::find(currentUser()->id);
        $send = 1;
        dd("Prueba de carga");
        //return view('admin.index')->with(['send' => $send, 'uno' => $level_uno, 'dos' => $level_dos, 'tres' => $level_tres]);
    }

    public function anyData(Request $request) {
        $tercero = Tercero::find((int) $request['id']);
        $results = DB::table('terceros')
                ->join('networks', 'terceros.network_id', '=', 'networks.id')
                ->where('terceros.apellidos', $tercero['email'])
                ->select('terceros.id', 'terceros.nombres', 'terceros.email', 'networks.name')
                ->get();
        $send = collect($results);
        return Datatables::of($send)
                        ->addColumn('id', function ($send) {
                            return '<div align=left>' . $send->id . '</div>';
                        })
                        ->addColumn('nombres', function ($send) {
                            return '<div align=left>' . ucwords($send->nombres) . '</div>';
                        })
                        ->addColumn('email', function ($send) {
                            return '<div align=left>' . $send->email . '</div>';
                        })
                        ->addColumn('name', function ($send) {
                            return '<div align=left>' . $send->name . '</div>';
                        })
                        ->make(true);
    }

    public function nivel($nivel = 1) {
        return view('admin.nivel.nivel', compact('nivel'));
    }

    public function level_one(Request $request) {

        if ($request->has('level') && $request->has('id')) {

            if ($request->level == 1) {

                $total_referidos = DB::raw('(SELECT COUNT(1) FROM terceros_networks tn WHERE tn.padre_id = t.id) as referidos');

                $results = DB::table('terceros as t')
                        ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                        ->where('tk.padre_id', $request->id)
                        ->where('t.state', true)
                        ->select('t.id', 't.nombres', 't.apellidos', 't.mispuntos', $total_referidos)
                        ->get();


                $send = collect($results);

                return Datatables::of($send)
                                ->addColumn('id', function ($send) {
                                    return '<div align=left>' . $send->id . '</div>';
                                })
                                ->addColumn('nombres', function ($send) {
                                    return '<div align=left>' . ucwords($send->nombres) . '</div>';
                                })
                                ->addColumn('apellidos', function ($send) {
                                    return '<div align=left>' . ucwords($send->apellidos) . '</div>';
                                })
                                ->addColumn('puntos', function ($send) {
                                    return '<div align=left>' . number_format($send->mispuntos) . '</div>';
                                })
                                ->addColumn('referidos', function ($send) {
                                    return '<div align=left>' . number_format($send->referidos) . '</div>';
                                })
                                ->make(true);
            }
        }
    }

    public function level_two(Request $request) {
        if ($request->has('level') && $request->has('id')) {

            if ($request->level == 2) {
                $total_referidos = DB::raw('(SELECT COUNT(1) FROM terceros_networks tn WHERE tn.padre_id = t.id) as referidos');
                $uno = DB::table('terceros as t')
                        ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                        ->where('tk.padre_id', $request->id)
                        ->where('t.state', true)
                        ->select('t.id')
                        ->get();

                $results = array();

                if (count($uno) > 0) {

                    foreach ($uno as $n) {

                        $dos = DB::table('terceros as t')
                                ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                                ->where('tk.padre_id', $n->id)
                                ->where('t.state', true)
                                ->select('t.id', 't.nombres', 't.apellidos', 't.mispuntos', $total_referidos)
                                ->get();

                        if (count($dos) > 0) {

                            foreach ($dos as $d) {
                                array_push($results, $d);
                            }
                        }
                    }
                }

                $send = collect($results);

                return Datatables::of($send)
                                ->addColumn('id', function ($send) {
                                    return '<div align=left>' . $send->id . '</div>';
                                })
                                ->addColumn('nombres', function ($send) {
                                    return '<div align=left>' . ucwords($send->nombres) . '</div>';
                                })
                                ->addColumn('apellidos', function ($send) {
                                    return '<div align=left>' . ucwords($send->apellidos) . '</div>';
                                })
                                ->addColumn('puntos', function ($send) {
                                    return '<div align=left>' . number_format($send->mispuntos) . '</div>';
                                })
                                ->addColumn('referidos', function ($send) {
                                    return '<div align=left>' . number_format($send->referidos) . '</div>';
                                })
                                ->make(true);
            }
        }
    }

    public function level_tree(Request $request) {
        if ($request->has('level') && $request->has('id')) {

            if ($request->level == 3) {
                
                $total_referidos = DB::raw('(SELECT COUNT(1) FROM terceros_networks tn WHERE tn.padre_id = t.id) as referidos');
                
                $uno = DB::table('terceros as t')
                        ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                        ->where('tk.padre_id', $request->id)
                        ->where('t.state', true)
                        ->select('t.id')
                        ->get();

                $results = array();

                if (count($uno) > 0) {

                    foreach ($uno as $n) {

                        $dos = DB::table('terceros as t')
                                ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                                ->where('tk.padre_id', $n->id)
                                ->where('t.state', true)
                                ->select('t.id')
                                ->get();

                        if (count($dos) > 0) {

                            foreach ($dos as $d) {

                                $tres = DB::table('terceros as t')
                                        ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                                        ->where('tk.padre_id', $d->id)
                                        ->where('t.state', true)
                                        ->select('t.id', 't.nombres', 't.apellidos', 't.mispuntos', $total_referidos)
                                        ->get();

                                if (count($tres) > 0) {

                                    foreach ($tres as $t) {

                                        array_push($results, $t);
                                    }
                                }
                            }
                        }
                    }
                }

                $send = collect($results);

                return Datatables::of($send)
                                ->addColumn('id', function ($send) {
                                    return '<div align=left>' . $send->id . '</div>';
                                })
                                ->addColumn('nombres', function ($send) {
                                    return '<div align=left>' . ucwords($send->nombres) . '</div>';
                                })
                                ->addColumn('apellidos', function ($send) {
                                    return '<div align=left>' . ucwords($send->apellidos) . '</div>';
                                })
                                ->addColumn('puntos', function ($send) {
                                    return '<div align=left>' . number_format($send->mispuntos) . '</div>';
                                })
                                ->addColumn('referidos', function ($send) {
                                    return '<div align=left>' . number_format($send->referidos) . '</div>';
                                })
                                ->make(true);
            }
        }
    }

    public function indexproveedores() {
        return view('admin.proveedores.index');
    }

}
