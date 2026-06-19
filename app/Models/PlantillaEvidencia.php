<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaEvidencia extends Model
{
    protected $table = 'plantillas_evidencia';
    protected $fillable = ['path', 'nombre', 'subido_por'];

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    /**
     * Formato vigente: el más reciente que se haya subido.
     */
    public static function vigente(): ?self
    {
        return static::query()->latest('id')->first();
    }
}
