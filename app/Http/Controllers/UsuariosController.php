<?php
namespace App\Http\Controllers;

use App\Banco;
use DB;
use App\Tipo;
use App\Documento;
use App\TipoCliente;
use Carbon\Carbon;
use function GuzzleHttp\Psr7\str;
use Validator;
use App\Entities\Ciudad;
use App\Entities\Oficina;
use App\Entities\Tercero;
use App\Entities\Network;
use App\Entities\Tercero_network;
use App\Http\Controllers\Controller;
use App\Http\Requests\Terceros\Usuarios\EditaUsuario;
use App\Http\Requests\Terceros\Usuarios\NuevoUsuario;
use App\Http\Requests\Terceros\Usuarios\NuevoUsuarioexterno;
use Bican\Roles\Models\Permission;
use Bican\Roles\Models\Role;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Yajra\Datatables\Datatables;

class UsuariosController extends Controller {
    /**
     * Display a listing of the  resource.
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function terms()
    {
        return view('admin.terms.terms');
    }
    public function verified_email(Request $request)
    {
        if ($request->has('email')) {

            $email = Tercero::where('email', $request->email)->first();

            if (count($email) > 0) {
                return response()->json(['err' => 'email existe'], 200);
            } else {
                return response()->json(['msg' => 'email valido'], 200);
            }


        } else {
            return response()->json(['err' => 'Falta el parametro email'], 200);
        }

    }
    public function verified_phone(Request $request)
    {
        if ($request->has('phone')) {

            $phone = Tercero::where('telefono', $request->phone)->first();

            if (count($phone) > 0) {
                return response()->json(['err' => 'telefono existe'], 200);
            } else {
                return response()->json(['msg' => 'telefono valido'], 200);
            }

        } else {
            return response()->json(['err' => 'Falta el parametro phone'], 200);
        }


    }
    public function verified_code(Request $request)
    {
        if ($request->has('code')) {

            $code = Tercero::where('identificacion', $request->code)->first();

            if (count($code) > 0) {
                return response()->json(['msg' => 'código valido'], 200);
            } else {
                return response()->json(['err' => 'código no valido'], 200);
            }


        } else {
            return response()->json(['err' => 'Falta el parametro code'], 200);
        }
    }
    public function index()
    {
        return $permisos = Permission::lists('name', 'id')->get();
        return view('admin.usuarios.index', compact('permisos'));
    }
    public function anyData()
    {
        $usuarios = Tercero::select('terceros.id', 'terceros.avatar', 'terceros.identificacion', 'terceros.nombres', 'terceros.apellidos', 'terceros.direccion', 'ciudades.nombre as ciudad', 'terceros.email', 'roles.name as rol', 'tipos.nombre as tipo')
            ->leftjoin('ciudades', 'terceros.ciudad_id', '=', 'ciudades.id')
            ->leftjoin('roles', 'terceros.rol_id', '=', 'roles.id')
            ->leftjoin('tipos','tipos.id','=','terceros.tipo_id')
            ->orderby('terceros.id');
        return Datatables::of($usuarios)
            ->addColumn('identificacion', function ($usuarios) {
                return '<div align=left>'.$usuarios->identificacion.'</div>';
            })
            ->addColumn('nombres', function ($usuarios) {
                return '<div align=left>'.$usuarios->nombres.'</div>';
            })
            ->addColumn('apellidos', function ($usuarios) {
                return '<div align=left>'.$usuarios->apellidos.'</div>';
            })
            ->addColumn('direccion', function ($usuarios) {
                return '<div align=left>'.$usuarios->direccion.'</div>';
            })
            ->addColumn('ciudad', function ($usuarios) {
                return '<div align=left>'.$usuarios->ciudad.'</div>';
            })
            ->addColumn('rol', function ($usuarios) {
                return '<div align=left>'.$usuarios->rol.'</div>';
            })
            ->addColumn('tipo', function ($usuarios) {
                return '<div align=left>'.$usuarios->tipo.'</div>';
            })
            ->addColumn('permisos', function ($usuarios) {
                return '
                    <div align=left>
                        <a data-toggle="modal" tercero_id="' . $usuarios->id . '" data-target="#permisos" class="btn btn-primary btn-xs get-permisos" OnClick="get_permisos(' . $usuarios->id . ');">Permisos</a>
                    </div>
                ';
            })
            ->addColumn('action', function ($usuarios) {
                return '
                    <div align=left>
                        <a  href="' . route('admin.usuarios.edit', $usuarios->id) . '"  class="btn btn-warning btn-xs">
                            <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                        </a>
                        <a href="' . route('admin.usuarios.destroy', $usuarios->id) . '"  onclick="return confirm(\'¿ Desea eliminar el registro seleccionado ?\')" class="btn btn-danger btn-xs">
                                <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
                        </a>
                    </div>
                ';
            })
            ->editColumn('avatar', function ($usuarios) {
                if ($usuarios->avatar !== NULL) {
                    return '<div align=left><img src="' . asset($usuarios->avatar) . '" class="img-circle avatar_table" /></div> ';
                } else {
                    return '<div align=left><img src="' . asset('img/avatar-bg.png') . '" class="img-circle avatar_table"/></div>';
                }
            })
            ->make(true);
    }
    public function hijos($id)
    {
        $usuarios = Tercero::tipoUsuario(2)->with('ciudad')->orderby('id')->paginate(10);
        return view('admin.usuarios.index', compact('usuarios'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ciudades   = Ciudad::get()->lists('nombre_completo', 'id');
        $tipos = Tipo::get()->lists('nombre','id');//->toarray();
        $oficinas =  Oficina::lists('nombre', 'id');
        $roles      = Role::lists('name', 'id');
        $clientes   = Tercero::tipoUsuario(3)->get()->lists('nombre_completo', 'id')->toArray();
        $red = Network::lists('name','id');
        return view('admin.usuarios.create', compact('ciudades','tipos', 'oficinas', 'roles', 'clientes','red'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store(NuevoUsuario $request)
    {
        $avatar = $request->file('avatar');
        $query = Tercero::select('id')->where('email', '=', ($request->email_Patrocinador))->first();
        if ($avatar) {
            $avatar_nombre = str_random(30) . "." . $avatar->getClientOriginalExtension();
            $path = public_path() . "/uploads/avatar/";
            $avatar->move($path, $avatar_nombre);
        }
        $usuario = new Tercero($request->all());
        $usuario->save();
        if ($avatar) {
            $usuario->avatar = "uploads/avatar/" . $avatar_nombre;
        }
        $usuario->contraseña = bcrypt($request->contraseña);
        $usuario->tipo_id = $request->tipo_id;
        if ($request->cliente) {
            $usuario->padre_id = $request->cliente;
        }
        $usuario->rol_id = $request->rol_id;
        $usuario->usuario_id = currentUser()->id;
        $padre = Tercero::find($query->id);
        $padre->numero_referidos = $padre->numero_referidos + 1;
        $usuario->ip = $request->ip();
        $usuario->networks()->attach(1, ['padre_id' => $padre->id]);
        $padre->save();
        $usuario->save();
        $permisos = Role::findOrFail($request->rol_id)->permissions;
        foreach ($permisos as $per) {
            $permiso = Permission::findOrFail($per->id);
            $usuario = Tercero::findOrFail($usuario->id);
            $usuario->attachPermission($permiso);
        }
        Alert::message("! Usuario registrado con éxito !  ", 'success');
        return redirect()->route('admin.usuarios.index');
    }
    /**
     * Display the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {}
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $usuario    = Tercero::findOrFail($id);
        $ciudades   = Ciudad::get()->lists('nombre_completo', 'id');
        $tipos = Tipo::get()->lists('nombre','id');//TipoIdenti(tipo_id)->
        $oficinas = Oficina::lists('nombre', 'id');
        $roles      = Role::lists('name', 'id');
        $clientes   = Tercero::tipoUsuario(3)->get()->lists('nombre_completo', 'id')->toArray();
        return view('admin.usuarios.edit', compact('usuario', 'tipos','ciudades', 'oficinas', 'roles', 'clientes'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditaUsuario $request, $id)
    {
        $usuario = Tercero::findOrFail($id);
        $usuario->fill($request->all());
        if ($request->file("avatar")) {
            $avatar        = $request->file('avatar');
            $avatar_nombre = str_random(30) . "." . $avatar->getClientOriginalExtension();
            $path          = public_path() . "/uploads/avatar/";
            $avatar->move($path, $avatar_nombre);
            $usuario->avatar = "uploads/avatar/" . $avatar_nombre;
        }

        $usuario->rol_id      = $request->rol_id;
        $usuario->usuario_id  = currentUser()->id;
        $usuario->ip          = $request->ip();
        $usuario->tipo_id    = $request->tipo_id;


        $usuario->save();
        Alert::message('! Usuario ' . $usuario->nombres . " " . $usuario->apellidos . " Actualizado con éxito ! ", 'success');
        return redirect()->route('admin.usuarios.index');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usuario = Tercero::findOrFail($id);
        $usuario->delete();
        Alert::message('! Usuario ' . $usuario->nombres . " " . $usuario->apellidos . " eliminado con éxito! ", 'success');
        return redirect()->route('admin.usuarios.index');
    }

    protected function getusuario(Request $request)
    {

        $cities = Ciudad::orderBy('nombre', 'asc')->get();

        $tipos= Tipo::with('tipos')->find(76);

        $cuentas = Tipo::with('tipos')->find(75);

        $documentos = Tipo::with('tipos')->find(74);

        $bancos = Banco::get();

        if ($request->has('q')) {

            $email = '' . $request->q;

            return view('admin.usuarios.createusua')->with(['tipos' => $tipos, 'email' => $email, 'cities' => $cities, 'documentos' => $documentos, 'bancos' => $bancos, 'cuentas' => $cuentas]);
            //return view('admin.usuarios.createusua')->with(['tipos' => $tipos, 'email' => $email, 'cities' => $cities, 'documentos' => $documentos,  'cuentas' => $cuentas]);
        }

        return view('admin.usuarios.createusua')->with(['tipos' => $tipos, 'cities' => $cities, 'documentos' => $documentos, 'bancos' => $bancos, 'cuentas' => $cuentas]);
        //return view('admin.usuarios.createusua')->with(['tipos' => $tipos, 'cities' => $cities, 'documentos' => $documentos,  'cuentas' => $cuentas]);
    }

    public function storeNuevo(Request $request)
    {

        $messages = [
            'code.exists' => 'El código referido es invalido, por favor verifiquelo!',
            'first-name.required' => 'Los nombre es requerido.',
            'last-name.required' => 'Los apellidos son requeridos.',
            'type_client.required' => 'Tipo de cliente requerido.',
            'type_dni.required' => 'Tipo de documento requerido.',
            'dni.required' => 'Número de documento requerido.',
            'city.required' => 'Ciudad requerida.',
            'sex.required' => 'Sexo requerido.',
            'birthday.required' => 'Fecha de nacimiento requerida.',
            'address.required' => 'Direccion requerida.',
            'phone.required' => 'Telefono requerido.',
	        'phone.unique:terceros,telefono' => 'El número de telefono ya existe.',
            'password.confirmed' => 'Se requiere confirmar las contraseñas.',
        ];

        $validator = Validator::make($request->all(), [
            'first-name' => 'required',
            'last-name' => 'required',
            'type_client' => 'required',
            'type_dni' => 'required',
            'dni' => 'required',
            'city' => 'required',
            'sex' => 'required',
            'birthday' => 'required',
            'address' => 'required',
            'phone' => 'required|unique:terceros,telefono',
            'code' => 'required|string|exists:terceros,identificacion',
            'email' => 'required|email|unique:terceros,email',
            'password' => 'required|min:3|confirmed',
            'password_confirmation' => 'required|min:3'
        ], $messages);

        if ($validator->fails()) {

            return redirect('/')
                ->withErrors($validator)
                ->withInput();
        }

        $usuario = new Tercero();
        $usuario->nombres = strtolower($request['first-name']);
        $usuario->apellidos = strtolower($request['last-name']);
        $usuario->direccion = strtolower($request->address);
        $usuario->telefono = strtolower($request->phone);
        $usuario->email = strtolower($request->email);
        $usuario->usuario = strtolower($request->email);
        $usuario->contraseña = bcrypt($request->password);
        $usuario->tipo_id = ($request->type_client == 83) ? 2 : 1;
        $usuario->ciudad_id = strtolower($request->city);
        $usuario->celular = strtolower($request->phone);
        $usuario->network_id = 1;
        $usuario->tipo_cliente_id = $request->type_client;
        $usuario->documento_id = $request->type_dni;
        $usuario->identificacion = strtolower($request->dni);
        $usuario->sexo = strtolower($request->sex);
        $usuario->fecha_nacimiento = Carbon::createFromFormat('d/m/Y', $request->birthday);

        if ($request->has('contract')) {

            ($request->contract == 'on') ? $usuario->contrato = true : false;
        }

        if ($request->has('terms')) {
            ($request->terms == 'on') ? $usuario->condiciones = true : false;
        }
        if ($request->has('acount')) {
            (isset($request->acount)) ? $usuario->acount = $request->acount: null;
        }


        if ($request->file('banco')) {
            $cuenta        = $request->file('banco');
            $cuenta_nombre = str_random(30) . "." . $cuenta->getClientOriginalExtension();
            $path          = public_path() . "/uploads";
            $cuenta->move($path, $cuenta_nombre);
            $usuario->cuenta = "uploads/" . $cuenta_nombre;
        }

        if ($request->file('cedula')) {

            $cuenta        = $request->file('cedula');
            $cuenta_nombre = str_random(30) . "." . $cuenta->getClientOriginalExtension();
            $path          = public_path() . "/uploads";
            $cuenta->move($path, $cuenta_nombre);
            $usuario->cedula = "uploads/" . $cuenta_nombre;

        }

        if ($request->file('rut')) {

            $cuenta        = $request->file('rut');
            $cuenta_nombre = str_random(30) . "." . $cuenta->getClientOriginalExtension();
            $path          = public_path() . "/uploads";
            $cuenta->move($path, $cuenta_nombre);
            $usuario->rut = "uploads/" . $cuenta_nombre;

        }

        if ($request->has('prime')) {
            $usuario->primes()->create([
                'fecha_inicio' => Carbon::now(),
                'fecha_final' => Carbon::now()->addMonth(),
                'log' => [
                    'id' => $request->getClientIp(),
                    'browser' => $request->header('User-Agent')
                ]
            ]);
        }

        $usuario->save();

        $city = Ciudad::find($request->city);

        if (count($usuario) > 0) {

            $api_url = 'https://'. env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');
            $client = new \GuzzleHttp\Client();

            $res = $client->request('post', $api_url . '/admin/customers.json', array(
                    'form_params' => array(
                        'customer' => array(
                            'first_name' => strtolower($request['first-name']),
                            'last_name' => strtolower($request['last-name']),
                            'email' => strtolower($request->email),
                            'verified_email' => true,
                            'addresses' => [

                                [
                                    'address1' => strtolower($request->address),
                                    'city' => strtolower($city->nombre),
                                    'province' => '',

                                    "zip" => '',
                                    'first_name' => strtolower($request['first-name']),
                                    'last_name' => strtolower($request['last-name']),
                                    'country' => 'CO'
                                ],

                            ],
                            "password" => $request->password,
                            "password_confirmation" => $request->password_confirmation,
                            'send_email_invite' => false,
                            'send_email_welcome' => false
                        )
                    )
                )
            );

            $customer = json_decode($res->getBody(), true);

            $search = Tercero::find($usuario->id);

            $search->customer_id = $customer['customer']['id'];

            $search->save();

        }

        $padre = Tercero::where('identificacion', $request->code)->first();

        if (count($padre) > 0 ) {

            if ($padre->tipo_cliente_id == 83) {

                $result = DB::table('terceros_networks')
                    ->where('customer_id', $usuario->id)
                    ->where('network_id', 1)
                    ->where('padre_id', $padre->id)
                    ->get();

                if (count($result) == 0) {
                    $usuario->networks()->attach(1, ['padre_id' => $padre->id]);
                }


            } else {

                $result = DB::table('terceros_networks')
                    ->where('customer_id', $usuario->id)
                    ->where('network_id', 1)
                    ->where('padre_id', 1)
                    ->get();

                if (count($result) == 0) {
                    $usuario->networks()->attach(1, ['padre_id' => 1]);
                }
            }

        } else {

            $result = DB::table('terceros_networks')
                ->where('customer_id', $usuario->id)
                ->where('network_id', 1)
                ->where('padre_id', 1)
                ->get();

            if (count($result) == 0) {

                $usuario->networks()->attach(1, ['padre_id' => 1]);
            }
        }

        /*if ($usuario) {
            \Auth::login($usuario);
            return redirect()->route('admin.index');
        }*/

        return redirect()->route('login')->with(['message' => 'Felicitaciones, has sido registrado correctamente.']);
    }
}
