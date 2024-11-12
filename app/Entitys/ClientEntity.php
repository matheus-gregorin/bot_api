<?php

namespace App\Entitys;

class ClientEntity
{

    private string $uuid;

    private string $name;

    private string $dateOfBirth;

    private string $number;

    private string $email;

    private array $address;

    private bool $activate;

    private string $updatedAt;

    private string $createdAt;

    public function __construct(
        string $uuid,
        string $name,
        string $dateOfBirth,
        string $number,
        string $email,
        array $address,
        bool $activate,
        string $updatedAt,
        string $createdAt
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->dateOfBirth = $dateOfBirth;
        $this->number = $number;
        $this->email = $email;
        $this->address = $address;
        $this->activate = $activate;
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
     * Get the value of dateOfBirth
     */ 
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set the value of dateOfBirth
     *
     * @return  self
     */ 
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get the value of number
     */ 
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set the value of number
     *
     * @return  self
     */ 
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

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
     * Get the value of activate
     */ 
    public function getActivate()
    {
        return $this->activate;
    }

    /**
     * Set the value of activate
     *
     * @return  self
     */ 
    public function setActivate($activate)
    {
        $this->activate = $activate;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */ 
    public function getupdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getcreatedAt()
    {
        return $this->createdAt;
    }

    public function toArray(bool $returnDates = false)
    {        
        //Inserir no banco: $returnDates = false
        //Listagem de operadores: $returnDates = true
        //Não precisa mandar essas informações de crated_at e updatedAt para o banco
        //pois ele mesmo se auto-gerência.

        $data = [
            'uuid' => $this->getUuid(),
            'name'=> $this->getName(),
            'date_of_birth' => $this->getDateOfBirth(),
            'number' => $this->getNumber(),
            'email'=> $this->getEmail(),
            'address'=> $this->getAddress(),
            'activate'=> $this->getActivate(),
        ];

        if($returnDates){
            $data['created_at'] = $this->getcreatedAt();
            $data['updated_at'] = $this->getupdatedAt();
        }

        return $data;
        
    }
}
