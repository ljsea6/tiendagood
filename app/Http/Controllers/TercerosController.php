<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Entities\Tercero;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class TercerosController extends Controller {

    /**
     * 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin.terceros.index');
    }

    public function anyData() {

        $referidos = Tercero::select('id', 'identificacion', 'nombres', 'apellidos', 'email', 'nivel_1', 'nivel_2', 'nivel_3', 'mispuntos', 'puntos_vendidos','rut','cedula','cuenta')
                ->where('state', true)
                ->get();

        $send = collect($referidos);

        return Datatables::of($send)
                        ->addColumn('action', function ($send ) {
                            return '<div align=center><a href="' . route('admin.terceros.show', $send['id']) . '"  class="btn btn-success btn-xs">
                        Red
                </a></div>';
                        })
                        ->addColumn('id', function ($send) {
                            return '<div align=left>' . $send['id'] . '</div>';
                        })
                        ->addColumn('identificacion', function ($send) {
                            return '<div align=left>' . $send['identificacion'] . '</div>';
                        })
                        ->addColumn('nombres', function ($send) {
                            return '<div align=left>' . $send['nombres'] . '</div>';
                        })
                        ->addColumn('apellidos', function ($send) {
                            return '<div align=left>' . $send['apellidos'] . '</div>';
                        })
                        ->addColumn('email', function ($send) {
                            return '<div align=left>' . $send['email'] . '</div>';
                        })
                        ->addColumn('nivel_1', function ($send) {
                            return '<div align=left>' . number_format($send['nivel_1']) . '</div>';
                        })
                        ->addColumn('nivel_2', function ($send) {
                            return '<div align=left>' . number_format($send['nivel_2']) . '</div>';
                        })
                        ->addColumn('nivel_3', function ($send) {
                            return '<div align=left>' . number_format($send['nivel_3']) . '</div>';
                        })
                        ->addColumn('mispuntos', function ($send) {
                            return '<div align=left>' . number_format($send['mispuntos']) . '</div>';
                        })
                        ->addColumn('puntos_vendidos', function ($send) {
                            return '<div align=left>' . number_format($send['puntos_vendidos']) . '</div>';
                        })
                        ->addColumn('edit', function ($send) {
                            return '<div align=center><a href="' . route('admin.terceros.edit', $send['id']) . '"  class="btn btn-warning btn-xs">
                        Editar
                </a></div>';
                        })
                        ->addColumn('rut', function ($send) {                
                            if($send['rut'] == NULL){
                                return 'Sin RUT';
                            }
                            else{
                                $rut = $send['rut'];
                                return '<div align=center><a href="' . route('admin.terceros.descargar_documentos',$send['rut']) .'"  class="btn btn-primary btn-xs"> RUT </a></div>';
                            }                           
                        })
                        ->addColumn('CC', function ($send) {
                            if($send['cedula'] == NULL){
                                return 'Sin cédula o Documento';
                            }
                            else{ 
                                return '<div align=center><a href="' . route('admin.terceros.descargar_documentos',$send['cedula']) .'"  class="btn btn-success btn-xs"> Cédula o Documento </a></div>';
                            }                               
                            
                        })
                        ->addColumn('BANK', function ($send) {
                            if($send['cuenta'] == NULL){
                                return 'Sin certificación bancaria';
                            }
                            else{ 
                                 return '<div align=center><a href="' . route('admin.terceros.descargar_documentos',$send['cuenta']) .'"  class="btn btn-warning btn-xs"> Certificación bancaria  </a></div>';
                            }                              
                        })
                        ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return view('admin.terceros.show', compact('id'));
    }

    public function anyShow(Request $request) {

        $results = DB::table('terceros as t')
                ->select('t2.id', 't2.nombres', 't2.apellidos', 't2.email')
                ->join('terceros_networks as tn', 'tn.padre_id', '=', 't.id')
                ->join('terceros as t2', 't2.id', '=', 'tn.customer_id')
                ->where('t.id', $request->id)
                ->get();

        $send = collect($results);

        return Datatables::of($send)
                        ->addColumn('id', function ($send) {
                            return '<div align=left>' . $send->id . '</div>';
                        })
                        ->addColumn('nombres', function ($send) {
                            return '<div align=left>' . $send->nombres . '</div>';
                        })
                        ->addColumn('email', function ($send) {
                            return '<div align=left>' . $send->email . '</div>';
                        })
                        ->addColumn('apellidos', function ($send) {
                            return '<div align=left>' . $send->apellidos . '</div>';
                        })
                        ->make(true);
    }

    public function edit($id) {
        $tercero = Tercero::find($id);

        if ($tercero->state === true) {
            return view('admin.terceros.edit', compact('tercero'));
        }
    }

    public function update(Request $request, $id) {
        $state = $request['state'];

        if ($state === 'false') {

            $tercero = Tercero::with('networks')->find($id);
            $tercero->state = $state;
            $tercero->numero_referidos = null;


            $networks = $tercero['networks'];
            $father = $networks[0]['pivot']['padre_id'];


            if (!is_null($father)) {

                $up = Tercero::find($father);
                $up->numero_referidos = $up->numero_referidos - 1;
                $up->save();
                $referidos = DB::table('terceros_networks')->where('padre_id', $tercero->id)->get();

                if (count($referidos) > 0) {

                    foreach ($referidos as $referido) {

                        DB::table('terceros_networks')->where('customer_id', $referido->customer_id)->update(['padre_id' => $father]);

                        DB::insert('insert into referidos_logs (tercero_id, old_father, new_father, created_at, updated_at) values (?, ?, ?, ?, ?)', [
                            $referido->customer_id,
                            $referido->padre_id,
                            $father,
                            Carbon::now(),
                            Carbon::now(),
                        ]);

                        $update = Tercero::find($father);
                        $update->numero_referidos = $update->numero_referidos + 1;
                        $update->save();
                    }
                }
            }

            if (is_null($father)) {

                $referidos = DB::table('terceros_networks')->where('padre_id', $tercero->id)->get();

                if (count($referidos) > 0) {

                    foreach ($referidos as $referido) {
                        DB::table('terceros_networks')->where('customer_id', $referido->customer_id)->update(['padre_id' => null]);
                        DB::insert('insert into referidos_logs (tercero_id, old_father, new_father, created_at, updated_at) values (?, ?, ?, ?, ?)', [
                            $referido->customer_id,
                            $referido->padre_id,
                            null,
                            Carbon::now(),
                            Carbon::now(),
                        ]);
                    }
                }
            }

            DB::table('terceros_logs')->insert([
                'tercero_id' => $tercero->id,
                'padre_id' => $father,
                'user' => currentUser()->nombre_completo,
                'ip' => $request->ip(),
                'browser' => $request->server('HTTP_USER_AGENT'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $tercero->save();

            return redirect('admin/terceros')->with(['status' => 'Se han hechos los cambios correctamente']);
        } else {

            return redirect('admin/terceros')->with(['status' => 'No se han hecho cambios.']);
        }
    }

    public function padreTercero(Request $request) {
        if ($request->has('textbuscar') && $request->has('id')) {
            $data = ['error' => false];
            $tercero = Tercero::with('networks')
                    ->where('email', '=', "" . strtolower($request['textbuscar']) . "")
                    ->orWhere("identificacion", '=', "" . strtolower($request['textbuscar']) . "")
                    ->first();

            if (count($tercero) > 0) {
                $networks = $tercero['networks'];
                $father = $networks[0]['pivot']['padre_id'];

                $tipo_cliente = \App\Entities\Tipo::find($tercero->tipo_cliente_id)->nombre;
                $data['tercero'] = ['id' => $tercero->id, 'nombre' => "$tercero->nombres $tercero->apellidos", 'identificacion' => $tercero->identificacion, 'email' => $tercero->email, 'tipo_cliente_id' => $tercero->tipo_cliente_id, 'error' => false];

                if (!is_null($father)) {
                    $padre = Tercero::find($father);
                    $tipo_padre = \App\Entities\Tipo::find($padre->tipo_cliente_id)->nombre;
                    $data['padre'] = ['nombre' => "$padre->nombres $padre->apellidos", 'email' => $padre->email, 'tipo_cliente' => $tipo_padre];
                    echo json_encode($data);
                } else {
                    $data['tercero']['error'] = '¡No se encuentra el usuario padre!';
                    echo json_encode($data);
                }
            } else {
                $data = array('error' => '¡No se encuentra el usuario tercero!');
                echo json_encode($data);
            }
        }
    }

    public function getPadre(Request $request) {
        if ($request->has('identificacion') && $request->has('_token')) {
            $data = ['error' => false];
            $padre = Tercero::where("identificacion", '=', "" . $request['identificacion'] . "")->first();

            if (count($padre) > 0) {
                if ($padre->identificacion != $request['tercero']) {
                    if ($padre->tipo_cliente_id == 83) {
                        $tipo_padre = \App\Entities\Tipo::find($padre->tipo_cliente_id)->nombre;
                        $data = ['nombre' => "$padre->nombres $padre->apellidos", 'email' => $padre->email, 'tipo_cliente' => $tipo_padre];
                        echo json_encode($data);
                    } else {
                        $data['error'] = '¡El usuario padre no es un vendedor!';
                        echo json_encode($data);
                    }
                } else {
                    $data['error'] = '¡El usuario padre no puede ser el mismo usuario!';
                    echo json_encode($data);
                }
            } else {
                $data['error'] = '¡No se encuentra el usuario padre!';
                echo json_encode($data);
            }
        }
    }

    public function editarDatos() {
        if (isset($_POST['id']) && isset($_POST['_token'])) {
            $model = Tercero::find($_POST['id']);
            $model->email = $_POST['identificacion'];
            $model->email = $_POST['email'];
            $model->tipo_cliente_id = $_POST['tipo_cliente_id'];
            print_r($model->save());
        }
    }

    function padreCambiar() {
        return view('admin.terceros.cambiarpadre');
    }    

    public function lista_documentos() {
        return view('admin.terceros.lista_documentos');
    }   

    public function descargar_documentos($nombre) {
 
        if ($nombre != '0') {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename(public_path() . "/uploads/".$nombre));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize(public_path() . "/uploads/".$nombre));
            ob_clean();
            flush();
            readfile($nombre);
            exit;  
        } else {
            return view('admin.terceros.lista_documentos');
        }
         
    }

}
