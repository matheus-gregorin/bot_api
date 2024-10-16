<?php

namespace App\Repository;

use App\Models\ListOfPurchase;
use Exception;

class ListOfPurchaseRepository
{
    private ListOfPurchase $listOfPurchaseModel;

    public function __construct(ListOfPurchase $listOfPurchaseModel) {
        $this->listOfPurchaseModel = $listOfPurchaseModel;
    }

    public function create(ListOfPurchase $listOfPurchaseModel)
    {
        try{

            return $listOfPurchaseModel->save();

        } catch (Exception $e){
            throw new Exception("Error in create operator - " . $e->getMessage(), 400);
        }
    }

    public function getByUuid(string $uuid)
    {
        try{

            return $this->listOfPurchaseModel->where('uuid', $uuid)->get()->first();

        } catch (Exception $e){
            throw new Exception("Error in get by uuid list - " . $e->getMessage(), 400);
        }
    }

    public function updatedItems($uuid, array $items)
    {
        try{

            $this->listOfPurchaseModel->where("uuid", $uuid)->update(['items' => $items]) ;

        } catch (Exception $e){
            throw new Exception("Error in updated items - " . $e->getMessage(), 400);
        }
    }

    public function getAll(array $data)
    {
        try{

            $list = [];
            $query = $this->listOfPurchaseModel::query();
            if(!empty($data['paginator'])){
                $pages = $query->paginate($data['paginator']);
            } else {
                return $this->listOfPurchaseModel->all();
            }

            foreach ($pages as $listOfPurchase){

                $createAt = $listOfPurchase->created_at;
                $updatedAt = $listOfPurchase->updated_at;

                $list[] = [
                    'uuid' => $listOfPurchase->uuid,
                    'client_uuid' => $listOfPurchase->client_uuid,
                    'items' => $listOfPurchase->items,
                    'value' => $listOfPurchase->value,
                    'date_schedule' => $listOfPurchase->date_schedule,
                    'form_purchase' => $listOfPurchase->form_purchase,
                    'address_send' => $listOfPurchase->address_send,
                    'status'=> $listOfPurchase->status,
                    'created_at' => $createAt->toDateTimeString(),
                    'updated_at' => $updatedAt->toDateTimeString(),
                ];
            }

            return $list;

        } catch (Exception $e){
            throw new Exception("Error in get all items - " . $e->getMessage(), 400);
        }
    }

    public function deleted(string $uuid)
    {
        try{

            return $this->listOfPurchaseModel->where('uuid', $uuid)->delete();
            
        } catch (Exception $e){
            throw new Exception("Error in delete list of purchase, uuid: " . $uuid, 400);
        }
    }

    public function allListsByClientUuid(string $uuid)
    {
        try{

            return $this->listOfPurchaseModel->where('client_uuid', $uuid)->get();
            
        } catch (Exception $e){
            throw new Exception("Error in delete list of purchase, uuid: " . $uuid, 400);
        }
    }

    public function allListsByMerchantUuid(string $uuid)
    {
        try{

            $query = $this->listOfPurchaseModel::query();

            return $query->where('items.' . $uuid, 'exists', true)->get();
            
        } catch (Exception $e){
            throw new Exception("Error in get by merchant uuid list of purchase, uuid: " . $uuid . " - " . $e->getMessage(), 400);
        }
    }

    public function updateListItems(string $uuid, $valueList, array $itemsList)
    {
        try{

            $listOfPurchase = $this->listOfPurchaseModel->where("uuid", $uuid)->get()->first();
            if($listOfPurchase){
                $listOfPurchase->value = $valueList;
                $listOfPurchase->items = $itemsList;
                return $listOfPurchase->save();
            }

            throw new Exception("List not found");
            
        } catch (Exception $e){
            throw new Exception("Error in add items in list of purchase, uuid: " . $uuid . " - " . $e->getMessage(), 400);
        }
    }
}
