<?php

namespace App\Listeners;

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
        print_r("Messagem: $msg->body\n");
        return $this->ack($msg);

      } catch (\Exception $e) {

        print_r("Error: $msg->body\n");  
        return $this->nack($msg);
      }
    }
  }
