<?php

namespace App\Http\Controllers;

use App\Services\OperatorsServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OperatorsControllers extends Controller
{
    private OperatorsServices $operatorsServices;

    public function __construct(OperatorsServices $operatorsServices) 
    {
        $this->operatorsServices = $operatorsServices;
    }

    public function create(Request $request)
    {
        try{

            $user = $this->operatorsServices->create($request->all());

            return response()->json([
                'success' => true,
                'user' => $user
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function login(Request $request)
    {
        try{

            $data = $this->operatorsServices->login($request->get('email'), $request->get('password'));

            return response()->json([
                'success' => true, 
                'token' => $data['token'],
                'uuid' => $data['uuid'],
                'expire_in' => 12
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function logout(string $operatorUuid, Request $request)
    {
        try{

            $token = !empty($request->header('authorization')) ? 
            $request->header('authorization') : 
            throw new Exception("Token not exists", 404);

            $this->operatorsServices->logout($operatorUuid, $token, $request->all());

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

    public function updated(string $uuid, Request $request)
    {
        try{

            $operator = $this->operatorsServices->updated($uuid, $request->all());

            return response()->json([
                'success' => true,
                'operator' => $operator
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

            $this->operatorsServices->deleted($uuid, $request->all());

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

            $operators = $this->operatorsServices->all($request->all());
            $total = $operators['total'] ?? 0;
            unset($operators['total']);

            return response()->json([
                'success' => true,
                'operators' => $operators,
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
            
            $operator = $this->operatorsServices->get($uuid, $request->all());
            unset($operator['password']);

            return response()->json([
                'success' => true,
                'operator' => $operator
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    public function valid(Request $request)
    {
        try {

            $token = $request->header('Authorization');
            $this->operatorsServices->validToken($token);

            return response()->json([
                'success' => true
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

}