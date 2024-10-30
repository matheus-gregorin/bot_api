<?php

namespace App\Repository;

use App\Models\Items;
use Exception;

class ItemsRepository
{
    private Items $itemsModel;

    public function __construct(Items $itemsModel) {
        $this->itemsModel = $itemsModel;
    }

    public function create(array $data)
    {
        try{

            return $this->itemsModel->create($data);

        } catch (Exception $e){
            throw new Exception("Error in create items - " . $e->getMessage());
        }
    }

    public function getBytUuid(string $uuid)
    {
        try{

            return $this->itemsModel->where('uuid', $uuid)->first();

        } catch (Exception $e){
            throw new Exception("Error in get items - " . $e->getMessage());
        }
    }

    public function deleted(string $uuid)
    {
        try{

            return $this->itemsModel->where('uuid', $uuid)->delete();

        } catch (Exception $e){
            throw new Exception("Error in delete items - " . $e->getMessage());
        }
    }

    public function allByMerchant(string $uuid)
    {
        try{

            return $this->itemsModel->where('merchant_uuid', $uuid)->get();

        } catch (Exception $e){
            throw new Exception("Error in delete items - " . $e->getMessage());
        }
    }

    public function removeQtd(string $uuid, int $qtd)
    {
        try{

            $item = $this->itemsModel->where('uuid', $uuid)->get()->first();
            if($item){
                $item->qtd_item -= $qtd;
                return $item->save();
            }

        } catch (Exception $e){
            throw new Exception("Error in update qtd item (REMOVE) - " . $e->getMessage());
        }
    }

    public function addQtd(string $uuid, int $qtd)
    {
        try{

            $item = $this->itemsModel->where('uuid', $uuid)->get()->first();
            if($item){
                $item->qtd_item += $qtd;
                return $item->save();
            }

        } catch (Exception $e){
            throw new Exception("Error in update qtd item (ADD) - " . $e->getMessage());
        }
    }
}
