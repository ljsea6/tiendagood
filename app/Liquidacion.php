<?php

namespace App;

use App\Entities\Tercero;
use Illuminate\Database\Eloquent\Model;

class Liquidacion extends Model
{
    protected $table = 'liquidaciones';
    protected $guarded = [];

    public function tercero()
    {
        return $this->belongsTo(Tercero::class, 'tercero_id');
    }
}
