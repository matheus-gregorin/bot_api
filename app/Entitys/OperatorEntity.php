<?php

namespace App\Entitys;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Date;

class OperatorEntity
{

    private string $uuid;

    private string $name;

    private string $email;

    private string $password;

    private array $permissions;

    private string $status;

    private string $updated_at;

    private string $created_at;

    public function __construct(
        string $uuid,
        string $name,
        string $email,
        string $password,
        array $permissions,
        string $status,
        string $updated_at,
        string $created_at
    ) 
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->permissions = $permissions;
        $this->status = $status;

        
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
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of permissions
     */ 
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set the value of permissions
     *
     * @return  self
     */ 
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;

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
        $data = [
            'uuid' => $this->getUuid(),
            'name'=> $this->getName(),
            'email' => $this->getEmail(),
            'password' => !empty($this->getPassword()) ? $this->getPassword(): "",
            'permissions'=> $this->getPermissions(),
            'status' => $this->getStatus()
        ];

        if($returnDates){
            $data['created_at'] = $this->getCreated_at();
            $data['updated_at'] = $this->getUpdated_at();
        }
        return $data;
    }
}
