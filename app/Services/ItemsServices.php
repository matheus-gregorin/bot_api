<?php

namespace App\Services;

use App\Entitys\ItemEntity;
use App\Repository\ItemsRepository;
use App\Repository\MerchantsRepository;
use Carbon\Carbon;
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
        $merchant = $this->merchantsRepository->getByUuid($data['merchant_uuid']);

        if($merchant){
            if($data['qtd_item'] > 0){

                $item = new ItemEntity(
                    Uuid::uuid4()->toString(),
                    $data['merchant_uuid'],
                    $data['name_item'],
                    $data['qtd_item'],
                    $data['value'],
                    $data['is_promotion'],
                    Carbon::now(),
                    Carbon::now()
                );

                return $this->itemsRepository->create($item->toArray(false));
            }

            return throw new Exception("Quantity not permited");
        }

        return throw new Exception("Merchant not found");
    }

    public function updated(string $uuid, array $data)
    {
        $item = $this->itemsRepository->getBytUuid($uuid);
        if($item){

            if(!empty($data['name_item']) && $data['name_item'] != $item->getNameItem()){
                $item->setNameItem($data['name_item']);
            }

            if(!empty($data['qtd_item']) && $data['qtd_item'] != $item->getQtdItem()){
                $item->setQtdItem($data['qtd_item']);
            }

            if(!empty($data['value']) && $data['value'] != $item->getValue()){
                $item->setValue($data['value']);
            }

            if(isset($data['is_promotion']) && $data['is_promotion'] != $item->getIsPromotion()){
                $item->setIsPromotion($data['is_promotion']);
            }

            $update = $this->itemsRepository->update($item->getUuid(), $item->toArray(false));
            if($update){
                return $item->toArray(true);
            }

            throw new Exception("Item can not update", 500);

        }

        throw new Exception("Item not found", 400);
    }

    public function deleted(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $item = $this->itemsRepository->getBytUuid($uuid);
        if($item){
            return $this->itemsRepository->deleted($item->getUuid());
        }

        throw new Exception("Item not found");
    }

    public function get(string $uuid, array $data)
    {

        $item = $this->itemsRepository->getBytUuid($uuid);
        if($item){
            return $item->toArray(true);
        }

        throw new Exception("Item not found");
    }

    public function allByMerchant(string $merchantUuid, array $data)
    {
        $merchant = $this->merchantsRepository->getByUuid($merchantUuid);
        if($merchant){
            return $this->itemsRepository->allByMerchant($merchantUuid, $data);
        }
        throw new Exception("Merchant not found");

    }

    public function all(array $data)
    {
        return $this->itemsRepository->all( $data);
    }
}
