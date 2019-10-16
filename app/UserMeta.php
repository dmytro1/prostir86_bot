<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * UserMeta
 *
 * @mixin \Eloquent
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder| newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder query()
 */

class UserMeta extends Model
{
    protected $guarded = [];

//    public function user()
//    {
//        return $this->belongsTo('App\User');
//    }
}
