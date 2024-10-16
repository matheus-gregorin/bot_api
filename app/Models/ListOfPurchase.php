<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model; // IMPORTANTISSIMO CONFIGURAR ESSA PARTE - DE Illuminate\Database\Eloquent\Model para -> Jenssegers\Mongodb\Eloquent\Model;

class ListOfPurchase extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    /**
     * Nome da tabela no banco de dados.
     *
     * @var string
     */
    protected $table = 'list_of_purchase';

    protected $fillable = [
        'uuid',
        'client_uuid',
        'items',
        'date_schedule',
        'form_purchase',
        'address_send',
        'value',
        'status',
        'updated_at',
        'created_at'
    ];
}
