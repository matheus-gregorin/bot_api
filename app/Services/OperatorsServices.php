<?php

namespace App\Services;

use App\Repository\LoginLogoutRepository;
use App\Repository\OperatorsRepository;
use Carbon\Carbon;
use Exception;
use Firebase\JWT\JWT;
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Ramsey\Uuid\Uuid;
use Twilio\Rest\Client;

class OperatorsServices
{
    use DispatchesJobs;

    private OperatorsRepository $operatorsRepository;
    private LoginLogoutRepository $loginLogoutRepository;

    public function __construct(OperatorsRepository $operatorsRepository, LoginLogoutRepository $loginLogoutRepository) {
        $this->operatorsRepository = $operatorsRepository;
        $this->loginLogoutRepository = $loginLogoutRepository;
    }

    public function create(array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $operatorVerify = $this->operatorsRepository->get($data['name']);
        if($operatorVerify){
            throw new Exception('Operator exists', 404);
        }

        $data = ["uuid" => Uuid::uuid4()->toString()] + $data;
        $data['password'] = bcrypt($data['password']);
        $data['status'] = "Offline";

        $user = $this->operatorsRepository->create($data);
        unset($user['password']);

        return $user;
    }

    public function login(string $name, string $password)
    {
        $operator = $this->operatorsRepository->get($name);
        if($operator && password_verify($password, $operator->password)){

            $token = JWT::encode(
                ['name' => $operator->name, 'permissions' => $operator->permissions, 'exp' => now()->addHours(12)->getTimestamp()],
                env('SECRET_JWT') ?? "X",
                'HS256'
            );

            $this->loginLogoutRepository->login(['operator_uuid' => $operator->uuid, 'log' => "Entrada : " . Carbon::now()->toString()]);
            $this->operatorsRepository->changeToOnline($operator->uuid);

        return $token;

        }

        throw new Exception("Credentials invalid", 401);
    }

    public function logout(array $data)
    {

        $operator = $this->operatorsRepository->get($data['name_guest']);
        if($operator){
            if($operator->status == "Online"){
                $this->loginLogoutRepository->logout(['operator_uuid' => $operator->uuid, 'log' => "Saída : " . Carbon::now()->toString()]);
                $this->operatorsRepository->changeToOffline($operator->uuid);
    
                return;
            }
            throw new Exception("Operator is already offline", 401);
        }

        throw new Exception("Operator not found", 401);
    }

    public function updated(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $operator = $this->operatorsRepository->getByUuid($uuid);
        if($operator){

            if($operator->name != $data['name_guest'] && !in_array("manager", $data["permissions_guest"])){
                throw new Exception("You not permited action", 401);
            }
            
            ///name
            // if(!empty($data['name'])){
            //     if($data['name'] != $operator->name){
            //         $operator->name = $data['name'];
            //     }
            // }

            //permissions
            if(!empty($data['permissions'])){
                $operator->permissions = $data['permissions'];
            }

            //status
            // if(!empty($data['status'])){
            //     if($data['status'] != $operator->status){
            //         var_dump(3);
            //         $operator->status = $data['status'];
            //     }
            // }

            return $operator->save();
        }

        throw new Exception("Operator not found", 401);
    }

    public function deleted(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $operator = $this->operatorsRepository->getByUuid($uuid);
        if($operator){
            return $this->operatorsRepository->deleted($operator->uuid);
        }

        throw new Exception("Operator not found", 401);
    }

    public function all(array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        return $this->operatorsRepository->listAll($data);
    }

    public function get(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $operator = $this->operatorsRepository->getByUUid($uuid);
        if($operator){
            unset($operator['password']);
            return $operator;
        }

        throw new Exception("Operator not found", 404);
    }

}