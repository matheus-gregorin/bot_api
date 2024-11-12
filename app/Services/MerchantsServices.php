<?php

namespace App\Services;

use App\Entitys\MerchantEntity;
use App\Repository\MerchantsRepository;
use Carbon\Carbon;
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

        $merchantVerify = $this->merchantsRepository->getByName($data['name']);
        if($merchantVerify){
            throw new Exception("Merchants exists", 404);
        }

        $merchant = new MerchantEntity(
            Uuid::uuid4()->toString(),
            $data["name"],
            $data["telephone"],
            $data['address'],
            false,
            Carbon::now(),
            Carbon::now()
        );

        return $this->merchantsRepository->create($merchant->toArray(false));
    }

    public function updated(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $merchant = $this->merchantsRepository->getByUuid($uuid);
        if($merchant){
            
            ///name
            if(!empty($data['name'])){
                if($data['name'] != $merchant->getName()){
                    $merchant->setName($data['name']);
                }
            }

            //telephone
            if(!empty($data['telephone'])){
                $merchant->setTelephone($data['telephone']);
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

                $merchant->setAddress($address);
            }

            //are open
            if(isset($data['are_open'])){
                $merchant->setAreOpen($data['are_open']);
            }

            return $this->merchantsRepository->update($merchant->getUuid(), $merchant->toArray(false));
        }

        throw new Exception("merchant not found", 401);
    }

    public function deleted(string $uuid, array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        $merchant = $this->merchantsRepository->getByUuid($uuid);
        if($merchant){
            return $this->merchantsRepository->deleted($merchant->getUuid());
        }

        throw new Exception("Merchant not found", 401);
    }

    public function get(string $uuid, array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        $merchant = $this->merchantsRepository->getByUuid($uuid);
        if($merchant){
            return $merchant->toArray(true);
        }

        throw new Exception("merchant not found", 404);
    }

    public function all(array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        Log::info("Merchants", ['data' => $data]);
        return $this->merchantsRepository->listAll($data);
    }

    public function operation(string $uuid, array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        $setOn = $data['operation'];

        $merchant = $this->merchantsRepository->getByUuid($uuid);
        if($merchant){
            if($merchant->getAreOpen() != $setOn){
                $merchant->setAreOpen($setOn);
                return $this->merchantsRepository->update($merchant->getUuid(), $merchant->toArray(false));
            }
            throw new Exception("merchant is already updated", 404);
        }

        throw new Exception("merchant not found", 404);
    }
}
