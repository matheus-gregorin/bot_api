<?php

namespace App\Listeners;

use App\Services\ListOfPurchaseServices;
use Illuminate\Support\Facades\Log;
use Pablicio\MirabelRabbitmq\RabbitMQWorkersConnection;

class StoreOrderReceivedWorker
{
    use RabbitMQWorkersConnection;
  
    const QUEUE = 'store-services.orders.received',
      routing_keys = [
        'order-services.order.received'
      ],
      options = [
        'exchange_type' => 'topic'
      ],
      retry_options = [
        'x-message-ttl' => 3600,
        'max-attempts' => 4
      ];
  
    public function work($msg)
    {
      try {
        Log::info('Listener send new email', ['message' => $msg->body]);
        return $this->ack($msg);

      } catch (\Exception $e) {
        print_r("Error:", $msg->body);  
        return $this->nack($msg);
      }
    }
  }
