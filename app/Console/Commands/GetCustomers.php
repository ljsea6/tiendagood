<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Validator;
use App\Customer;
use App\Entities\Network;
use Carbon\Carbon;
use App\Entities\Tercero;
use DB;

class GetCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para obtener todos los clientes de la API shopify';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $api_url = 'https://'. env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');
        $client = new \GuzzleHttp\Client();
        $resa = $client->request('GET', $api_url . '/admin/customers/count.json');
        $countCustomers = json_decode($resa->getBody(), true);
        $this->info('Customers: ' . $countCustomers['count']);

        $result = true;
        $h = 1;

        do {
            $this->info('Entrando al do');

            $res = $client->request('GET', $api_url . '/admin/customers.json?limit=250&&page=' . $h);

            $headers = $res->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
            $x = explode('/', $headers[0]);
            $diferencia = $x[1] - $x[0];
            if ($diferencia < 20) {

                usleep(10000000);

            }

            $results = json_decode($res->getBody(), true);

            foreach ($results['customers'] as $customer) {

                $this->info('Entrando al for');

                $response = Customer::where('network_id', 1)
                    ->where('customer_id', $customer['id'])
                    ->get();

                if(count($response) == 0) {

                    $this->info('Customer no existe');

                    Customer::createCustomer($customer);
                }
            }

            $h++;

            if (count($results['customers']) < 1) {
                $result = false;
            }

            $this->info('Saliendo del do');

        } while($result);

        $this->info('Los clientes han sido descargados correctamente');
    }
}
