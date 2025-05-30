<?php

namespace App\Repository;

use App\Entitys\ClientEntity;
use App\Entitys\ListOfPurchaseEntity;
use App\Models\ListOfPurchase;
use Exception;

class ListOfPurchaseRepository
{
    private ListOfPurchase $listOfPurchaseModel;

    public function __construct(ListOfPurchase $listOfPurchaseModel) {
        $this->listOfPurchaseModel = $listOfPurchaseModel;
    }

    public function create(array $data)
    {
        try{

            return $this->listOfPurchaseModel->create($data);

        } catch (Exception $e){
            throw new Exception("Error in create operator - " . $e->getMessage(), 400);
        }
    }

    public function getByUuid(string $uuid)
    {
        try{

            $list = $this->listOfPurchaseModel->where('uuid', $uuid)->first();
            if(!empty($list)){
                return $this->modelToEntity($list);
            }

            return false;
            

        } catch (Exception $e){
            throw new Exception("Error in get by uuid list - " . $e->getMessage(), 400);
        }
    }

    public function getAll(array $data)
    {
        try{

            $list = [];
            $query = $this->listOfPurchaseModel::query();

            if(!empty($data['order_by']) && $data['order_by'] == 'desc'){
                $query->orderBy('created_at', 'desc');
            }

            if(!empty($data['paginator'])){
                $pontoDePartida = $data['paginator'] - 10;
                $pontoFinal = $pontoDePartida + 10;
                $pages = $query->skip($pontoDePartida)->take($pontoFinal)->get();
            } else {
                $pages = $query->get();
            }

            foreach ($pages as $listOfPurchase){
                $listEntity = $this->modelToEntity($listOfPurchase);
                $list[] = $listEntity->toArray(true);
            }

            $list['total'] = $this->listOfPurchaseModel::count();

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
            throw new Exception("Error in get all list of purchase by client, uuid: " . $uuid, 400);
        }
    }

    public function updateListItems(string $uuid, float $valueList, array $itemsList)
    {
        try{

            $listOfPurchaseModel = $this->listOfPurchaseModel->where("uuid", $uuid)->first();
            if($listOfPurchaseModel){
                $listOfPurchase = $this->modelToEntity($listOfPurchaseModel);
                $listOfPurchase->setValue($valueList);
                $listOfPurchase->setItems($itemsList);
                $update = $this->update($listOfPurchase->getUuid(), $listOfPurchase->toArray(false));
                if($update){
                    return $listOfPurchase->toArray(true);
                }

                return false;
            }

            throw new Exception("List not found");
            
        } catch (Exception $e){
            throw new Exception("Error in add items in list of purchase, uuid: " . $uuid . " - " . $e->getMessage(), 400);
        }
    }

    public function update(string $uuid, array $data)
    {
        try{

            return $this->listOfPurchaseModel->where('uuid', $uuid)->update($data);
            
        } catch (Exception $e){
            throw new Exception("Error in updated list of purchase, uuid: " . $uuid, 400);
        }
    }

    public function getBytUuid(string $uuid)
    {
        try{

            $list = $this->listOfPurchaseModel->where('uuid', $uuid)->first();
            if(!empty($list)){
                return $this->modelToEntity($list);
            }

            return false;

        } catch (Exception $e){
            throw new Exception("Error in get list - " . $e->getMessage());
        }
    }

    public function modelToEntity(ListOfPurchase $listOfPurchase)
    {
        if(!empty($listOfPurchase->client)){
            $client = new ClientEntity(
                $listOfPurchase->client['uuid'],
                $listOfPurchase->client['name'],
                $listOfPurchase->client['date_of_birth'],
                $listOfPurchase->client['number'],
                $listOfPurchase->client['email'],
                $listOfPurchase->client['address'],
                $listOfPurchase->client['activate'],
                $listOfPurchase->client['updated_at'],
                $listOfPurchase->client['created_at'],
            );
        } else {
            throw new Exception("Error in client of list", 400);
        }

        return new ListOfPurchaseEntity(
            $listOfPurchase->uuid,
            $client,
            $listOfPurchase->items,
            $listOfPurchase->form_purchase,
            $listOfPurchase->address_send,
            $listOfPurchase->date_schedule,
            $listOfPurchase->status,
            $listOfPurchase->value,
            $listOfPurchase->updated_at,
            $listOfPurchase->created_at,
        );
    }

    public function getAllForGraph(array $data)
    {
        try{

            $list = [];
            $pages = $this->listOfPurchaseModel::all();

            foreach ($pages as $listOfPurchase){
                $listEntity = $this->modelToEntity($listOfPurchase);
                $list[] = $listEntity->toArray(true);
            }

            $list['total'] = $this->listOfPurchaseModel::count();

            return $list;

        } catch (Exception $e){
            throw new Exception("Error in get all items - " . $e->getMessage(), 400);
        }
    }
}
