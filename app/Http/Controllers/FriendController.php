<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FriendRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FriendController extends Controller
{
    public function sendRequest($receiverId)
    {
        try {
            if (FriendRequest::where('sender_id', auth()->id())->where('receiver_id', $receiverId)->exists()) {
                return back()->with('error', 'Friend request already sent.');
            }

            FriendRequest::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $receiverId,
            ]);

            return redirect()->back()->with('success', 'Friend request sent.');
        } catch (\Exception $e) {
            Log::error("Error sending friend request: " . $e->getMessage());
            return back()->with('error', 'Something went wrong while sending the friend request.');
        }
    }

    public function requests()
    {
        try {
            $requests = FriendRequest::where('receiver_id', auth()->id())
                ->where('accepted', false)
                ->with('sender')
                ->get();

            return view('friends.requests', compact('requests'));
        } catch (\Exception $e) {
            Log::error("Error fetching friend requests: " . $e->getMessage());
            return back()->with('error', 'Could not load friend requests.');
        }
    }

    public function acceptRequest($id)
    {
        try {
            $request = FriendRequest::findOrFail($id);
            if ($request->receiver_id == auth()->id()) {
                $request->update(['accepted' => true]);
                return redirect()->back()->with('success', 'Friend request accepted.');
            }
            return redirect()->back()->with('error', 'Unauthorized action.');
        } catch (\Exception $e) {
            Log::error("Error accepting request: " . $e->getMessage());
            return back()->with('error', 'Could not accept friend request.');
        }
    }

    public function cancelRequest($receiverId)
    {
        try {
            FriendRequest::where('sender_id', auth()->id())
                ->where('receiver_id', $receiverId)
                ->delete();

            return back()->with('success', 'Friend request canceled.');
        } catch (\Exception $e) {
            Log::error("Error canceling request: " . $e->getMessage());
            return back()->with('error', 'Could not cancel friend request.');
        }
    }


    public function rejectRequest($id)
    {
        try {
            $request = FriendRequest::where('id', $id)
                ->where('receiver_id', auth()->id())
                ->firstOrFail();

            $request->delete();

            return back()->with('success', 'Friend request rejected.');
        } catch (\Exception $e) {
            Log::error("Error rejecting request: " . $e->getMessage());
            return back()->with('error', 'Could not reject friend request.');
        }
    }

    public function friendList()
    {
        try {
            $user = auth()->user();

            // Load both types of friends with pivot data
            $friendsOfMine = $user->friendsOfMine()->withPivot('created_at')->get();
            $friendOf = $user->friendOf()->withPivot('created_at')->get();

            // Merge both and sort by friendship date (optional)
            $friends = $friendsOfMine->merge($friendOf)->sortByDesc(fn($f) => $f->pivot->created_at);

            return view('friends.list', compact('friends'));
        } catch (\Exception $e) {
            Log::error("Error fetching friend list: " . $e->getMessage());
            return back()->with('error', 'Could not retrieve your friends.');
        }
    }


    public function profile($id)
    {
        try {
            $authUserId = auth()->id();
            $profileUser = User::findOrFail($id);

            $authFriends = FriendRequest::where('accepted', true)
                ->where(function ($q) use ($authUserId) {
                    $q->where('sender_id', $authUserId)
                        ->orWhere('receiver_id', $authUserId);
                })
                ->get()
                ->map(fn($fr) => $fr->sender_id == $authUserId ? $fr->receiver_id : $fr->sender_id)
                ->toArray();

            $profileFriends = FriendRequest::where('accepted', true)
                ->where(function ($q) use ($id) {
                    $q->where('sender_id', $id)
                        ->orWhere('receiver_id', $id);
                })
                ->get()
                ->map(fn($fr) => $fr->sender_id == $id ? $fr->receiver_id : $fr->sender_id)
                ->toArray();

            $mutualFriendIds = array_values(array_filter(array_intersect($authFriends, $profileFriends), function ($fid) use ($authUserId, $id) {
                return $fid != $authUserId && $fid != $id;
            }));

            $mutualFriends = User::whereIn('id', $mutualFriendIds)->get();

            $friendStatus = $this->getFriendStatus($id);

            return view('users.profile', compact('profileUser', 'mutualFriends', 'friendStatus'));
        } catch (\Exception $e) {
            Log::error("Error loading profile: " . $e->getMessage());
            return back()->with('error', 'Could not load profile.');
        }
    }

    public function acceptFromSearch($senderId)
    {
        try {
            $friendRequest = FriendRequest::where('sender_id', $senderId)
                ->where('receiver_id', auth()->id())
                ->where('accepted', false)
                ->first();

            if ($friendRequest) {
                $friendRequest->update(['accepted' => true]);
                return back()->with('success', 'Friend request accepted.');
            }

            return back()->with('error', 'Friend request not found.');
        } catch (\Exception $e) {
            Log::error("Error accepting from search: " . $e->getMessage());
            return back()->with('error', 'Could not accept friend request.');
        }
    }

    public function getFriendStatus($otherUserId)
    {
        try {
            $authId = auth()->id();

            $existing = FriendRequest::where(function ($q) use ($authId, $otherUserId) {
                $q->where('sender_id', $authId)->where('receiver_id', $otherUserId);
            })->orWhere(function ($q) use ($authId, $otherUserId) {
                $q->where('sender_id', $otherUserId)->where('receiver_id', $authId);
            })->first();

            if (!$existing) return 'not_friends';
            if ($existing->accepted) return 'friends';
            if ($existing->sender_id == $authId) return 'request_sent';
            if ($existing->receiver_id == $authId) return 'request_received';

            return 'not_friends';
        } catch (\Exception $e) {
            Log::error("Error checking friend status: " . $e->getMessage());
            return 'error';
        }
    }
}
