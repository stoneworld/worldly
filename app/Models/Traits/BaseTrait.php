<?php

namespace App\Models\Traits;

trait BaseTrait
{
	public static function saveData($attributes=array())
    {
        $obj = new static;
        foreach ($attributes as $key => $value) {
            $obj->$key = $value;
        }
        $obj->save();
        return static::find($obj->id);
    }
}