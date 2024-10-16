<?php

namespace App\Services;

use App\Models\ListOfPurchase;
use App\Repository\ClientsRepository;
use App\Repository\ItemsRepository;
use App\Repository\ListOfPurchaseRepository;
use App\Repository\MerchantsRepository;
use Exception;
use Ramsey\Uuid\Uuid;

class ListOfPurchaseServices
{
    private ListOfPurchaseRepository $listOfPurchaseRepository;
    private ClientsRepository $clientRepository;
    private MerchantsRepository $merchantRepository;
    private ItemsRepository $itemsRepository;

    public function __construct(
        ListOfPurchaseRepository $listOfPurchaseRepository,
        ClientsRepository $clientRepository,
        MerchantsRepository $merchantRepository,
        ItemsRepository $itemsRepository
        ) {

        $this->listOfPurchaseRepository = $listOfPurchaseRepository;
        $this->clientRepository = $clientRepository;
        $this->merchantRepository = $merchantRepository;
        $this->itemsRepository = $itemsRepository;
    }

    public function create(array $data)
    {
        $client = $this->clientRepository->getByUUid($data['client_uuid']);
        if($client){
            $listOfPurchase = new ListOfPurchase();
            $listOfPurchase->uuid = Uuid::uuid4()->toString();
            $listOfPurchase->client_uuid = $data['client_uuid'];

            if(!empty($data['items'])){
                $listOfPurchase->items = $data['items'];
                
            } else {
                throw new Exception("items not found");
            }

            if(!empty($data['date_schedule'])){
                $listOfPurchase->date_schedule = $data['date_schedule'];
            } else {
                throw new Exception("date schedule not found");
            }

            if(!empty($data['form_purchase'])){
                $listOfPurchase->form_purchase = $data['form_purchase'];
            } else {
                throw new Exception("form purchase not found");
            }

            if(!empty($data['address_send'])){
                $listOfPurchase->address_send = $data['address_send'];
            } else {
                throw new Exception("address send not found");
            }

            $listOfPurchase->status = "AWAIT";

            $valueList = $this->calculateValueList($data, null, true);
            $listOfPurchase->value = $valueList;

            return $this->listOfPurchaseRepository->create($listOfPurchase);
        }

        throw new Exception("Client not found");
    }

    public function updateListItems(string $uuid, array $data)
    {

        $listOfPurchase = $this->listOfPurchaseRepository->getByUuid($uuid);
        if($listOfPurchase){
            $itemsList = $listOfPurchase->items;
            $itemsAdd = $data["items"];

            foreach($itemsList as $merchantUuid => $items){
                foreach($itemsAdd as $merchantUuidAdd => $itemAdd){

                    $newMerchantAdd[$merchantUuidAdd] = ["need_create" => true];

                    if($merchantUuid == $merchantUuidAdd){
                        $newMerchantAdd[$merchantUuidAdd]['need_create'] = false;
                        
                        foreach ($itemsList[$merchantUuid] as $uuidItem => $qtd) {
                            if(!empty($itemsAdd[$merchantUuid][$uuidItem])){
                                if(!empty($itemsAdd[$merchantUuid][$uuidItem]) > 0){
                                    $itemsList[$merchantUuid][$uuidItem] = $itemsAdd[$merchantUuid][$uuidItem];
                                }

                            } else {
                                $itemsList[$merchantUuid] = $itemsList[$merchantUuid] + $itemAdd;
                            }
                        }
                    }
                }
            }

            foreach($newMerchantAdd as $merchantUuidAddNow => $needCreate){
                if($needCreate['need_create']){
                    $itemsList = $itemsList + [$merchantUuidAddNow => $itemsAdd[$merchantUuidAddNow]];
                }
            }

            $valueList = $this->calculateValueList(["items" => $itemsList], $listOfPurchase->items);
            return $this->listOfPurchaseRepository->updateListItems($uuid, $valueList, $itemsList);

        }

        throw new Exception("List of purchase not found");
    }

    public function deleted(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $listOfPurchase = $this->listOfPurchaseRepository->getByUuid($uuid);
        if($listOfPurchase){
            return $this->listOfPurchaseRepository->deleted($listOfPurchase->uuid);
        }

        throw new Exception("List of purchase not found");
    }

    public function getAll(array $data)
    {
        $list = $this->listOfPurchaseRepository->getAll($data);
        if($list){
            return $list;
        }

        throw new Exception("List is null");
    }

    public function allByClient(string $clientUuid, array $data)
    {

        $client = $this->clientRepository->getByUUid($clientUuid);
        if($client){

            $lists = $this->listOfPurchaseRepository->allListsByClientUuid($clientUuid);
            if($lists){
                return $lists;
            }

            throw new Exception("List not found by client uuid - " . $clientUuid);
        }

        throw new Exception("Client not found, uuid - " . $clientUuid);
    }

    public function allByMerchant(string $merchantUuid, array $data)
    {

        $merchant = $this->merchantRepository->getByUUid($merchantUuid);
        if($merchant){

            $lists = $this->listOfPurchaseRepository->allListsByMerchantUuid($merchantUuid);
            if($lists){
                return $lists;
            }

            throw new Exception("List not found by merchant uuid - " . $merchantUuid);
        }

        throw new Exception("Merchant not found, uuid - " . $merchantUuid);
    }

    public function calculateValueList(array $data, ?array $listCurrent = null, ?bool $isCreate = false)
    {

        try {
            $valueList = 0.00;
            foreach($data['items'] as $merchantUuid => $items){
                $itemsMerchant = $this->itemsRepository->allByMerchant($merchantUuid);
                if($itemsMerchant){
    
                    foreach($itemsMerchant as $itemMerchant){
                        foreach($items as $uuid_item => $qtd){
    
                            if($uuid_item == $itemMerchant->uuid){
                                if(!$isCreate){
                                    if($qtd < $itemMerchant->qtd_item && $qtd > $listCurrent[$merchantUuid][$uuid_item]){
                                        $itemMerchant->qtd_item = $itemMerchant->qtd_item - $qtd;
        
                                    } else if ($qtd < $itemMerchant->qtd_item && $qtd < $listCurrent[$merchantUuid][$uuid_item]){
                                        $dif = $listCurrent[$merchantUuid][$uuid_item] - $qtd;
                                        $itemMerchant->qtd_item = $itemMerchant->qtd_item + $dif;
                                    }

                                } else {

                                    if($qtd < $itemMerchant->qtd_item){
                                        $itemMerchant->qtd_item = $itemMerchant->qtd_item - $qtd;
        
                                    } else if ($qtd < $itemMerchant->qtd_item){
                                        $dif = $listCurrent[$merchantUuid][$uuid_item] - $qtd;
                                        $itemMerchant->qtd_item = $itemMerchant->qtd_item + $dif;
                                    }

                                }

    
                                $valueList += $itemMerchant->value * $qtd;
                                $itemMerchant->save();
                            }
                        }
    
                    }
    
                } else {
                    throw new Exception("The merchant does not have enough items for sale");
                }
            }

        } catch (Exception $e){
            throw new Exception("The merchant does not have enough items for sale: " . $e->getMessage());
        }

        return $valueList;
    }

    public function deleteItemList(string $litsUuid, string $merchantUuid, string $itemUuid, array $data)
    {
        $listOfPurchase = $this->listOfPurchaseRepository->getByUuid($litsUuid);
        if($listOfPurchase){
            if(!empty($listOfPurchase->items[$merchantUuid])){
                if(!empty($listOfPurchase->items[$merchantUuid][$itemUuid])){

                    $this->returnQuantityOfItems($itemUuid, $listOfPurchase->items[$merchantUuid][$itemUuid]);

                    $itemsList = $listOfPurchase->items;
                    unset($itemsList[$merchantUuid][$itemUuid]);

                    $valueList = $this->calculateValueList(["items" => $itemsList], $listOfPurchase->items);
                    return $this->listOfPurchaseRepository->updateListItems($litsUuid, $valueList, $itemsList);
                }

                throw new Exception("Item not found in list/merchant, list-uuid - " . $litsUuid . " merchant-uuid - " . $merchantUuid . " item-uuid - " . $itemUuid);
            }

            throw new Exception("Merchant not found in list, list-uuid - " . $litsUuid . " merchant-uuid - " . $merchantUuid);
        }

        throw new Exception("List not found, uuid - " . $litsUuid);
    }

    public function returnQuantityOfItems(string $itemUuid, $qtd)
    {
        $item = $this->itemsRepository->getBytUuid($itemUuid);
        if($item){
            return $this->itemsRepository->updateQtd($itemUuid, $qtd);
        }

        throw new Exception("Item not found in update, uuid - " . $itemUuid);
    }

    public function get(string $uuid, array $data)
    {

        $list = $this->listOfPurchaseRepository->getByUUid($uuid);
        if($list){
            return $list;
        }

        throw new Exception("list not found", 404);
    }
}
