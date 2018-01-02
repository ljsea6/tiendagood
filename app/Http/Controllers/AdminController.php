<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use DB;
use Mail;
use App\Order;
use App\Liquidacion;
use App\Helpers\Good;
use App\Helpers\Mercando;
use App\Entities\Network;
use App\Entities\Tercero;
use App\Helpers\GiftCard;
use App\Helpers\GuzzleHttp;
use App\LiquidacionDetalle;
use App\LiquidacionTercero;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class AdminController extends Controller {

    public function email()
    {
        return view('admin.send.mail');
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

        $level_uno = 0;
        $level_dos = 0;
        $level_tres = 0;

        $points_level_1 = 0;
        $points_level_2 = 0;
        $points_level_3 = 0;
        $my_points = 0;

        $uno = DB::table('terceros as t')
                ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                ->where('tk.padre_id', currentUser()->id)
                ->where('t.tipo_cliente_id', '!=', 85)
                ->where('t.state', true)
                ->select('t.id', 't.email')
                ->get();

        $results = array();

        $puntos_propios = DB::table('orders')
                ->whereRaw("tercero_id = " . currentUser()->id . " AND financial_status = 'paid' AND cancelled_at IS NULL AND comisionada IS NULL AND liquidacion_id IS NULL")
                ->selectRaw('COALESCE(SUM(points),0) as puntos')
                ->value('puntos');

        $puntos_amparados = DB::table('orders')
                ->join('terceros_networks as tn', 'orders.tercero_id', '=', 'tn.customer_id')
                ->join('terceros as t', 'orders.tercero_id', '=', 't.id')
                ->whereRaw("tn.padre_id = " . currentUser()->id . " AND t.tipo_cliente_id = 85 AND financial_status = 'paid' AND cancelled_at IS NULL AND comisionada IS NULL AND liquidacion_id IS NULL")
                ->selectRaw('COALESCE(SUM(points),0) as puntos')
                ->value('puntos');


        $my_points += $puntos_propios + $puntos_amparados;

        if (count($uno) > 0) {

            $level_uno = $level_uno + count($uno);

            foreach ($uno as $n) {

                $puntos_propios1 = DB::table('orders')
                        ->whereRaw("tercero_id = " . $n->id . " AND financial_status = 'paid' AND cancelled_at IS NULL AND comisionada IS NULL AND liquidacion_id IS NULL")
                        ->selectRaw('COALESCE(SUM(points),0) as puntos')
                        ->value('puntos');

                $puntos_amparados1 = DB::table('orders')
                        ->join('terceros_networks as tn', 'orders.tercero_id', '=', 'tn.customer_id')
                        ->join('terceros as t', 'orders.tercero_id', '=', 't.id')
                        ->whereRaw("tn.padre_id = " . $n->id . " AND t.tipo_cliente_id = 85 AND financial_status = 'paid' AND cancelled_at IS NULL AND comisionada IS NULL AND liquidacion_id IS NULL")
                        ->selectRaw('COALESCE(SUM(points),0) as puntos')
                        ->value('puntos');

                $points_level_1 += $puntos_propios1 + $puntos_amparados1;

                $dos = DB::table('terceros as t')
                        ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                        ->where('tk.padre_id', $n->id)
                        ->where('t.tipo_cliente_id', '!=', 85)
                        ->where('t.state', true)
                        ->select('t.id', 't.email')
                        ->get();

                if (count($dos) > 0) {

                    $level_dos = $level_dos + count($dos);

                    foreach ($dos as $d) {

                        $puntos_propios2 = DB::table('orders')
                                ->whereRaw("tercero_id = " . $d->id . " AND financial_status = 'paid' AND cancelled_at IS NULL AND comisionada IS NULL AND liquidacion_id IS NULL")
                                ->selectRaw('COALESCE(SUM(points),0) as puntos')
                                ->value('puntos');

                        $puntos_amparados2 = DB::table('orders')
                                ->join('terceros_networks as tn', 'orders.tercero_id', '=', 'tn.customer_id')
                                ->join('terceros as t', 'orders.tercero_id', '=', 't.id')
                                ->whereRaw("tn.padre_id = " . $d->id . " AND t.tipo_cliente_id = 85 AND financial_status = 'paid' AND cancelled_at IS NULL AND comisionada IS NULL AND liquidacion_id IS NULL")
                                ->selectRaw('COALESCE(SUM(points),0) as puntos')
                                ->value('puntos');

                        $points_level_2 += $puntos_propios2 + $puntos_amparados2;

                        $tres = DB::table('terceros as t')
                                ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                                ->where('tk.padre_id', $d->id)
                                ->where('t.tipo_cliente_id', '!=', 85)
                                ->where('t.state', true)
                                ->select('t.id', 't.email')
                                ->get();

                        if (count($tres) > 0) {

                            $level_tres = $level_tres + count($tres);

                            foreach ($tres as $t) {

                                $puntos_propios3 = DB::table('orders')
                                        ->whereRaw("tercero_id = " . $t->id . " AND financial_status = 'paid' AND cancelled_at IS NULL AND comisionada IS NULL AND liquidacion_id IS NULL")
                                        ->selectRaw('COALESCE(SUM(points),0) as puntos')
                                        ->value('puntos');

                                $puntos_amparados3 = DB::table('orders')
                                        ->join('terceros_networks as tn', 'orders.tercero_id', '=', 'tn.customer_id')
                                        ->join('terceros as t', 'orders.tercero_id', '=', 't.id')
                                        ->whereRaw("tn.padre_id = " . $t->id . " AND t.tipo_cliente_id = 85 AND financial_status = 'paid' AND cancelled_at IS NULL AND comisionada IS NULL AND liquidacion_id IS NULL")
                                        ->selectRaw('COALESCE(SUM(points),0) as puntos')
                                        ->value('puntos');

                                $points_level_3 += $puntos_propios3 + $puntos_amparados3;

                                array_push($results, $t);
                            }
                        }
                    }
                }
            }
        }

        $send = Tercero::with('cliente', 'levels', 'networks', 'primes', 'tipo')->find(currentUser()->id);
        $patrocinador = '';
        $nombre_completo = '';
        $email = '';
        $telefono = '';
        $tipo_nombre = '';
        if (count($send->networks) > 0) {
            $patrocinador = Tercero::with('cliente', 'levels')->find($send->networks['0']['pivot']['padre_id']);
            $nombre_completo = $patrocinador['nombres'] . ' ' . $patrocinador['apellidos'];
            $email = $patrocinador['email'];
            $telefono = $patrocinador['telefono'];
        }

        $fecha_inicio = '';
        $fecha_final = '';

        foreach ($send->primes as $value) {
            if ($value->estado == true) {
                $fecha_inicio = $value->fecha_inicio;
                $fecha_final = $value->fecha_final;
            }
        }

        $fecha_inicio = $fecha_inicio;
        $fecha_inicio = strtotime($fecha_inicio);
        $fecha_inicio = date("Y-m-d", $fecha_inicio);
        $fecha_final = $fecha_final;
        $fecha_final = strtotime($fecha_final);
        $fecha_final = date("Y-m-d", $fecha_final);

        return view('admin.index')->with(['send' => $send, 'uno' => $level_uno, 'dos' => $level_dos, 'tres' => $level_tres,
                    'points_level_1' => $points_level_1, 'points_level_2' => $points_level_2, 'points_level_3' => $points_level_3,
                    'my_points' => $my_points, 'nombre_completo' => $nombre_completo, 'email' => $email, 'telefono' => $telefono,
                    'fecha_inicio' => $fecha_inicio, 'fecha_final' => $fecha_final, 'tipo_nombre' => $send->tipo['nombre']]);
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

                $puntos = DB::raw("(SELECT COALESCE(SUM(o.points),0) FROM orders o WHERE o.tercero_id = t.id AND o.financial_status = 'paid' AND o.cancelled_at IS NULL AND o.comisionada IS NULL AND o.liquidacion_id IS NULL) + 
                (SELECT COALESCE(SUM(oa.points), 0) FROM orders oa
		JOIN terceros_networks tn ON oa.tercero_id = tn.customer_id
		JOIN terceros t1 ON oa.tercero_id = t1.id AND t1.tipo_cliente_id = 85 AND tn.padre_id = t.id
		WHERE oa.financial_status = 'paid' AND oa.cancelled_at IS NULL AND oa.comisionada IS NULL AND oa.liquidacion_id IS NULL) AS puntos");

                $results = DB::table('terceros as t')
                        ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                        ->where('tk.padre_id', $request->id)
                        ->where('t.state', true)
                        ->where('t.tipo_cliente_id', '!=', 85)
                        ->select('t.id', 't.nombres', 't.apellidos', $puntos, $total_referidos)
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
                                    return '<div align=left>' . number_format($send->puntos) . '</div>';
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

                $puntos = DB::raw("(SELECT COALESCE(SUM(o.points),0) FROM orders o WHERE o.tercero_id = t.id AND o.financial_status = 'paid' AND o.cancelled_at IS NULL AND o.comisionada IS NULL AND o.liquidacion_id IS NULL) + 
                (SELECT COALESCE(SUM(oa.points), 0) FROM orders oa
		JOIN terceros_networks tn ON oa.tercero_id = tn.customer_id
		JOIN terceros t1 ON oa.tercero_id = t1.id AND t1.tipo_cliente_id = 85 AND tn.padre_id = t.id
		WHERE oa.financial_status = 'paid' AND oa.cancelled_at IS NULL AND oa.comisionada IS NULL AND oa.liquidacion_id IS NULL) AS puntos");

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
                                ->select('t.id', 't.nombres', 't.apellidos', $puntos, $total_referidos)
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
                                    return '<div align=left>' . number_format($send->puntos) . '</div>';
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

                $puntos = DB::raw("(SELECT COALESCE(SUM(o.points),0) FROM orders o WHERE o.tercero_id = t.id AND o.financial_status = 'paid' AND o.cancelled_at IS NULL AND o.comisionada IS NULL AND o.liquidacion_id IS NULL) + 
                (SELECT COALESCE(SUM(oa.points), 0) FROM orders oa
		JOIN terceros_networks tn ON oa.tercero_id = tn.customer_id
		JOIN terceros t1 ON oa.tercero_id = t1.id AND t1.tipo_cliente_id = 85 AND tn.padre_id = t.id
		WHERE oa.financial_status = 'paid' AND oa.cancelled_at IS NULL AND oa.comisionada IS NULL AND oa.liquidacion_id IS NULL) AS puntos");

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
                                        ->select('t.id', 't.nombres', 't.apellidos', $puntos, $total_referidos)
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
                                    return '<div align=left>' . number_format($send->puntos) . '</div>';
                                })
                                ->addColumn('referidos', function ($send) {
                                    return '<div align=left>' . number_format($send->puntos) . '</div>';
                                })
                                ->make(true);
            }
        }
    }

    public function indexproveedores()
    {
        return view('admin.proveedores.index');
    }

    public function liquidaciones()
    {
        
        $usuario = currentUser()->id; 

         $prime = DB::table('terceros_prime as tp')->join('terceros as t', 'tp.tercero_id', '=', 't.id')->where('tp.tercero_id',  $usuario)
                    ->where('estado', true)->orderBy('tp.id', 'desc')->first();
            $prime_val='no';
                if (count($prime) > 0) {

                    $now = Carbon::now();
                    $old = Carbon::parse($prime->fecha_final);

                    if ($now <= $old) {
                        $prime_val='si';
                    }
                } 

        return view('admin.liquidaciones.index', compact('prime_val'));
    }

    public function data_liquidaciones()
    {
        $id = currentUser()->id;
  //currentUser()->id
        //$liquidaciones = Tercero::with('liquidacion_tercero')->find($id);
        $liquidaciones = DB::table('liquidaciones_terceros')
                                        ->join('tipos', 'liquidaciones_terceros.estado_id', '=', 'tipos.id')
                                        ->join('liquidaciones', 'liquidaciones.id', '=', 'liquidaciones_terceros.liquidacion_id')
                                        ->where('liquidaciones_terceros.tercero_id', $id) 
                                        ->select(DB::raw("liquidaciones_terceros.*, tipos.nombre as tipo_nombre,  tipos.id as tipo_id, liquidaciones.*, 
                                            (select nombre from tipos as t where t.id = tipo_pendiente_id) as  motivo"))
                                        ->get();

        $send = collect($liquidaciones);

        return Datatables::of($send)

            ->addColumn('date', function ($send) {
                
                return '<div align=center>' . Carbon::parse($send->fecha_inicio)->format('d/m/Y') . ' - ' . Carbon::parse($send->fecha_final)->format('d/m/Y')  . '</div>';
            })
            ->addColumn('nombres', function ($send) {
                $t = Tercero::find($send->tercero_id);
                return '<div align=center>' . ucwords($t->nombres) . ' ' . ucwords($t->apellidos) . ' </div>';
            })
            ->addColumn('consignacion', function ($send) {
                return '<div align=center>' . number_format((float)$send->giro) . '</div>';
            })
            ->addColumn('bono', function ($send) {              

              $ok = LiquidacionTercero::where('liquidacion_id', $send->liquidacion_id)->where('tercero_id', $send->tercero_id)->where('bono_good', null)->where('bono_mercando', null)->where('giftcard_good', null)->where('giftcard_mercando', null)->first();
                if (count($ok) == 0) {
                    $boton = '<div align=center><a href="' . route('admin.liquidaciones.edit', $send->id) . '"  class="btn btn-warning btn-xs">Crear Bonos</a></div>';
                } else {
                    $boton =  '<div align=center>Sus bonos ya fueron generados</div>';
                }

                return '<div align=center>' . number_format((float)$send->virtual) . ' <br><br> '.($boton).' </div>';
            })
            ->addColumn('total', function ($send) {
                return '<div align=center>' . number_format((float)$send->valor_comision) . '</div>';
            })
            ->addColumn('total_paga', function ($send) {
              if($send->valor_comision_paga >= 1){           
                return '<div align=center>' . number_format((float)$send->valor_comision_paga) . '</div>';
               }
               else{
                   return '<div align=center>0</div>';
                } 
            })
            ->addColumn('rete_fuente', function ($send) {   
                return '<div align=left><b>Retefuente:</b> ' . number_format((float)$send->rete_fuente) . '<br>'.
                    '<div align=left><b>Rete ICA:</b> ' . number_format((float)$send->rete_ica) . '<br>'. 
                    '<div align=left><b>Prime: </b>' . number_format((float)$send->prime) . '<br>'.   
                    '<div align=left><b>IVA Prime:</b> ' . number_format((float)$send->prime_iva) . '<br>'. 
                    '<div align=left><b>Transferencia: </b>' . number_format((float)$send->transferencia) . '<br>'.   
                    '<div align=left><b>Extractos:</b> ' . number_format((float)$send->extracto) . '<br>'.    
                    '<div align=left><b>Administrativos: </b>' . number_format((float)$send->administrativo) . 
                '<br></div>';
            })

            ->addColumn('estado', function ($send) {      
              if($send->tipo_nombre != 'Pendiente'){          
                return '<div align=center>' . $send->tipo_nombre . '</div>';
              }
              else{
                return '<div align=center>' . $send->tipo_nombre . ' <br><br> <b>Motivo: </b> ' . $send->motivo . ' </div>';
              }
            })
            ->addColumn('edit', function ($send) {
                $ok = LiquidacionTercero::where('id', $send->id)->where('bono_good', null)->where('bono_mercando', null)->where('giftcard_good', null)->where('giftcard_mercando', null)->first();
                if (count($ok) > 0) {
                    return '<div align=center><a href="' . route('admin.liquidaciones.edit', $send->id) . '"  class="btn btn-warning btn-xs">
                        Crear Bonos
                </a></div>';
                } else {
                    return '<div align=center>Sus bonos ya fueron generados</div>';
                }

            })
            ->addColumn('extractos', function ($send) {

                $liquidacion = Liquidacion::find($send->liquidacion_id);

                $prime = DB::table('terceros_prime as tp')
                    ->join('terceros as t', 'tp.tercero_id', '=', 't.id')
                    ->where('tp.tercero_id',  $send->tercero_id)
                    ->where('estado', true)
                    ->orderBy('tp.id', 'desc')
                    ->first();

                if (count($prime) > 0) {

                    $now = Carbon::now();
                    $old = Carbon::parse($prime->fecha_final);

                    if ($now <= $old) {

                        return '<div align=center><a href="' . route('liquidacion.liquidaciones_extracto_comisiones', $send->liquidacion_id) . '"  class="btn btn-warning btn-xs">
                        Extracto
                </a></div>';

                    }

                    return '<div align=center>No es prime</div>';

                }

                return '<div align=center>No es prime</div>';
            })
            ->make(true);
    }

    public function editar_liquidaciones($id)
    {
        $liquidacion_tercero = LiquidacionTercero::find($id);


        return view('admin.liquidaciones.edit')->with(['total' => $liquidacion_tercero->valor_comision_paga, 'id' => $liquidacion_tercero->id, 'consignacion' => ($liquidacion_tercero->valor_comision_paga), 'bono' => ($liquidacion_tercero->valor_comision_paga), 'fecha' => Carbon::parse($liquidacion_tercero->fecha_liquidacion)->diffForHumans()]);

    }

    public function gift_card(Request $request)
    {

        if ($request->has('good') && $request->has('mercando') && $request->has('bono') && $request->has('liquidacion')) {

            $liquidacion = $request->liquidacion;
            $good = (float)$request->good;
            $mercando = (float)$request->mercando;
            $bono = (float)$request->bono;

            $suma = (float)($good + $mercando);

            if ($suma > $bono) {

                return redirect()->back()->withErrors(['errors' => '¡Por favor, verifique que la suma de los bonos para Tienda Good y Mercando no superen el total del Bono!']);

            } else {

                $tercero = Tercero::where('id', currentUser()->id)
                    ->where('state', true)
                    ->first();

                $url_good = 'https://'. env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');

                $url_mercando = 'https://'. env('API_KEY_MERCANDO') . ':' . env('API_PASSWORD_MERCANDO') . '@' . env('API_SHOP_MERCANDO');

                $url_hello = 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';  // api hello
                $id_m = 276171980843;
                $id_g = 239272853541;
                $id_h = 5960597121;

                $client = GuzzleHttp::client();
                $send = [
                    'form_params' => [
                        'gift_card' => [
                            "note" => "This is a note",
                            "initial_value" => 1000,
                            "template_suffix" => "gift_cards.birthday.liquid",
                            "currency" => "COP",
                            "customer_id" => $id_h,
                            "expires_on" => Carbon::now()->addMonth()
                        ]
                    ]
                ];

                try {

                    $response = $client->request('post', $url_hello . '/admin/gift_cards.json', $send);

                    $headers = $response->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                    $x = explode('/', $headers[0]);
                    $diferencia = $x[1] - $x[0];

                    if ($diferencia < 10) {
                        usleep(500000);

                    }

                    $result = json_decode($response->getBody(), true);

                    return $result['gift_card'];

                } catch (ClientException $e) {

                    if ($e->hasResponse()) {

                        return $e->getResponse()->getBody();

                    }
                }

                if (count($tercero) > 0) {

                    $liquidacion_tercero = LiquidacionTercero::where('id', $liquidacion)
                        ->where('tercero_id', $tercero->id)
                        ->first();

                    $update = LiquidacionTercero::find($liquidacion_tercero->id);

                    $ID_GOOD = Good::exist($tercero->email);
                    $ID_MERCANDO = Mercando::exist($tercero->email);

                    $result_g = array();
                    $result_m = array();

                    if (count($liquidacion_tercero) > 0) {


                        if ($good != 0 && $mercando == 0) {

                            if ($ID_GOOD != 0) {

                                return $result = GiftCard::gift(Good::url(), $good, $ID_GOOD);


                                if ($result == null) {

                                    return redirect()->back()->withErrors(['errors' => '¡Lo sentimos, hubo un error al tratar de crear su bono en Tienda Good, pongase en contacto con servicio al cliente.!']);
                                }

                                $update->bono_good = $good;
                                $update->bono_mercando = $mercando;
                                $update->giftcard_good = $result;
                                $update->giftcard_mercando = null;
                                $update->save();

                                return redirect()->back()->with(['g' => '¡Felicidades, su bono en Tienda Good ha sido creado exitosamente.!']);

                            }

                        }

                        if ($good == 0 && $mercando != 0) {

                            if ($ID_MERCANDO != 0) {


                                return $result = GiftCard::gift(Mercando::url(), $mercando, $ID_MERCANDO);


                                if ($result == null) {

                                    return redirect()->back()->withErrors(['errors' => '¡Lo sentimos, hubo un error al tratar de crear su bono en Mercando, pongase en contacto con servicio al cliente.!']);
                                }

                                $update->bono_good = $good;
                                $update->bono_mercando = $mercando;
                                $update->giftcard_good = null;
                                $update->giftcard_mercando = $result;
                                $update->save();

                                return redirect()->back()->with(['m' => '¡Felicidades, su bono en Mercando ha sido creado exitosamente en Mercando.!']);

                            }
                        }

                        if ($good != 0 && $mercando != 0) {

                            $g = true;
                            $m = true;

                            if ($ID_GOOD != 0) {

                                $result_g = GiftCard::gift(Good::url(), $good, $ID_GOOD);


                                if ($result_g == null) {
                                    $g = false;
                                }

                            } else {
                                $g = false;
                            }

                            if ($ID_MERCANDO != 0) {

                                $result_m = GiftCard::gift(Mercando::url(), $mercando, $ID_MERCANDO);

                                if ($result_m == null) {

                                    $m = false;
                                }

                            } else {

                                $m = false;
                            }

                            if ($g == false && $m != false) {

                                $update->bono_good = $good;
                                $update->bono_mercando = $mercando;
                                $update->giftcard_good = null;
                                $update->giftcard_mercando = $result_m;
                                $update->save();

                                return redirect()->back()->with(['m' => '¡Su bono en Mercando ha sido creado pero su bono para Tienga Good no se pudo crear, pongase en contacto con servicio al cliente para verificar su bono en Tienda Good.!']);

                            }

                            if ($g != false && $m == false) {

                                $update->bono_good = $good;
                                $update->bono_mercando = $mercando;
                                $update->giftcard_good = $result_g;
                                $update->giftcard_mercando = null;
                                $update->save();

                                return redirect()->back()->with(['g' => '¡Su bono en Tienda Good ha sido creado pero su bono para Mercando no se pudo crear, pongase en contacto con servicio al cliente para verificar su bono en Mercando.!']);


                            }

                            if ($g == false && $m == false) {

                                $update->bono_good = $good;
                                $update->bono_mercando = $mercando;
                                $update->giftcard_good = null;
                                $update->giftcard_mercando = null;
                                $update->save();

                                return redirect()->back()->withErrors(['errors' => '¡Lo sentimos, sus bonos no pudieron ser creados, pongase en contacto de inmediato con servicio al cliente.!']);


                            }

                            if ($g != false && $m != false) {

                                $update->bono_good = $good;
                                $update->bono_mercando = $mercando;
                                $update->giftcard_good = $result_g;
                                $update->giftcard_mercando = $result_m;
                                $update->save();

                                return redirect()->back()->with(['gm' => '¡Felicidades, sus bonos han sido creados correctamente.!']);
                            }
                        }

                    } else {

                        return redirect()->back()->withErrors(['errors' => '¡Liquidación existe!']);
                    }
                }

            }

        } else {

            return redirect()->back()->withErrors(['errors' => '¡No se encontró la variable para good o mercando!']);
        }
    }

}
