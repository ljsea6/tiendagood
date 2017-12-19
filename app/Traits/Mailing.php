<?php
/**
 * Created by PhpStorm.
 * User: desarrollo
 * Date: 19/12/17
 * Time: 09:51 AM
 */

namespace App\Traits;

use Mail;


trait Mailing
{

    public function mail($user, $msg)
    {

        Mail::send('send.notifications', [
            'user' => $user,
            'msg' => $msg,
        ], function ($m)  use ($user, $msg) {
            $m->from('info@tiendagood.com', 'Tienda Good');
            $m->to($user->email, $user->name)->subject($msg);
        });
    }
}