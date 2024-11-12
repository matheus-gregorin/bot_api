<?php

namespace App\Repository;

use App\Entitys\MerchantEntity;
use App\Models\Merchants;
use Exception;

class MerchantsRepository
{
    private Merchants $merchantsModel;

    public function __construct(Merchants $merchantsModel) {
        $this->merchantsModel = $merchantsModel;
    }

    public function create(array $data)
    {
        try{

            return $this->merchantsModel->create($data);

        } catch (Exception $e){
            throw new Exception("Error in create merchant - " . $e->getMessage(), 400);
        }
    }

    public function update(string $uuid, array $data)
    {
        try{

            $this->merchantsModel->where('uuid', $uuid)->update($data);
            
        } catch (Exception $e){
            throw new Exception("Error in updated merchant, uuid: " . $uuid, 400);
        }
    }

    public function getByName(string $name)
    {
        try{

            $merchant = $this->merchantsModel->where('name', $name)->first();
            if(!empty($merchant)){
                return $this->modelToEntity($merchant);
            }

            return false;

        } catch (Exception $e){
            throw new Exception("Error in get merchant - " . $e->getMessage(), 400);
        }
    }

    public function getByUuid(string $uuid)
    {
        try{

            $merchant = $this->merchantsModel->where('uuid', $uuid)->first();
            if(!empty($merchant)){
                return $this->modelToEntity($merchant);
            }

            return false;

        } catch (Exception $e){
            throw new Exception("Error in get merchant - " . $e->getMessage(), 400);
        }
    }

    public function deleted(string $uuid)
    {
        try{

            return $this->merchantsModel->where('uuid', $uuid)->delete();
            
        } catch (Exception $e){
            throw new Exception("Error in delete merchant, uuid: " . $uuid, 400);
        }
    }

    public function listAll(array $data)
    {
        try{

            $list = [];
            $query = $this->merchantsModel::query();

            if(!empty($data['name'])){
                $query->where('name', 'LIKE', '%'.$data['name'].'%');
            }

            if(!empty($data['telephone'])){
                $query->where('telephone', 'LIKE', '%'.$data['telephone'].'%');
            }

            if(!empty($data['are_open'])){
                if($data['are_open'] == "false"){
                    $query->where('are_open', false);
                } else {
                    $query->where('are_open', true);
                }
            }
    
            if(!empty($data['paginator'])){
                $pages = $query->paginate($data['paginator']);
            } else {
                throw new Exception("Paginator not found", 400);
            }

            foreach($pages as $merchant){

                $merchant = $this->modelToEntity($merchant);
                $list[] = $merchant->toArray(true);
            }

            $list['total'] = $pages->total();

            return $list;

        } catch (Exception $e){
            throw new Exception("Error in list all merchants - " . $e->getMessage(), 400);
        }
    }

    public function modelToEntity(Merchants $merchantsModel)
    {
        return new MerchantEntity(
            $merchantsModel->uuid,
            $merchantsModel->name,
            $merchantsModel->telephone,
            $merchantsModel->address,
            $merchantsModel->are_open,
            $merchantsModel->updated_at,
            $merchantsModel->created_at,
        );
    }
}
