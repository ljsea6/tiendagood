<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class SetTransactions extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para guardar las transacciones de las tiendas de good y mercardo y gurdarlas en la tabla transactions del backoffice';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {



        $ordenes = DB::table('orders')
                ->selectRaw('id,shop')
                ->whereRaw("financial_status = 'paid'")
                ->get();
        
        $this->info($ordenes);

        $client = new \GuzzleHttp\Client();

        foreach ($ordenes as $orden) {
            if ($orden->shop != NULL) {

                if ($orden->shop == 'good') {
                    $api = 'https://' . env('API_KEY_SHOPIFY') . ':' . env('API_PASSWORD_SHOPIFY') . '@' . env('API_SHOP');
                    $this->info('isgood');
                } elseif ($orden->shop == 'mercando') {
                    $api = 'https://' . env('API_KEY_MERCANDO') . ':' . env('API_PASSWORD_MERCANDO') . '@' . env('API_SHOP_MERCANDO');
                    $this->info('ismercando');
                }

                $shop = $client->request('GET', $api . "/admin/orders/$orden->id/transactions.json");
                $headers = $shop->getHeaders()['X-Shopify-Shop-Api-Call-Limit'];
                $x = explode('/', $headers[0]);
                $diferencia = $x[1] - $x[0];
                if ($diferencia < 20) {
                    usleep(10000000);
                }

                $results = json_decode($shop->getBody(), true);
                $transactions = $results['transactions'];
                if (count($transactions) > 0) {
                    foreach ($transactions as $transaction) {
                        $this->info('TRANSACTIOS # ' . $transaction['id']);
                    }
                }
            }
        }
    }

}
