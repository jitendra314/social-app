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

        // 1. Search Users
        $search = $request->input('search');
        $usersQuery = User::where('id', '!=', $user->id);
        if ($search) {
            $usersQuery->where('name', 'like', "%{$search}%");
        }
        $users = $usersQuery->limit(5)->get();

        // 2. Pending Friend Requests (received)
        $requests = FriendRequest::where('receiver_id', $user->id)
            ->where('accepted', false)
            ->with('sender')
            ->get();

        // 3. Friends (limit 5)
        $friends = $user->friends()->take(5);

        // 4. Suggestions (users who are not current friends or have no pending requests)
        $friendIds = $user->friends()->pluck('id')->toArray();
        $sentRequestIds = FriendRequest::where('sender_id', $user->id)->pluck('receiver_id')->toArray();
        $receivedRequestIds = FriendRequest::where('receiver_id', $user->id)->pluck('sender_id')->toArray();

        $excludeIds = array_merge([$user->id], $friendIds, $sentRequestIds, $receivedRequestIds);

        $suggestions = User::whereNotIn('id', $excludeIds)->limit(6)->get();
        $totalSuggestionsCount = User::whereNotIn('id', $excludeIds)->count();

        return view('dashboard', compact(
            'users',
            'requests',
            'friends',
            'suggestions',
            'search',
            'totalSuggestionsCount'
        ));
    }
}
