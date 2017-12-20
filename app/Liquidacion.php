<?php

namespace App;

use App\Entities\Tercero;
use App\LiquidacionTercero;
use App\LiquidacionDetalle;
use Illuminate\Database\Eloquent\Model;

class Liquidacion extends Model
{
    protected $table = 'liquidaciones';
    protected $guarded = [];

    public function tercero()
    {
        return $this->belongsTo(Tercero::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(LiquidacionDetalle::class, 'liquidacion_id');
    }

    public function liquidacion_tercero()
    {
        return $this->hasOne(LiquidacionTercero::class, 'liquidacion_id');
    }
}
