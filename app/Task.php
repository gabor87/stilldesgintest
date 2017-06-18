<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_DONE = 2;
    
    public static function statuses() {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_IN_PROGRESS => 'In progress',
            self::STATUS_DONE => 'Done',
        ];
    }
}
