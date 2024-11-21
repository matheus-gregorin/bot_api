<?php

namespace App\Repository;

use App\Entitys\ClientEntity;
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

    public function getByEmail(string $email)
    {
        try{

            $client = $this->clientsModel->where('email', $email)->first();
            if(!empty($client)){
                return $this->modelToEntity($client);
            }

            return false;

        } catch (Exception $e){
            throw new Exception("Error in get Client - " . $e->getMessage(), 400);
        }
    }

    public function getByUuid(string $uuid)
    {
        try{

            $client = $this->clientsModel->where('uuid', $uuid)->first();
            if(!empty($client)){
                return $this->modelToEntity($client);
            }

            return false;

        } catch (Exception $e){
            throw new Exception("Error in get client - " . $e->getMessage(), 400);
        }
    }

    public function update(string $uuid, array $data)
    {
        try{

            return $this->clientsModel->where('uuid', $uuid)->update($data);
            
        } catch (Exception $e){
            throw new Exception("Error in updated client, uuid: " . $uuid, 400);
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

            if(!empty($data['order_by']) && $data['order_by'] == 'desc'){
                $query->orderBy('created_at', 'desc');
            }
    
            if(!empty($data['paginator'])){
                $pages = $query->paginate($data['paginator']);
            } else {
                throw new Exception("Paginator not found");
            }

            foreach($pages as $client){

                $client = $this->modelToEntity($client);
                $list[] = $client->toArray(true);
            }

            $list['total'] = $pages->total();

            return $list;

        } catch (Exception $e){
            throw new Exception("Error in list all clients - " . $e->getMessage(), 400);
        }
    }

    public function modelToEntity(Clients $client)
    {
        return new ClientEntity(
            $client->uuid,
            $client->name,
            $client->date_of_birth,
            $client->number,
            $client->email,
            $client->address,
            $client->activate,
            $client->updated_at,
            $client->created_at
        );
    }
}
