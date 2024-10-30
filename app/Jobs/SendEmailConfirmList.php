<?php

namespace App\Jobs;

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

    private ListOfPurchase $listOfPurchase;
    private Clients $client;
    private array $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( ListOfPurchase $listOfPurchase, Clients $client, array $data)
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

            Log::info("", [$this->listOfPurchase, $this->client, $this->data]);
            $email = Mail::to($this->client->email)->send(new SendEmailConfirmListMail($this->listOfPurchase, $this->client, $this->data));
            Log::info("Send email operator", ['user' => $this->client->uuid, 'email'=> $this->client->email]);

        } catch (\Exception $e) {
            Log::error("Send email operator error", ['message' => $e->getMessage()]);
        }
    }
}
