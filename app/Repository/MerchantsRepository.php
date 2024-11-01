<?php

namespace App\Repository;

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

    public function getByName(string $name)
    {
        try{

            return $this->merchantsModel->where('name', $name)->first();

        } catch (Exception $e){
            throw new Exception("Error in get merchant - " . $e->getMessage(), 400);
        }
    }

    public function getByUuid(string $uuid)
    {
        try{

            return $this->merchantsModel->where('uuid', $uuid)->get()->first();

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
            }

            foreach($pages as $merchant){

                $createAt = $merchant->created_at;
                $updatedAt = $merchant->updated_at;

                $list[] = [
                    'uuid' => $merchant->uuid,
                    'name' => $merchant->name,
                    'address' => $merchant->address,
                    'are_open' => $merchant->are_open,
                    'created_at' => $createAt->toDateTimeString(),
                    'updated_at' => $updatedAt->toDateTimeString(),
                ];
            }

            $list['total'] = $pages->total();

            return $list;

        } catch (Exception $e){
            throw new Exception("Error in list all merchants - " . $e->getMessage(), 400);
        }
    }
}
