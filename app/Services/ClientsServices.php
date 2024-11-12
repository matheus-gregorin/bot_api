<?php

namespace App\Services;

use App\Entitys\ClientEntity;
use App\Repository\ClientsRepository;
use Carbon\Carbon;
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

        $clientVerify = $this->clientsRepository->getByEmail($data['email']);
        if($clientVerify){
            throw new Exception("Client exists", 404);
        }

        $client = new ClientEntity(
            Uuid::uuid4()->toString(),
            $data["name"],
            $data["date_of_birth"],
            $data["number"],
            $data["email"],
            $data["address"],
            false,
            Carbon::now(),
            Carbon::now()
        );

        return $this->clientsRepository->create($client->toArray(false));
    }

    public function updated(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $client = $this->clientsRepository->getByUuid($uuid);
        if($client){
            
            ///name
            if(!empty($data['name'])){
                if($data['name'] != $client->getName()){
                    $client->setName($data['name']);
                }
            }

            //address
            if(!empty($data['address'])){

                $address = [];

                if(!empty($data['address']['street'])){
                    $address['street'] = $data['address']['street'];
                }

                if(!empty($data['address']['number'])){
                    $address['number'] = $data['address']['number'];
                }

                if(!empty($data['address']['neighborhood'])){
                    $address['neighborhood'] = $data['address']['neighborhood'];
                }

                if(!empty($data['address']['city'])){
                    $address['city'] = $data['address']['city'];
                }

                $client->setAddress($address);
            }

            //number
            if(!empty($data['number'])){
                $client->setNumber($data['number']);
            }

            //activate
            if(isset($data['activate'])){
                if($data['activate'] != $client->getActivate()){
                    $client->setActivate($data['activate']);
                }
            }

            return $this->clientsRepository->update($client->getUuid(), $client->toArray(false));
        }

        throw new Exception("client not found", 401);
    }

    public function deleted(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $client = $this->clientsRepository->getByUuid($uuid);
        if($client){
            return $this->clientsRepository->deleted($client->getUuid());
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

        $client = $this->clientsRepository->getByUuid($uuid);
        if($client){
            return $client->toArray(true);
        }

        throw new Exception("client not found", 404);
    }
}
