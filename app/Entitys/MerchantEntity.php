<?php

namespace App\Entitys;

class MerchantEntity
{
    private string $uuid;

    private string $name;

    private string $telephone;

    private array $address;

    private bool $areOpen;

    private string $updated_at;

    private string $created_at;

    public function __construct(
        string $uuid,
        string $name,
        string $telephone,
        array $address,
        bool $areOpen,
        string $updated_at,
        string $created_at
    ) 
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->telephone = $telephone;
        $this->address = $address;
        $this->areOpen = $areOpen;
        
        $this->updated_at = $updated_at;
        $this->created_at = $created_at;
    }

    /**
     * Get the value of uuid
     */ 
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set the value of uuid
     *
     * @return  self
     */ 
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of telephone
     */ 
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     *
     * @return  self
     */ 
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get the value of address
     */ 
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @return  self
     */ 
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of areOpen
     */ 
    public function getAreOpen()
    {
        return $this->areOpen;
    }

    /**
     * Set the value of areOpen
     *
     * @return  self
     */ 
    public function setAreOpen($areOpen)
    {
        $this->areOpen = $areOpen;

        return $this;
    }

    /**
     * Get the value of updated_at
     */ 
    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreated_at()
    {
        return $this->created_at;
    }

    public function toArray(bool $returnDates = false)
    {        
        //Inserir no banco: $returnDates = false
        //Listagem de operadores: $returnDates = true
        //Não precisa mandar essas informações de crated_at e updated_at para o banco
        //pois ele mesmo se auto-gerência.

        $data = [
            'uuid' => $this->getUuid(),
            'name'=> $this->getName(),
            'telephone' => $this->getTelephone(),
            'address' => $this->getAddress(),
            'are_open'=> $this->getAreOpen(),
        ];

        if($returnDates){
            $data['created_at'] = $this->getCreated_at();
            $data['updated_at'] = $this->getUpdated_at();
        }

        return $data;
    }
}
