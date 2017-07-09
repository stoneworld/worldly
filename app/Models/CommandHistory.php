<?php

namespace App\Models;

use App\Models\Traits\BaseTrait;

use Illuminate\Database\Eloquent\Model;

class CommandHistory extends Model
{
    use BaseTrait;
    protected $table = 'command_histories';
    protected $fillable = ['*'];
    public $timestamps = false;
    
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'parame'      => 'object',
    ];
}
