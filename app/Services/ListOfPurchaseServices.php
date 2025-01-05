<?php

namespace App\Services;

use App\Entitys\ListOfPurchaseEntity;
use App\Enums\Status;
use App\Jobs\SendEmailConfirmList;
use App\Jobs\sendEmailListPurchase;
use App\Models\Clients;
use App\Models\ListOfPurchase;
use App\Publishers\OrderReceivedEvent;
use App\Repository\ClientsRepository;
use App\Repository\ItemsRepository;
use App\Repository\ListOfPurchaseRepository;
use App\Repository\MerchantsRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ListOfPurchaseServices
{
    private ListOfPurchaseRepository $listOfPurchaseRepository;
    private ClientsRepository $clientRepository;
    private MerchantsRepository $merchantRepository;
    private ItemsRepository $itemsRepository;

    public function __construct(
        ListOfPurchaseRepository $listOfPurchaseRepository,
        ClientsRepository $clientRepository,
        MerchantsRepository $merchantRepository,
        ItemsRepository $itemsRepository
        ) {

        $this->listOfPurchaseRepository = $listOfPurchaseRepository;
        $this->clientRepository = $clientRepository;
        $this->merchantRepository = $merchantRepository;
        $this->itemsRepository = $itemsRepository;
    }

    public function create(array $data)
    {
        $this->validation($data['client_uuid'], $data['items']);

        $listOfPurchase = new ListOfPurchaseEntity(
            Uuid::uuid4()->toString(),
            $data['client_uuid'],
            $data['items'],
            $data['form_purchase'],
            $data['address_send'],
            $data['date_schedule'],
            Status::$LIST_PURCHASE_STATUS_AWAIT,
            0,
            Carbon::now(),
            Carbon::now()
        );

        $value = $this->calculateValueAndUpdateItem(Status::$CASE_CREATE, $listOfPurchase->getItems(), null);
        $listOfPurchase->setValue($value);

        $list = $this->listOfPurchaseRepository->create($listOfPurchase->toArray(false));

        try{
            // Dispatch menssenger
            (new OrderReceivedEvent("Create list " . $listOfPurchase->getUuid()))->publish();
            $email = $this->sendEmailOfConfirmPurchase($listOfPurchase, $listOfPurchase->getClientUuid());
            Log::info("Send email and message", []);
    
        } catch (Exception $e){
            Log::info("Error to send email of List", ['message' => $e->getMessage()]);
        }

        if($list){
            return $listOfPurchase->toArray(true);
        }
        throw new Exception("List not create", 500);

    }

    public function updateItems(string $listUuid, array $data)
    {
        $list = $this->listOfPurchaseRepository->getByUuid($listUuid);
        if(!empty($list)){
            $this->validation(null, $data[ 'items']);
            $value = $this->calculateValueAndUpdateItem(Status::$CASE_UPDATE, $data['items'], $list);
            $listUpdated = $this->listOfPurchaseRepository->updateListItems($list->getUuid(), $value, $data['items']);
            if($listUpdated){
                return $listUpdated;
            }

            throw new Exception("error in updated items", 500);

        }

        throw new Exception("List not found", 400);
    }

    public function update(string $listUuid, array $data)
    {
        $list = $this->listOfPurchaseRepository->getByUuid($listUuid);
        if(!empty($list)){
            
            if(!empty($data['date_schedule'])){
                $list->setDateSchedule($data['date_schedule']);
            }

            if(!empty($data['form_purchase'])){
                $list->setFormPurchase($data['form_purchase']);
            }

            if(!empty($data['address_send'])){
                $address = $list->getAddressSend();

                if(!empty($data['address_send']['street'])){
                    $address['street'] = $data['address_send']['street'];
                }
                if(!empty($data['address_send']['number'])){
                    $address['number'] = $data['address_send']['number'];
                }
                if(!empty($data['address_send']['neighborhood'])){
                    $address['neighborhood'] = $data['address_send']['neighborhood'];
                }
                if(!empty($data['address_send']['city'])){
                    $address['city'] = $data['address_send']['city'];
                }

                $list->setAddressSend($address);
            }

            $update = $this->listOfPurchaseRepository->update($list->getUuid(), $list->toArray(false));
            if($update){
                return $list->toArray(true);
            }

            throw new Exception("List can not updated", 400);
        }

        throw new Exception("List not found", 400);
    }

    public function delete(string $listUuid, array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        $list = $this->listOfPurchaseRepository->getByUuid($listUuid);
        if(!empty($list)){
            $this->calculateValueAndUpdateItem(Status::$CASE_DELETE, $list->getItems(), $list);
            return $this->listOfPurchaseRepository->deleted($list->getUuid());
        }

        throw new Exception("List not found", 400);
    }

    public function validation(?string $clientuuid = null, ?array $items = null)
    {
        if(!empty($clientuuid)){
            $client = $this->clientRepository->getByUuid($clientuuid);
            if(empty($client)){
                throw new Exception("Client not found");
            }
        }

        if(!empty($items)){
            foreach ($items as $uuid => $value){
                $item = $this->itemsRepository->getBytUuid($uuid);
                if(empty($item)){
                    throw new Exception("Item not exist - $uuid");
                }
            }
        }
    }

    public function calculateValueAndUpdateItem(string $case, array $items, ?ListOfPurchaseEntity $list = null)
    {
        $totalValue = 0;
        foreach ($items as $uuid => $qtd){
            $item = $this->itemsRepository->getBytUuid($uuid);
            switch ($case) {
                case Status::$CASE_CREATE:

                    if($qtd > $item->getQtdItem()){
                        throw new Exception("quantity of items requested is greater than allowed", 400);
                    }
                    $totalValue += $item->getValue() * $qtd;
                    $this->itemsRepository->removeQtd($item->getUuid(), $qtd);
                    break;

                case Status::$CASE_UPDATE:

                    $itemsCurrent = $list->getItems();
                    if(array_key_exists($uuid, $itemsCurrent)){

                        if($qtd > 0 && $qtd > $itemsCurrent[$uuid]){

                            if($qtd > $item->getQtdItem()){
                                throw new Exception("quantity of items requested is greater than allowed", 400);
                            }

                            $totalValue += $item->getValue() * $qtd;
                            $qtdDif = $qtd - $itemsCurrent[$uuid];
                            $this->itemsRepository->removeQtd($item->getUuid(), $qtdDif);

                        } else {

                            $totalValue += $item->getValue() * $qtd;
                            $qtdDif = $itemsCurrent[$uuid] - $qtd;
                            $this->itemsRepository->addQtd($item->getUuid(), $qtdDif);

                        }

                    } else {

                        if($qtd > $item->getQtdItem()){
                            throw new Exception("quantity of items requested is greater than allowed", 400);
                        }

                        $totalValue += $item->getValue() * $qtd;
                        $this->itemsRepository->removeQtd($item->getUuid(), $qtd);

                    }
                    break;
                
                case Status::$CASE_DELETE:

                    $this->itemsRepository->addQtd($uuid, $qtd);
                
            }
        }

        return $totalValue;
    }

    public function getAll(array $data)
    {
        return $this->listOfPurchaseRepository->getAll($data);
    }

    public function get(string $uuid, ?array $data = [])
    {

        $list = $this->listOfPurchaseRepository->getBytUuid($uuid);
        if($list){
            return $list->toArray(true);
        }

        throw new Exception("List not found");
    }

    public function sendEmailOfConfirmPurchase(ListOfPurchaseEntity $listOfPurchase, string $uuidClient)
    {
        try {

            $client = $this->clientRepository->getByUuid($uuidClient);
            if(!empty($client)){

                $data = [];
                foreach($listOfPurchase->getItems() as $uuidItem => $qtd){
                    $item = $this->itemsRepository->getBytUuid($uuidItem);
                    $data[] = [
                        'name' => $item->getNameItem(),
                        'value' => $item->getValue(),
                        'qtd' => $qtd
                    ];
                }

                //Job
                SendEmailConfirmList::dispatch($listOfPurchase, $client, $data);
            }

        }
        catch(Exception $e){
            Log::critical("ERROR", [$e->getMessage()]);
            return false;
        }  
    }

    public function graph(array $data)
    {
        $months = [0,0,0,0,0,0,0,0,0,0,0,0];
        $values = [0,0,0,0,0,0,0,0,0,0,0,0];

        $lists = $this->listOfPurchaseRepository->getAllForGraph($data);

        foreach($lists as $list){
            if(!empty($list['created_at'])){

                $month = Carbon::parse($list['created_at'])->month;
                $months[$month - 1] += 1;
    
                $containValue = !empty($list['value']) ? true : false;
                if($containValue){
                    $values[$month - 1] += $list['value'];
                }

            }
        }

        return [
            'list_per_months' => $months,
            'values_per_month' => $values
        ];
    }
}