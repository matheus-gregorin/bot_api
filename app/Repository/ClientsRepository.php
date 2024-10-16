<?php

namespace App\Repository;

use App\Models\Clients;
use Exception;

class ClientsRepository
{
    private Clients $clientsModel;

    public function __construct(Clients $clientsModel)
    {
     $this->clientsModel = $clientsModel;   
    }

    public function create(array $data)
    {
        try{

            return $this->clientsModel->create($data);

        } catch (Exception $e){
            throw new Exception("Error in create client - " . $e->getMessage(), 400);
        }
    }

    public function get(string $name)
    {
        try{

            return $this->clientsModel->where('name', $name)->get()->first();

        } catch (Exception $e){
            throw new Exception("Error in get Client - " . $e->getMessage(), 400);
        }
    }

    public function getByUUid(string $uuid)
    {
        try{

            return $this->clientsModel->where('uuid', $uuid)->get()->first();

        } catch (Exception $e){
            throw new Exception("Error in get client - " . $e->getMessage(), 400);
        }
    }

    public function deleted(string $uuid)
    {
        try{

            return $this->clientsModel->where('uuid', $uuid)->delete();
            
        } catch (Exception $e){
            throw new Exception("Error in delete operator, uuid: " . $uuid, 400);
        }
    }

    public function listAll(array $data)
    {
        try{

            $list = [];
            $query = $this->clientsModel::query();

            if(!empty($data['name'])){
                $query->where('name', 'LIKE', '%'.$data['name'].'%');
            }

            if(!empty($data['date_of_birth'])){
                $query->where('date_of_birth', 'LIKE', '%'.$data['date_of_birth'].'%');
            }

            if(isset($data['activate'])){
                if($data['activate'] == "false"){
                    $query->where('activate', false);
                } else {
                    $query->where('activate', true);
                }
            }
    
            if(!empty($data['paginator'])){
                $pages = $query->paginate($data['paginator']);
            }

            foreach($pages as $client){

                $createAt = $client->created_at;
                $updatedAt = $client->updated_at;

                $list[] = [
                    'uuid' => $client->uuid,
                    'name' => $client->name,
                    'date_of_birth' => $client->date_of_birth,
                    'address' => $client->address,
                    'activate' => $client->activate,
                    'created_at' => $createAt->toDateTimeString(),
                    'updated_at' => $updatedAt->toDateTimeString(),
                ];
            }

            $list['total'] = $pages->total();

            return $list;

        } catch (Exception $e){
            throw new Exception("Error in list all clients - " . $e->getMessage(), 400);
        }
    }
}
