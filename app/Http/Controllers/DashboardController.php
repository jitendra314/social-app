<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FriendRequest;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Users for search (optional: limit to 5 or search query)
        $search = $request->input('search');
        $usersQuery = User::where('id', '!=', $user->id);
        if ($search) {
            $usersQuery->where('name', 'like', "%{$search}%");
        }
        $users = $usersQuery->limit(5)->get();

        // Pending friend requests (requests sent to this user)
        $requests = FriendRequest::where('receiver_id', $user->id)
                                 ->where('accepted', '0')
                                 ->with('sender')
                                 ->get();

        // Friends list (assuming you have a method on User model)
        $friends = $user->friends()->take(5);

        return view('dashboard', compact('users', 'requests', 'friends', 'search'));
    }
}

