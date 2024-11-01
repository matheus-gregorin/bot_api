<?php

namespace App\Enums;

class Status 
{
    public static string $LIST_PURCHASE_STATUS_AWAIT = "AWAIT";

    public static string $CASE_CREATE = "CREATE";
    public static string $CASE_UPDATE = "UPDATE";
    public static string $CASE_DELETE = "DELETE";

    public static string $OPERATOR_STATUS_OFF = "Offline";
    public static string $OPERATOR_STATUS_ON = "Online";
}