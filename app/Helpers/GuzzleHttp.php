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

    public static function url_test()
    {
        //return 'https://'. env('API_KEY_TEST') . ':' . env('API_PASSWORD_TEST') . '@' . env('API_SHOP_TEST');
        return 'https://c17edef9514920c1d2a6aeaf9066b150:afc86df7e11dcbe0ab414fa158ac1767@mall-hello.myshopify.com';  // api hello
    }

}