<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Providers as ProviderModel;

class Order extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'ACMROI';

    // Campos que se pueden rellenar
    protected $fillable = [
        'CNTDOCID',
        'ACMROIDOC',
        'ACMROIFDOC',
        'ACMROIFCEP',
        'INALMNID',
        'CNCDIRID',
        'ACMROIREF', //ref
        'ACMROICXP',
        'ACMROIDSC',
        'ACMROIUMT',
        'ACMROILIN',
        'INPRODID',
        'ACMROIQT',
        'ACMROIQTTR',
        'ACMROINP',
        'ACMROING',
        'ACMROITDOC',
        'ACMROINDOC',
        'ACMROIBGP'
    ];

    // Si no usas timestamps
    public $timestamps = false;

    public function provider()
    {
        return $this->belongsTo(ProviderModel::class, 'CNCDIRID', 'CNCDIRID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'INPRODID', 'INPRODID');
    }
}
