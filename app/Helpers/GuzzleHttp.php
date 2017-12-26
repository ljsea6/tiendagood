<?php
/**
 * Created by PhpStorm.
 * User: desarrollo
 * Date: 26/12/17
 * Time: 09:46 AM
 */

namespace App\Helpers;


class GuzzleHttp
{
    public static function client()
    {
        return  new \GuzzleHttp\Client();
    }

}