<?php

if (!function_exists('checkingWhetherTheRequestWasMadeByAManager')) {
    function checkingWhetherTheRequestWasMadeByAManager(array $data)
    {
        if(!in_array('manager', $data['permissions_guest'])){
            throw new Exception("Operator not is manager", 401);
        }
    }
}
