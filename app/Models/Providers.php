<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Providers extends Model
{
    use HasFactory;

    protected $table = 'CNCDIR';

    protected $primaryKey = 'CNCDIRID';

    protected $fillable = [
        'CNCDIRID',
        'CNCDIRNOM',
        // otros campos si es necesario
    ];
}
