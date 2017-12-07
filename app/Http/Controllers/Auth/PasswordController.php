<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Entities\Tercero;
use Illuminate\Http\Request;
use Mail;
use Hash;
use Session;
//use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordController extends Controller {

    /**
     * |--------------------------------------------------------------------------
     * | Password Reset Controller
     * |--------------------------------------------------------------------------
     * |
     * | This controller is responsible for handling password reset requests
     * | and uses a simple trait to include this behavior. You're free to
     * | explore this trait and override any methods you wish to tweak.
     * |
     */

   // use ResetsPasswords;
  //  protected $redirectTo = '/dashboard';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */


    public function getEmail() {
        return view('auth.password', compact('nivel'));
    }

    public function postEmail(Request $request) {
        $email = Tercero::where('email', strtolower($request->email))->first();

        if($email){  
           $token = str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789".uniqid());

            $usuario = Tercero::findOrFail($email['id']);
            $usuario->remember_token = $token;
            $usuario->save();
       
            Mail::send('auth.reset_password', ['token' => $token], function($message) use ($request) {
                $message->from('info@tiendagood.com', 'Recupera tu contraseña');
                $message->subject('Recupera tu contraseña');
                $message->to($request->email);
            }); 

            Session::flash('flash_msg', 'Revise su correo para saber como puede cambiar su contrase\u00f1a');
             return redirect()->action('Auth\PasswordController@getEmail');
        }
      // return view('auth.password', compact('nivel'));
    }

    public function getReset($token) {
       $remember_token = Tercero::where('remember_token', $token)->first();
        if($remember_token != ''){ 
            $email = $remember_token['email'];
            $id = $remember_token['id'];
          return view('auth.reset', compact('token', 'email', 'id'));
        }
        else{
             return view('auth.login', compact('nivel'));
        }
    }

    public function postReset(Request $request) { 
       $remember_token = Tercero::where('remember_token', $request->token)->first();
        if($remember_token != ''){ 
            $email = $remember_token['email'];
            $id = $remember_token['id'];

            $usuario = Tercero::findOrFail($remember_token['id']);
            $usuario->contraseña = bcrypt($request->password);
            $usuario->remember_token = '';
            $usuario->save();
            //cambio de clave
            Session::flash('flash_msg', 'El cambio de contrase\u00f1a se realizo correctamente');
             return redirect()->action('Auth\AuthController@getLogin');
        }
        else{
             return view('auth.login', compact('nivel'));
        }
    }
/*

    public function __construct() {
        $this->middleware('guest');
    }

    public function redirectPath() {
        return route('admin');
    }

    protected function getEmailSubject() {
        return 'Recupera tu contraseñad';
    }
 */

}
