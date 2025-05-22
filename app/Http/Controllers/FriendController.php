<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FriendRequest;

class FriendController extends Controller
{
    public function sendRequest($receiverId)
    {
        FriendRequest::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $receiverId,
        ]);

        return redirect()->back()->with('success', 'Friend request sent.');
    }

    public function requests()
    {
        $requests = FriendRequest::where('receiver_id', auth()->id())
            ->where('accepted', false)
            ->with('sender') // Load sender data
            ->get();

        return view('friends.requests', compact('requests'));
    }


    public function acceptRequest($id)
    {
        $request = FriendRequest::findOrFail($id);
        if ($request->receiver_id == auth()->id()) {
            $request->update(['accepted' => true]);
        }
        return redirect()->back()->with('success', 'Friend request accepted.');
    }

    public function profile($id)
    {
        $authUserId = auth()->id();
        $profileUser = User::findOrFail($id);

        // Get all accepted friends for both users
        $authFriends = FriendRequest::where('accepted', true)
            ->where(function ($q) use ($authUserId) {
                $q->where('sender_id', $authUserId)
                ->orWhere('receiver_id', $authUserId);
            })
            ->get()
            ->map(function ($fr) use ($authUserId) {
                return $fr->sender_id == $authUserId ? $fr->receiver_id : $fr->sender_id;
            })->toArray();

        $profileFriends = FriendRequest::where('accepted', true)
            ->where(function ($q) use ($id) {
                $q->where('sender_id', $id)
                ->orWhere('receiver_id', $id);
            })
            ->get()
            ->map(function ($fr) use ($id) {
                return $fr->sender_id == $id ? $fr->receiver_id : $fr->sender_id;
            })->toArray();

        // Intersect both friend lists
        $mutualFriendIds = array_intersect($authFriends, $profileFriends);

        // Exclude self and profile user from results
        $mutualFriendIds = array_filter($mutualFriendIds, function ($friendId) use ($authUserId, $id) {
            return $friendId != $authUserId && $friendId != $id;
        });

        $mutualFriends = User::whereIn('id', $mutualFriendIds)->get();

        return view('users.profile', compact('profileUser', 'mutualFriends'));
    }



    public function friendList()
    {
        $userId = auth()->id();

        // Get all accepted friendships where the user is either the sender or receiver
        $friendsIds = FriendRequest::where('accepted', true)
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->get()
            ->map(function ($friendRequest) use ($userId) {
                return $friendRequest->sender_id === $userId
                    ? $friendRequest->receiver_id
                    : $friendRequest->sender_id;
            });

        $friends = User::whereIn('id', $friendsIds)->get();

        return view('friends.list', compact('friends'));
    }

    public function acceptFromSearch($senderId)
    {
        $friendRequest = FriendRequest::where('sender_id', $senderId)
            ->where('receiver_id', auth()->id())
            ->where('accepted', false)
            ->first();

        if ($friendRequest) {
            $friendRequest->update(['accepted' => true]);
            return back()->with('success', 'Friend request accepted.');
        }

        return back()->with('error', 'Friend request not found.');
    }



}
