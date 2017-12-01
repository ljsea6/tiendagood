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


class AdminController extends Controller {
    
    public function email()
    {
        return view('admin.send.mail');
    }
    
    public function send(Request $request)
    {
        $data = $request->all();

        //return $data;
       
        if(isset($data['email']) && $data['email'] !== '') {

            Mail::send('admin.send.message', ['body' => $request->body, 'code' => $request->code], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email);
            });

        }
        
        if(isset($data['emailone']) && $data['emailone'] !== '') {
            Mail::send('admin.send.message', ['body' => $request->body, 'code' => $request->code], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->emailone);
            });
        }
        
        if(isset($data['emailtwo']) && $data['emailtwo'] !== '') {
            Mail::send('admin.send.message', ['body' => $request->body, 'code' => $request->code], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->emailtwo);
            });
        }
        
        if(isset($data['email1']) && $data['email1'] !== '') {
            Mail::send('admin.send.message', ['body' => $request->body, 'code' => $request->code], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email1);
            });
        }
        
        if(isset($data['email2']) && $data['email2'] !== '') {
            Mail::send('admin.send.message', ['body' => $request->body, 'code' => $request->code], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email2);
            });
        }
        
        if(isset($data['email3']) && $data['email3'] !== '') {
            Mail::send('admin.send.message', ['body' => $request->body, 'code' => $request->code], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email3);
            });
        }
        
        if(isset($data['email4']) && $data['email4'] !== '') {
            Mail::send('admin.send.message', ['body' => $request->body, 'code' => $request->code], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email4);
            });
        }
        
         if(isset($data['email5']) && $data['email5'] !== '') {
            Mail::send('admin.send.message', ['body' => $request->body, 'code' => $request->code], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email2);
            });
        }
        
        if(isset($data['email6']) && $data['email6'] !== '') {
            Mail::send('admin.send.message', ['body' => $request->body, 'code' => $request->code], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email3);
            });
        }
        
        if(isset($data['email7']) && $data['email7'] !== '') {

            Mail::send('admin.send.message', ['body' => $request->body, 'code' => $request->code], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Tienda good');
                $message->subject($request->body);
                $message->to($request->email4);
            });

        }
        
        return view('admin.send.success');
    }

    public function buscar(Request $request)
    {
        if ($request->has('email')) {

            $tercero = Tercero::with('networks')->where('email', strtolower($request['email']))->get();

            return view('admin.search.index', compact('tercero'));
        }

    }

    public function search()
    {
        return view('admin.search');
    }

    public function finder(Request $request)
    {
        if ($request->has('email') && $request->has('id')) {

            $results = Tercero::with('networks')
                ->where('email', '=', "".strtolower($request['email'])."")   
                ->orWhere(DB::raw("(nombres)"), '=', "".strtolower($request['email'])."")
                ->where('state', true)
                ->first();

            $level = '';

            if (count($results) > 0 && $results->state == true) {
                if (count($results->networks) > 0) {
                    $uno = $results->networks[0]->pivot->padre_id;
                    if ($uno == $request->id) {
                        $level = 1;
                       // return view('admin.find')->with(['results' => $results, 'level' => $level]);
                       $data[] =  array('label' => 'Nombre: '.$results['nombres'].' '.$results['apellidos'].' Correo: '.$results['email'].' Nivel:'.$level,  
                       	'nombre' => $results['nombres'].' '.$results['apellidos'], 'correo' => $results['email'],  'nivel' => $level);    
                         echo json_encode($data);
                    } else {
                        $results_dos = Tercero::with('networks')->find($uno);
                        if (count($results_dos) > 0 && $results_dos->state == true) {
                            if (count($results_dos->networks) > 0) {
                                $dos = $results_dos->networks[0]->pivot->padre_id;
                                if ($dos == $request->id) {
                                    $level = 2;
                                   // return view('admin.find')->with(['results' => $results, 'level' => $level]);
                       $data[] =  array('label' => 'Nombre: '.$results['nombres'].' '.$results['apellidos'].' Correo: '.$results['email'].' Nivel:'.$level,  
                       	'nombre' => $results['nombres'].' '.$results['apellidos'], 'correo' => $results['email'],  'nivel' => $level);                          
                                   echo json_encode($data);
                                } else {
                                    $results_tres = Tercero::with('networks')->find($dos);
                                    if (count($results_tres) > 0 && $results_tres->state == true) {
                                        if (count($results_tres->networks) > 0) {
                                            $tres = $results_tres->networks[0]->pivot->padre_id;
                                            if ($tres == $request->id) {
                                                $level = 3;
                                               // return view('admin.find')->with(['results' => $results, 'level' => $level]);
                       $data[] =  array('label' => 'Nombre: '.$results['nombres'].' '.$results['apellidos'].' Correo: '.$results['email'].' Nivel:'.$level,  
                       	'nombre' => $results['nombres'].' '.$results['apellidos'], 'correo' => $results['email'],  'nivel' => $level);                                           
                                               echo json_encode($data);

                                            } else {
                                                $err = 'No está en su lista de referidos';
                                                //return view('admin.find', compact('err'));
                                                $data[] =  array('label' => $err, 'nivel' => 0);                       
                                               echo json_encode($data);                                               
                                            }
                                        }
                                    } else {
                                        $err = 'No está en su lista de referidos';
                                        //return view('admin.find', compact('err'));
                                        $data[] =  array('label' => $err, 'nivel' => 0);                       
                                        echo json_encode($data);                                       
                                    }
                                }
                            }
                        } else {
                            $err = 'No está en su lista de referidos';
                            //return view('admin.find', compact('err'));
                            $data[] =  array('label' => $err, 'nivel' => 0);                       
                           echo json_encode($data);                         
                        }
                    }
                } else {
                    $err = 'No está en su lista de referidos';
                    //return view('admin.find', compact('err'));
                    $data[] =  array('label' => $err, 'nivel' => 0);                       
                    echo json_encode($data);                   
                }

            } else {
                $err = 'No está en su lista de referidos';
                //return view('admin.find', compact('err'));
                $data[] =  array('label' => $err, 'nivel' => 0);                       
                echo json_encode($data);               
            }

           /* if(currentUser()->tipo_id == 2) {
                $results = Tercero::where('email', 'like', '%' .strtolower($request['email']) . '%')->get();

                return view('admin.find', compact('results'));
            } */

            /*if (currentUser()->tipo_id != 2 && currentUser()->tipo_id != 1) {

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

            }*/
        }
    }

    public function network()
    {
        $tercero = Tercero::find(currentUser()->id);
        $referidos  = DB::table('terceros')
            ->where('apellidos',  $tercero['email'])
            ->select('id', 'nombres', 'email')
            ->get();

        $orders = Order::where('email', $tercero['email'])->get();
        $total = 0;

        foreach ($referidos as $referido) {
            $results = Order::where('email', $referido->email)->get();
            if (count($results) > 0) {
                foreach ($results as $result) {
                    $total = $total + (double)$result['total_price'];
                }
            }
        }

        $totalPrice = number_format($total, 0);
        $networks = Network::all();
        $terceros = [];

        foreach ($networks as $network) {
            $results = DB::table('terceros')
                ->join('networks', 'terceros.network_id', '=', 'networks.id')
                ->where('terceros.apellidos',  $tercero['email'])
                ->where('networks.id', $network['id'])
                ->select('terceros.id', 'terceros.nombres', 'terceros.apellidos','terceros.email', 'terceros.network_id')
                ->take(10)->get();
            foreach ($results as $result) {
                array_push($terceros, (array)$result);
            }
        }

        $send = [
            'referidos' => number_format(count($referidos)),
            'orders'  => number_format(count($orders)),
            'total' => $totalPrice,
            'terceros' => collect($terceros),
            'tercero' => $tercero,
            'redes' => $networks
        ];

        return view('admin.network', compact('send'));
    }

    public function index()
    {
    	$level_uno = 0;
    	$level_dos = 0;
    	$level_tres = 0;

                $uno  = DB::table('terceros as t')
                    ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                    ->where('tk.padre_id',  currentUser()->id)
                    ->where('t.state',  true)
                    ->select('t.id')
                    ->get();

                $results = array();

                if (count($uno) > 0) {

                	$level_uno = $level_uno + count($uno);

                    foreach ($uno as $n) {

                        $dos  = DB::table('terceros as t')
                            ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                            ->where('tk.padre_id',  $n->id)
                            ->where('t.state',  true)
                            ->select('t.id')
                            ->get();

                        if (count($dos) > 0) {

                        	$level_dos = $level_dos + count($dos);

                            foreach ($dos as $d) {

                                $tres  = DB::table('terceros as t')
                                    ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                                    ->where('tk.padre_id',  $d->id)
                                    ->where('t.state',  true)
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
   		       
                   	
        $send = Tercero::find(currentUser()->id);
        return view('admin.index')->with(['send' => $send, 'uno' => $level_uno, 'dos' => $level_dos, 'tres' => $level_tres]);
    }

    public function anyData(Request $request)
    {
        $tercero = Tercero::find((int)$request['id']);
        $results  = DB::table('terceros')
            ->join('networks', 'terceros.network_id', '=', 'networks.id')
            ->where('terceros.apellidos',  $tercero['email'])
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

    public function nivel($nivel=1)
    {
        return view('admin.nivel.nivel', compact('nivel'));
    }

    public function level_one(Request $request)
    {

        if ($request->has('level') && $request->has('id')) {

            if ($request->level == 1) {

                $results  = DB::table('terceros as t')
                    ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                    ->where('tk.padre_id',  $request->id)
                    ->where('t.state',  true)
                    ->select('t.id', 't.nombres', 't.mispuntos')
                    ->get();


                $send = collect($results);
                return Datatables::of($send)
                    ->addColumn('id', function ($send) {
                        return '<div align=left>' . $send->id . '</div>';
                    })
                    ->addColumn('nombres', function ($send) {
                        return '<div align=left>' . ucwords($send->nombres) . '</div>';
                    })
                    ->addColumn('puntos', function ($send) {
                        return '<div align=left>' . number_format($send->mispuntos)  . '</div>';
                    })
                    ->make(true);
            }

        }
    }

    public function level_two(Request $request)
    {
        if ($request->has('level') && $request->has('id')) {

            if ($request->level == 2) {

                $uno  = DB::table('terceros as t')
                    ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                    ->where('tk.padre_id',  $request->id)
                    ->where('t.state',  true)
                    ->select('t.id')
                    ->get();

                $results = array();

                if (count($uno) > 0) {

                    foreach ($uno as $n) {

                        $dos  = DB::table('terceros as t')
                            ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                            ->where('tk.padre_id',  $n->id)
                            ->where('t.state',  true)
                            ->select('t.id', 't.nombres', 't.mispuntos')
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
                    ->addColumn('puntos', function ($send) {
                        return '<div align=left>' . number_format($send->mispuntos) . '</div>';
                    })
                    ->make(true);
            }

        }
    }

    public function level_tree(Request $request)
    {
        if ($request->has('level') && $request->has('id')) {

            if ($request->level == 3) {

                $uno  = DB::table('terceros as t')
                    ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                    ->where('tk.padre_id',  $request->id)
                    ->where('t.state',  true)
                    ->select('t.id')
                    ->get();

                $results = array();

                if (count($uno) > 0) {

                    foreach ($uno as $n) {

                        $dos  = DB::table('terceros as t')
                            ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                            ->where('tk.padre_id',  $n->id)
                            ->where('t.state',  true)
                            ->select('t.id')
                            ->get();

                        if (count($dos) > 0) {

                            foreach ($dos as $d) {

                                $tres  = DB::table('terceros as t')
                                    ->join('terceros_networks as tk', 'tk.customer_id', '=', 't.id')
                                    ->where('tk.padre_id',  $d->id)
                                    ->where('t.state',  true)
                                    ->select('t.id', 't.nombres', 't.mispuntos')
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
                    ->addColumn('puntos', function ($send) {
                        return '<div align=left>' . number_format($send->mispuntos) . '</div>';
                    })
                    ->make(true);
            }

        }
    }

    public function indexproveedores()
    {
        return view('admin.proveedores.index');
	}
}