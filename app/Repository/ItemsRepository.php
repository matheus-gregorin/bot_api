<?php

namespace App\Repository;

use App\Entitys\ItemEntity;
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

            $item = $this->itemsModel->where('uuid', $uuid)->first();
            if(!empty($item)){
                return $this->modelToEntity($item);
            }

            return false;

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

    public function allByMerchant(string $uuid, array $data)
    {
        try{
            $list = [];
            $query = $this->itemsModel::query();

            if(!empty($uuid)){
                $query->where('merchant_uuid', 'LIKE', '%'.$uuid.'%');
            }
    
            if(!empty($data['paginator'])){
                $pages = $query->paginate($data['paginator']);
            } else {
                throw new Exception("Paginator not found");
            }

            foreach($pages as $item){
                $item = $this->modelToEntity($item);
                $list[] = $item->toArray(true);
            }

            $list['total'] = $pages->total();

            return $list;

        } catch (Exception $e){
            throw new Exception("Error in list all items - " . $e->getMessage(), 400);
        }
    }

    public function all(array $data)
    {
        try{
            $list = [];
            $query = $this->itemsModel::query();

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

            foreach($pages as $item){
                $item = $this->modelToEntity($item);
                $list[] = $item->toArray(true);
            }

            $list['total'] = $this->itemsModel::count();

            return $list;

        } catch (Exception $e){
            throw new Exception("Error in list all items - " . $e->getMessage(), 400);
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

    public function update(string $uuid, array $data)
    {
        try{

            return $this->itemsModel->where('uuid', $uuid)->update($data);
            
        } catch (Exception $e){
            throw new Exception("Error in updated item, uuid: " . $uuid, 400);
        }
    }

    public function modelToEntity(Items $item)
    {
        return new ItemEntity(
            $item->uuid,
            $item->merchant_uuid,
            $item->name_item,
            $item->qtd_item,
            $item->value,
            $item->is_promotion,
            $item->updated_at,
            $item->created_at
        );
    }
}
