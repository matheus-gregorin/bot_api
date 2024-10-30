<?php

namespace App\Repository;

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

    public function get(string $name)
    {
        try{

            return $this->operatorModel->where('email', $name)->get()->first();

        } catch (Exception $e){
            throw new Exception("Error in get operator - " . $e->getMessage(), 400);
        }
    }

    public function getByUuid(string $uuid)
    {
        try{

            return $this->operatorModel->where('uuid', $uuid)->get()->first();

        } catch (Exception $e){
            throw new Exception("Error in get operator - " . $e->getMessage(), 400);
        }
    }

    public function changeToOnline(string $uuid)
    {
        try{

            $user = $this->operatorModel->where('uuid', $uuid)->get()->first();
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

            $user = $this->operatorModel->where('uuid', $uuid)->get()->first();
            if($user){
                $user->status = "Offline";
                $user->save();
            }

        } catch (Exception $e){
            throw new Exception("Error in get operator", 400);
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

            // if(!empty($data['created_at'])){
            //     $query->where('created_at', '=', new DateTimeImmutable($data['created_at']));
            // }

            // if(!empty($data['updated_at'])){
            //     $query->where('updated_at', '=', new DateTimeImmutable($data['updated_at']));
            // }
    
            if(!empty($data['paginator'])){
                $pages = $query->paginate($data['paginator']);
            } else {
                throw new Exception("Not content paginator", 400);
            }

            foreach($pages as $user){

                $createAt = $user->created_at;
                $updatedAt = $user->updated_at;

                $list[] = [
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'permissions' => $user->permissions,
                    'status' => $user->status,
                    'created_at' => $createAt->toDateTimeString(),
                    'updated_at' => $updatedAt->toDateTimeString(),
                ];
            }

            $list['total'] = $pages->total();

            return $list;

        } catch (Exception $e){
            throw new Exception("Error in list all operators - " . $e->getMessage(), 400);
        }
    }
}
