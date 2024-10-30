<?php

namespace App\Mail;

use App\Models\Clients;
use App\Models\ListOfPurchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmailConfirmListMail extends Mailable
{
    use Queueable, SerializesModels;

    private ListOfPurchase $listOfPurchase;
    private Clients $client;
    private array $items;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( ListOfPurchase $listOfPurchase, Clients $client, array $items)
    {
        $this->listOfPurchase = $listOfPurchase;
        $this->client = $client;
        $this->items = $items;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Confirmação de compra na BOTT',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.SendConfirmListPurchase',
            with: [
                'list' => $this->listOfPurchase,
                'client'=> $this->client,
                'items' => $this->items
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
