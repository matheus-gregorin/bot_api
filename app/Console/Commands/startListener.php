<?php

namespace App\Console\Commands;

use App\Listeners\StoreOrderReceivedWorker;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class startListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start:listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia a leitura da fila do rabbitMq - Fila: store-services.orders.received';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        try{

            Log::info("Iniciando a leitura", []);
            (new StoreOrderReceivedWorker)->subscribe();

        } catch (Exception $e){
            Log::critical("Erro ao consumir o Rabbit", ["msg" => $e->getMessage()]);
        }
    }
}
