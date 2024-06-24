<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreCostCenter extends Model
{
    protected $table = 'store_cost_centers';

    public function users()
    {
        return $this->hasMany(User::class, 'cost_center_id');
    }
}
