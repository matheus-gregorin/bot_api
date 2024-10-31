<?php

namespace App\Jobs;

use App\Models\Operators;
use App\Repository\OperatorsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $userUuid;
    private OperatorsRepository $operators;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $uuid)
    {
        $this->userUuid = $uuid;
        $this->operators = new OperatorsRepository(new Operators());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            Log::channel('stderr')->info("Job iniciado.");
            $user = $this->operators->getByUuid($this->userUuid);
            $email = Mail::to($user->email)->send(new \App\Mail\SendWelcomeEmail($user));
            Log::info("Send email operator", ['user' => $user->uuid, 'email'=> $email]);

        } catch (\Exception $e) {
            Log::error("Send email operator error", ['message' => $e->getMessage()]);
        }
    }
}
