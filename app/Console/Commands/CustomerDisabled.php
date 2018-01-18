<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\GuzzleHttp;

class CustomerDisabled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:disabled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para desabilitar customer en tienda good y en mercando';

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
        $data = array(
            'state' => 'enable'
        );

        GuzzleHttp::api_usuarios('good', 'ljsea6@gmail.com', $data, 'actualizar');
        GuzzleHttp::api_usuarios('mercando', 'ljsea6@gmail.com', $data, 'actualizar');

        $this->info('Los clientes han sido desavilitados');
    }
}
