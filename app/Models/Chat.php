<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['owner_id', 'partner_id'];

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<User, $this>
     */
    public function partner()
    {
        return $this->hasOne(User::class, 'id', 'partner_id');
    }
}
