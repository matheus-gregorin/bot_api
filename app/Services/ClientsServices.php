<?php

namespace App\Services;

use App\Repository\ClientsRepository;
use Exception;
use Ramsey\Uuid\Uuid;

class ClientsServices
{
    private ClientsRepository $clientsRepository;

    public function __construct(ClientsRepository $clientsRepository) {
        $this->clientsRepository = $clientsRepository;
    }

    public function create(array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        $clientVerify = $this->clientsRepository->get($data['name']);
        if($clientVerify){
            throw new Exception("Client exists", 404);
        }

        $data = ["uuid" => Uuid::uuid4()->toString()] + $data;
        $data['activate'] = false;

        return $this->clientsRepository->create($data);
    }

    public function updated(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $client = $this->clientsRepository->getByUuid($uuid);
        if($client){

            if($client->name != $data['name_guest'] && !in_array("manager", $data["permissions_guest"])){
                throw new Exception("You not permited action", 401);
            }
            
            ///name
            if(!empty($data['name'])){
                if($data['name'] != $client->name){
                    $client->name = $data['name'];
                }
            }

            //address
            if(!empty($data['address'])){
                $client->address = $data['address'];
            }

            //activate
            if(isset($data['activate'])){
                if($data['activate'] != $client->activate){
                    $client->activate = $data['activate'];
                }
            }

            return $client->save();
        }

        throw new Exception("client not found", 401);
    }

    public function deleted(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $client = $this->clientsRepository->getByUuid($uuid);
        if($client){
            return $this->clientsRepository->deleted($client->uuid);
        }

        throw new Exception("client not found", 401);
    }

    public function all(array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        return $this->clientsRepository->listAll($data);
    }

    public function get(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $client = $this->clientsRepository->getByUUid($uuid);
        if($client){
            unset($client['password']);
            return $client;
        }

        throw new Exception("client not found", 404);
    }
}
