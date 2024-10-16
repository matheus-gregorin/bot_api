<?php

namespace App\Http\Controllers;

use App\Services\ListOfPurchaseServices;
use Exception;
use Illuminate\Http\Request;

class ListOfPurchaseControllers extends Controller
{
    private ListOfPurchaseServices $listOfPurchaseServices;

    public function __construct(ListOfPurchaseServices $listOfPurchaseServices) {
        $this->listOfPurchaseServices = $listOfPurchaseServices;
    }

    public function create(Request $request)
    {
        try{

            $purchase = $this->listOfPurchaseServices->create($request->all());

            return response()->json([
                'success' => true,
                'lis_of_purchase' => $purchase
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateListItems(string $uuid, Request $request)
    {
        try{

            $this->listOfPurchaseServices->updateListItems($uuid, $request->all());

            return response()->json([
                'success' => true
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleted(string $uuid, Request $request)
    {
        try{

            $this->listOfPurchaseServices->deleted($uuid, $request->all());

            return response()->json([
                'success' => true
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAll(Request $request)
    {
        try{

            $containsPaginator = $request->has('paginator');

            $list = $this->listOfPurchaseServices->getAll($request->all());

            if($containsPaginator){
                // Extraindo a coluna pela qual queremos ordenar
                $idades = array_column($list, 'created_at');

                // Ordena o array usando array_multisort
                array_multisort($idades, SORT_DESC, $list);

            }

            return response()->json([
                'success' => true,
                'list' => $list,
                'total' => count($list)
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function AllByClient(string $clientUuid, Request $request)
    {
        try{

            $lists = $this->listOfPurchaseServices->allByClient($clientUuid, $request->all());
            $lists['total'] = count($lists);

            return response()->json([
                'success' => true,
                'lists' => $lists
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function allByMerchant(string $merchantUuid, Request $request)
    {
        try{

            $lists = $this->listOfPurchaseServices->allByMerchant($merchantUuid, $request->all());
            $lists['total'] = count($lists);

            return response()->json([
                'success' => true,
                'lists' => $lists
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteItemList(string $listUuid, string $merchantUuid, string $itemUuid, Request $request)
    {
        try{

            $this->listOfPurchaseServices->deleteItemList($listUuid, $merchantUuid, $itemUuid, $request->all());

            return response()->json([
                'success' => true
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function get(string $uuid, Request $request)
    {
        try{

            //No $request contém as informações do guest (Solicitante da requisição)
            //Por isso está sendo passado para service
            
            $list = $this->listOfPurchaseServices->get($uuid, $request->all());

            return response()->json([
                'success' => true,
                'list' => $list
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

}
