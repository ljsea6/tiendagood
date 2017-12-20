<?php
namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Liquidaciones extends Model {

	protected $table = 'liquidaciones';

	protected $fillable = [
		    'id',
            'usuario_id',
            'fecha_inicio',
            'fecha_final',
            'fecha_liquidacion',
            'created_at',
            'updated_at'
	]; 

}
