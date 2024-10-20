<?php

namespace App\Services;

use App\Repository\ItemsRepository;
use App\Repository\MerchantsRepository;
use Exception;
use Ramsey\Uuid\Uuid;

class ItemsServices
{
    private ItemsRepository $itemsRepository;
    private MerchantsRepository $merchantsRepository;

    public function __construct(ItemsRepository $itemsRepository, MerchantsRepository $merchantsRepository) {
        $this->itemsRepository = $itemsRepository;
        $this->merchantsRepository = $merchantsRepository;
    }

    public function create(array $data)
    {
        $data = ["uuid" => Uuid::uuid4()->toString()] + $data;
        $merchant = $this->merchantsRepository->getByUuid($data['merchant_uuid']);

        if($merchant){
            if($data['qtd_item'] > 0){
                return $this->itemsRepository->create($data);
            }

            return throw new Exception("Quantity not permited");
        }

        return throw new Exception("Merchant not found");
    }

    public function updated(string $uuid, array $data)
    {
        $item = $this->itemsRepository->getBytUuid($uuid);
        if($item){

            if(!empty($data['name_item']) && $data['name_item'] != $item->name_item){
                $item->name_item = $data['name_item'];
            }

            if(!empty($data['qtd_item']) && $data['qtd_item'] != $item->qtd_item){
                $item->qtd_item = $data['qtd_item'];
            }

            if(!empty($data['value']) && $data['value'] != $item->value){
                $item->value = $data['value'];
            }

            if(isset($data['is_promotion']) && $data['is_promotion'] != $item->is_promotion){
                $item->is_promotion = $data['is_promotion'];
            }

            return $item->save();
        }

        throw new Exception("Item not found");
    }

    public function deleted(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $item = $this->itemsRepository->getBytUuid($uuid);
        if($item){
            return $this->itemsRepository->deleted($item->uuid);
        }

        throw new Exception("Item not found");
    }

    public function get(string $uuid, array $data)
    {

        $item = $this->itemsRepository->getBytUuid($uuid);
        if($item){
            return $item;
        }

        throw new Exception("Item not found");
    }

    public function allByMerchant(string $merchantUuid, array $data)
    {

        $merchant = $this->merchantsRepository->getByUuid($merchantUuid);
        if($merchant){
            $items = $this->itemsRepository->allByMerchant($merchantUuid);
            if($items){
                return $items;
            }

            throw new Exception("items not found");

        }

        throw new Exception("Merchant not found");
    }
}
