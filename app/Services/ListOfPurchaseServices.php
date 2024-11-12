<?php

namespace App\Services;

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

        $listOfPurchase = new ListOfPurchase();
        $listOfPurchase->uuid = Uuid::uuid4()->toString();
        $listOfPurchase->client_uuid = $data['client_uuid'];
        $listOfPurchase->items = $data['items'];
        $listOfPurchase->form_purchase = $data['form_purchase'];
        $listOfPurchase->address_send = $data['address_send'];
        $listOfPurchase->date_schedule = $data['date_schedule'];
        $listOfPurchase->status = Status::$LIST_PURCHASE_STATUS_AWAIT;

        $value = $this->calculateValueAndUpdateItem(Status::$CASE_CREATE, $data['items'], null);
        $listOfPurchase->value = $value;

        $list = $this->listOfPurchaseRepository->create($listOfPurchase);

        try{
            // Dispatch menssenger
            (new OrderReceivedEvent("Create list " . $listOfPurchase->uuid))->publish();
            $email = $this->sendEmailOfConfirmPurchase($listOfPurchase, $data['client_uuid']);
            Log::info("Send email and message", ['email' => $email]);
    
        } catch (Exception $e){
            Log::info("Error to send email of List", ['message' => $e->getMessage()]);
        }

        return $listOfPurchase;

    }

    public function updateItems(string $listUuid, array $data)
    {
        $list = $this->listOfPurchaseRepository->getByUuid($listUuid);
        if(!empty($list)){
            $this->validation(null, $data[ 'items']);
            $value = $this->calculateValueAndUpdateItem(Status::$CASE_UPDATE, $data['items'], $list);
            return $this->listOfPurchaseRepository->updateListItems($list->uuid, $value, $data['items']);
        }

        throw new Exception("List not found", 400);
    }

    public function update(string $listUuid, array $data)
    {
        $list = $this->listOfPurchaseRepository->getByUuid($listUuid);
        if(!empty($list)){
            
            if(!empty($data['date_schedule'])){
                $list->date_schedule = $data['date_schedule'];
            }

            if(!empty($data['form_purchase'])){
                $list->form_purchase = $data['form_purchase'];
            }

            if(!empty($data['address_send'])){
                $address = $list->address_send;

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

                $list->address_send = $address;
            }

            $this->listOfPurchaseRepository->update($list);
        }

        throw new Exception("List not found", 400);
    }

    public function delete(string $listUuid, array $data)
    {
        checkingWhetherTheRequestWasMadeByAManager($data);

        $list = $this->listOfPurchaseRepository->getByUuid($listUuid);
        if(!empty($list)){
            $this->calculateValueAndUpdateItem(Status::$CASE_DELETE, $list->items, $list);
            return $this->listOfPurchaseRepository->deleted($list->uuid);
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

    public function calculateValueAndUpdateItem(string $case, array $items, ?ListOfPurchase $list = null)
    {
        $totalValue = 0;
        foreach ($items as $uuid => $qtd){
            $item = $this->itemsRepository->getBytUuid($uuid);
            switch ($case) {
                case Status::$CASE_CREATE:

                    if($qtd > $item->qtd_item){
                        throw new Exception("quantity of items requested is greater than allowed", 400);
                    }
                    $totalValue += $item->value * $qtd;
                    $this->itemsRepository->removeQtd($item->uuid, $qtd);
                    break;

                case Status::$CASE_UPDATE:

                    $itemsCurrent = $list->items;
                    if(array_key_exists($uuid, $itemsCurrent)){

                        if($qtd > 0 && $qtd > $itemsCurrent[$uuid]){

                            $totalValue += $item->value * $qtd;
                            $qtdDif = $qtd - $itemsCurrent[$uuid];
                            $this->itemsRepository->removeQtd($item->uuid, $qtdDif);

                        } else {

                            $totalValue += $item->value * $qtd;
                            $qtdDif = $itemsCurrent[$uuid] - $qtd;
                            $this->itemsRepository->addQtd($item->uuid, $qtdDif);

                        }

                    } else {

                        $totalValue += $item->value * $qtd;
                        $this->itemsRepository->removeQtd($item->uuid, $qtd);

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
            return $list;
        }

        throw new Exception("List not found");
    }

    public function sendEmailOfConfirmPurchase(ListOfPurchase $listOfPurchase, string $uuidClient)
    {
        try {

            $client = $this->clientRepository->getByUuid($uuidClient);
            if(!empty($client)){

                $data = [];
                foreach($listOfPurchase->items as $uuidItem => $qtd){
                    $item = $this->itemsRepository->getBytUuid($uuidItem);
                    $data[] = [
                        'name' => $item->name_item,
                        'value' => $item->value,
                        'qtd' => $qtd
                    ];
                }

                //Job
                return SendEmailConfirmList::dispatch($listOfPurchase, $client, $data);
            }

        }
        catch(Exception $e){
            Log::critical("ERROR", [$e->getMessage()]);
            return false;
        }  
    }
}