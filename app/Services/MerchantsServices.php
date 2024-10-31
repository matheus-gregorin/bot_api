<?php

namespace App\Services;

use App\Repository\MerchantsRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class MerchantsServices
{
    private MerchantsRepository $merchantsRepository;

    public function __construct(MerchantsRepository $merchantsRepository) {
        $this->merchantsRepository = $merchantsRepository;
    }

    public function create(array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        $merchantVerify = $this->merchantsRepository->get($data['name']);
        if($merchantVerify){
            throw new Exception("Merchants exists", 404);
        }

        $data = ["uuid" => Uuid::uuid4()->toString()] + $data;
        $data['are_open'] = false;

        return $this->merchantsRepository->create($data);
    }

    public function updated(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $merchant = $this->merchantsRepository->getByUuid($uuid);
        if($merchant){
            
            ///name
            if(!empty($data['name'])){
                if($data['name'] != $merchant->name){
                    $merchant->name = $data['name'];
                }
            }

            //telephone
            if(!empty($data['telephone'])){
                $merchant->telephone = $data['telephone'];
            }
    
            //address
            if(!empty($data['address'])){
                
                $address = $merchant->address;

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

                $merchant->address = $address;

            }

            //are open
            if(isset($data['are_open'])){
                $merchant->are_open = $data['are_open'];
            }

            return $merchant->save();
        }

        throw new Exception("merchant not found", 401);
    }

    public function deleted(string $uuid, array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        $merchant = $this->merchantsRepository->getByUuid($uuid);
        if($merchant){
            return $this->merchantsRepository->deleted($merchant->uuid);
        }

        throw new Exception("Merchant not found", 401);
    }

    public function get(string $uuid, array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        $merchant = $this->merchantsRepository->getByUuid($uuid);
        if($merchant){
            unset($merchant['password']);
            return $merchant;
        }

        throw new Exception("merchant not found", 404);
    }

    public function all(array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        Log::info("", ['data' => $data]);

        return $this->merchantsRepository->listAll($data);
    }

    public function operation(string $uuid, array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        $setOn = $data['operation'];

        $merchant = $this->merchantsRepository->getByUuid($uuid);
        if($merchant){
            if($merchant->are_open != $setOn){
                $merchant->are_open = $setOn;
                return $merchant->save();
            }
            throw new Exception("merchant is already updated", 404);
        }

        throw new Exception("merchant not found", 404);
    }
}
