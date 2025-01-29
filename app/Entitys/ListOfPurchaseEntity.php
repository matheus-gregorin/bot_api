<?php

namespace App\Entitys;

class ListOfPurchaseEntity
{
    private string $uuid;

    private ClientEntity $client;

    private array $items;

    private string $formPurchase;

    private array $addressSend;

    private string $dateSchedule;

    private string $status;

    private float $value;

    private string $updatedAt;

    private string $createdAt;

    public function __construct(
        string $uuid,
        ClientEntity $client,
        array $items,
        string $formPurchase,
        array $addressSend,
        string $dateSchedule,
        string $status,
        float $value,
        string $updatedAt,
        string $createdAt
    ) 
    {
        $this->uuid = $uuid;
        $this->client = $client;
        $this->items = $items;
        $this->formPurchase = $formPurchase;
        $this->addressSend = $addressSend;
        $this->dateSchedule = $dateSchedule;
        $this->status = $status;
        $this->value = $value;
        $this->updatedAt = $updatedAt;
        $this->createdAt = $createdAt;
    }

    /**
     * Get the value of uuid
     */ 
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Get the value of clientUuid
     */ 
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the value of clientUuid
     *
     * @return  self
     */ 
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get the value of items
     */ 
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set the value of items
     *
     * @return  self
     */ 
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get the value of formPurchase
     */ 
    public function getFormPurchase()
    {
        return $this->formPurchase;
    }

    /**
     * Set the value of formPurchase
     *
     * @return  self
     */ 
    public function setFormPurchase($formPurchase)
    {
        $this->formPurchase = $formPurchase;

        return $this;
    }

    /**
     * Get the value of addressSend
     */ 
    public function getAddressSend()
    {
        return $this->addressSend;
    }

    /**
     * Set the value of addressSend
     *
     * @return  self
     */ 
    public function setAddressSend($addressSend)
    {
        $this->addressSend = $addressSend;

        return $this;
    }

    /**
     * Get the value of dateSchedule
     */ 
    public function getDateSchedule()
    {
        return $this->dateSchedule;
    }

    /**
     * Set the value of dateSchedule
     *
     * @return  self
     */ 
    public function setDateSchedule($dateSchedule)
    {
        $this->dateSchedule = $dateSchedule;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of value
     */ 
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @return  self
     */ 
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */ 
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function toArray(bool $returnDates = false)
    {
        $data = [
            'uuid' => $this->getUuid(),
            'client'=> $this->getClient()->toArray(true),
            'items' => $this->getItems(),
            'form_purchase' => $this->getFormPurchase(),
            'address_send'=> $this->getAddressSend(),
            'date_schedule'=> $this->getDateSchedule(),
            'status'=> $this->getStatus(),
            'value'=> $this->getValue(),
        ];

        if($returnDates){
            $data['created_at'] = $this->getCreatedAt();
            $data['updated_at'] = $this->getupdatedAt();
        }

        return $data;
    }
}
