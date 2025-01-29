<?php

namespace App\Jobs;

use App\Entitys\ClientEntity;
use App\Entitys\ListOfPurchaseEntity;
use App\Mail\SendEmailConfirmListMail;
use App\Models\Clients;
use App\Models\ListOfPurchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailConfirmList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ListOfPurchaseEntity $listOfPurchase;
    private ClientEntity $client;
    private array $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( ListOfPurchaseEntity $listOfPurchase, ClientEntity $client, array $data)
    {
        $this->listOfPurchase = $listOfPurchase;
        $this->client = $client;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            Log::info("Purchase:", [
                    $this->client->getName(),
                    json_encode($this->listOfPurchase->toArray(true)),  
                    json_encode($this->data)
                ]
            );
            Mail::to($this->client->getEmail())->send(new SendEmailConfirmListMail(
            $this->listOfPurchase->toArray(true), 
                    $this->client->toArray(true), 
                    $this->data
                )
            );
            Log::info("Send email confirm list", []);

        } catch (\Exception $e) {
            Log::error("Send email confirm list error", ['message' => $e->getMessage()]);
        }
    }
}
