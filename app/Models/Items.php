<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model; // IMPORTANTISSIMO CONFIGURAR ESSA PARTE - DE Illuminate\Database\Eloquent\Model para -> Jenssegers\Mongodb\Eloquent\Model;

class Items extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    /**
     * Nome da tabela no banco de dados.
     *
     * @var string
     */
    protected $table = 'items';

    protected $fillable = [
        'uuid',
        'merchant_uuid',
        'name_item',
        'qtd_item',
        'value',
        'is_promotion',
        'updated_at',
        'created_at'
    ];
}
