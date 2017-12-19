<?php
/**
 * Created by PhpStorm.
 * User: desarrollo
 * Date: 19/12/17
 * Time: 11:09 AM
 */

namespace App\Helpers;

use Mail;

class Helpers {

    public static function send_mails($user, $msg)
    {

        Mail::send('admin.send.notifications', [
            'user' => $user,
            'msg' => $msg,
        ], function ($m)  use ($user, $msg) {
            $m->from('info@tiendagood.com', 'Tienda Good');
            $m->to($user->email, $user->name)->subject($msg);
        });
    }
}


