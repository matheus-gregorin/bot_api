<?php

namespace App\Repository;

use App\Entitys\OperatorEntity;
use App\Enums\Status;
use App\Models\Operators;
use Carbon\Carbon;
use DateTimeImmutable;
use Exception;

class OperatorsRepository
{
    private Operators $operatorModel;

    public function __construct(Operators $operatorModel) {
        $this->operatorModel = $operatorModel;
    }

    public function create(array $data)
    {
        try{

            return $this->operatorModel->create($data);

        } catch (Exception $e){
            throw new Exception("Error in create operator - " . $e->getMessage(), 400);
        }
    }

    public function getByEmail(string $email)
    {
        try{

            $operator = $this->operatorModel->where('email', $email)->first();
            if(!empty($operator)){
                return $this->modelToEntity($operator);
            }

            return false;

        } catch (Exception $e){
            throw new Exception("Error in get operator - " . $e->getMessage(), 400);
        }
    }

    public function getByUuid(string $uuid)
    {
        try{

            $operator = $this->operatorModel->where('uuid', $uuid)->first();
            if(!empty($operator)){
                return $this->modelToEntity($operator);
            }

            return false;

        } catch (Exception $e){
            throw new Exception("Error in get operator - " . $e->getMessage(), 400);
        }
    }

    public function changeToOnline(string $uuid)
    {
        try{

            $user = $this->operatorModel->where('uuid', $uuid)->first();
            if($user){
                $user->status = "Online";
                $user->save();
            }

        } catch (Exception $e){
            throw new Exception("Error in get operator - " . $e->getMessage(), 400);
        }
    }

    public function changeToOffline(string $uuid)
    {
        try{

            $operator = $this->operatorModel->where('uuid', $uuid)->first();
            if($operator){
                $operator->status = Status::$OPERATOR_STATUS_OFF;
                $operator->save();
            }

        } catch (Exception $e){
            throw new Exception("Error in get operator", 400);
        }
    }

    public function update(string $uuid, array $data)
    {
        try{

            $this->operatorModel->where('uuid', $uuid)->update($data);
            
        } catch (Exception $e){
            throw new Exception("Error in updated operator, uuid: " . $uuid, 400);
        }
    }

    public function deleted(string $uuid)
    {
        try{

            return $this->operatorModel->where('uuid', $uuid)->delete();
            
        } catch (Exception $e){
            throw new Exception("Error in delete operator, uuid: " . $uuid, 400);
        }
    }

    public function listAll(array $data)
    {
        try{

            $list = [];
            $query = $this->operatorModel::query();

            if(!empty($data['name'])){
                $query->where('name', 'LIKE', '%'.$data['name'].'%');
            }

            if(!empty($data['permissions'])){
                $data['permissions'] = explode(', ', $data['permissions']);
                foreach($data['permissions'] as $permission){
                    $query->where('permissions', 'LIKE', '%'.$permission.'%');
                }
            }

            if(!empty($data['status'])){
                $query->where('status', 'LIKE', '%'.$data['status'].'%');
            }
    
            if(!empty($data['paginator'])){
                $pages = $query->paginate($data['paginator']);
            } else {
                throw new Exception("Not content paginator", 400);
            }

            foreach($pages as $user){

                $user->password = "";
                $operator = $this->modelToEntity($user);
                $list[] = $operator->toArray(true);
            }

            $list['total'] = $pages->total();

            return $list;

        } catch (Exception $e){
            throw new Exception("Error in list all operators - " . $e->getMessage(), 400);
        }
    }
    
    public function modelToEntity(Operators $operatorModel)
    {
        return new OperatorEntity(
            $operatorModel->uuid,
            $operatorModel->name,
            $operatorModel->email,
            $operatorModel->password,
            $operatorModel->permissions,
            $operatorModel->status,
            $operatorModel->updated_at,
            $operatorModel->created_at
        );
    }

}
