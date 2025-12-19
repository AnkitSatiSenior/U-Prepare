<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['name', 'is_group'];

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function users() {
        return $this->belongsToMany(User::class, 'conversation_user');
    }
    public function lastMessage()
{
    return $this->hasOne(Message::class)->latestOfMany();
}

}
