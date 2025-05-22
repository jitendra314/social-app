<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FriendRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::where('id', '!=', auth()->id())
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->get();

        $authUserId = auth()->id();

        // Get friend relationships
        $friends = FriendRequest::where(function ($query) use ($authUserId) {
            $query->where('sender_id', $authUserId)
                ->orWhere('receiver_id', $authUserId);
        })->get();

        // Build a map of existing requests/friendships
        $friendIds = [];
        $pendingIds = [];
        $receivedRequestIds = [];

        foreach ($friends as $friend) {
            if ($friend->accepted) {
                $friendIds[] = $friend->sender_id == $authUserId ? $friend->receiver_id : $friend->sender_id;
            } else {
                if ($friend->sender_id == $authUserId) {
                    $pendingIds[] = $friend->receiver_id;
                } elseif ($friend->receiver_id == $authUserId) {
                    $receivedRequestIds[] = $friend->sender_id;
                }
            }
        }

        return view('users.index', compact('users', 'friendIds', 'pendingIds', 'receivedRequestIds'));

    }

}
