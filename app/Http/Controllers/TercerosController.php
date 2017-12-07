<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Entities\Tercero;
use Yajra\Datatables\Datatables;
use DB;
use Carbon\Carbon;


class TercerosController extends Controller
{
   

    /**
     * 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.terceros.index');
    }

    public function anyData()
    {
      
        $referidos = Tercero::select('id', 'identificacion', 'nombres', 'apellidos', 'email', 'numero_referidos', 'numero_ordenes_referidos', 'total_price_orders', 'ganacias')
                ->where('state', true)
                ->get();
        
        $send = collect($referidos);

        return Datatables::of($send )
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
            ->addColumn('numero_referidos', function ($send) {
                return '<div align=left>' . number_format($send['numero_referidos']) . '</div>';
            })
            ->addColumn('numero_ordenes_referidos', function ($send) {
                return '<div align=left>' . number_format($send['numero_ordenes_referidos']) . '</div>';
            })
            ->addColumn('total_price_orders', function ($send) {
                return '<div align=left>' . number_format($send['total_price_orders']) . '</div>';
            })
            ->addColumn('ganacias', function ($send) {
                return '<div align=left>' . number_format($send['ganacias']) . '</div>';
            })
             ->addColumn('edit', function ($send) {
                return '<div align=center><a href="' . route('admin.terceros.edit', $send['id']) . '"  class="btn btn-warning btn-xs">
                        Editar
                </a></div>';
            })
            ->make(true);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('admin.terceros.show', compact('id'));
    }

    public function anyShow(Request $request)
    {

        $results = DB::table('terceros as t')
            ->select('t2.id', 't2.nombres', 't2.apellidos', 't2.email')
            ->join('terceros_networks as tn', 'tn.padre_id', '=', 't.id')
            ->join('terceros as t2', 't2.id', '=', 'tn.customer_id')
            ->where('t.id', $request->id)
            ->get();

        $send = collect($results);

        return Datatables::of($send )
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

    public function edit($id)
    {
        $tercero = Tercero::find($id);
        
        if($tercero->state === true) {
           return view('admin.terceros.edit', compact('tercero')); 
        }
    }

    public function update(Request $request, $id) 
    {
        $state = $request['state'];

        if ($state === 'false') {

            $tercero = Tercero::with('networks')->find($id);
            $tercero->state = $state;
            $tercero->numero_referidos = null;


            $networks = $tercero['networks'];
            $father = $networks[0]['pivot']['padre_id'];


            if(!is_null($father)){

                $up = Tercero::find($father);
                $up->numero_referidos = $up->numero_referidos - 1;
                $up->save();
                $referidos = DB::table('terceros_networks')->where('padre_id', $tercero->id)->get();
             
                if(count($referidos) > 0) {

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

            if(is_null($father)) {

                $referidos = DB::table('terceros_networks')->where('padre_id', $tercero->id)->get();
                
                if(count($referidos) > 0) {
                    
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
    
    public function cambiarPadre()
    {
        return view('admin.terceros.cambiarpadre');
    }
    
}
