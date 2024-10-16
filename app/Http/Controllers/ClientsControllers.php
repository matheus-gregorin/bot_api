<?php

namespace App\Http\Controllers;

use App\Services\ClientsServices;
use Exception;
use Illuminate\Http\Request;

class ClientsControllers extends Controller
{
    private ClientsServices $clientsServices;

    public function __construct(ClientsServices $clientsServices) {
        $this->clientsServices = $clientsServices;
    }

    public function create(Request $request)
    {
        try{

            $client = $this->clientsServices->create($request->all());

            return response()->json([
                'success' => true,
                'client' => $client
            ]);
        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updated(string $uuid, Request $request)
    {
        try{

            $this->clientsServices->updated($uuid, $request->all());

            return response()->json([
                'success' => true,
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

            //No $request contém as informações do guest (Solicitante da requisição)
            //Por isso está sendo passado para service

            $this->clientsServices->deleted($uuid, $request->all());

            return response()->json([
                'success' => true,
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function all(Request $request)
    {
        try{

            $clients = $this->clientsServices->all($request->all());
            $total = $clients['total'] ?? 0;
            unset($clients['total']);

            return response()->json([
                'success' => true,
                'clients' => $clients,
                'total' => $total
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
            
            $client = $this->clientsServices->get($uuid, $request->all());

            return response()->json([
                'success' => true,
                'client' => $client
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
