<?php

namespace App\Entitys;

class ItemEntity
{

    private string $uuid;
    
    private string $merchantUuid;

    private string $nameItem;

    private int $qtdItem;

    private float $value;

    private bool $isPromotion;

    private string $updatedAt;

    private string $createdAt;

    public function __construct(
    string $uuid,
    string $merchantUuid,
    string $nameItem,
    int $qtdItem,
    $value,
    bool $isPromotion,
    string $updatedAt,
    string $createdAt
    )
    {
        $this->uuid = $uuid;
        $this->merchantUuid = $merchantUuid;
        $this->nameItem = $nameItem;
        $this->qtdItem = $qtdItem;
        $this->value = $value;
        $this->isPromotion = $isPromotion;
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
     * Get the value of merchantUuid
     */ 
    public function getMerchantUuid()
    {
        return $this->merchantUuid;
    }

    /**
     * Set the value of merchantUuid
     *
     * @return  self
     */ 
    public function setMerchantUuid($merchantUuid)
    {
        $this->merchantUuid = $merchantUuid;

        return $this;
    }

    /**
     * Get the value of nameItem
     */ 
    public function getNameItem()
    {
        return $this->nameItem;
    }

    /**
     * Set the value of nameItem
     *
     * @return  self
     */ 
    public function setNameItem($nameItem)
    {
        $this->nameItem = $nameItem;

        return $this;
    }

    /**
     * Get the value of qtdItem
     */ 
    public function getQtdItem()
    {
        return $this->qtdItem;
    }

    /**
     * Set the value of qtdItem
     *
     * @return  self
     */ 
    public function setQtdItem($qtdItem)
    {
        $this->qtdItem = $qtdItem;

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
     * Get the value of isPromotion
     */ 
    public function getIsPromotion()
    {
        return $this->isPromotion;
    }

    /**
     * Set the value of isPromotion
     *
     * @return  self
     */ 
    public function setIsPromotion($isPromotion)
    {
        $this->isPromotion = $isPromotion;

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
     * Get the value of createAt
     */ 
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function toArray(bool $returnDates = false)
    {
        $data = [
            'uuid' => $this->getUuid(),
            'merchant_uuid'=> $this->getMerchantUuid(),
            'name_item' => $this->getNameItem(),
            'qtd_item' => $this->getQtdItem(),
            'value'=> $this->getValue(),
            'is_promotion'=> $this->getIsPromotion()
        ];

        if($returnDates){
            $data['created_at'] = $this->getCreatedAt();
            $data['updated_at'] = $this->getupdatedAt();
        }

        return $data;

    }
}
