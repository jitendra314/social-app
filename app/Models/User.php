<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'facebook_id'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Friends that this user sent requests to and are accepted
    public function friendsOfMine()
    {
        return $this->belongsToMany(User::class, 'friend_requests', 'sender_id', 'receiver_id')
                    ->wherePivot('accepted', '1');
    }

    // Friends who sent requests to this user and are accepted
    public function friendOf()
    {
        return $this->belongsToMany(User::class, 'friend_requests', 'receiver_id', 'sender_id')
                    ->wherePivot('accepted', '1');
    }

    // Get all friends (both directions merged)
    public function friends()
    {
        $friendsOfMine = $this->friendsOfMine()->get();
        $friendOf = $this->friendOf()->get();

        return $friendsOfMine->merge($friendOf);
    }

    // In User.php
    public function isFriendWith($userId)
    {
        return $this->friendsOfMine()->where('receiver_id', $userId)->exists() ||
            $this->friendOf()->where('sender_id', $userId)->exists();
    }

    public function sentFriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function hasPendingRequestWith($userId)
    {
        return $this->sentFriendRequests()
                    ->where('receiver_id', $userId)
                    ->where('accepted', false)
                    ->exists();
    }

    public function hasReceivedRequestFrom($userId)
    {
        return $this->receivedFriendRequests()
                    ->where('sender_id', $userId)
                    ->where('accepted', false)
                    ->exists();
    }



}
