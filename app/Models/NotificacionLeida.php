<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionLeida extends Model
{
    protected $table = 'notificaciones_leidas';

    protected $fillable = ['user_id', 'clave'];

    public function user() { return $this->belongsTo(User::class); }
}
