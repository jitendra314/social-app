@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Welcome, {{ auth()->user()->name }}!</h2>

        <div class="row">
            <!-- Search Users -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        Search Users
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard') }}">
                            <div class="input-group mb-3">
                                <input type="text" name="search" class="form-control" placeholder="Search users by name"
                                    value="{{ $search ?? '' }}">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>

                        @if (isset($users) && $users->count())
                            <ul class="list-group">
                                @foreach ($users as $user)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $user->name }}
                                        @if (auth()->user()->isFriendWith($user->id))
                                            <span class="badge bg-success">Friends</span>
                                        @elseif (auth()->user()->hasPendingRequestWith($user->id))
                                            <span class="badge bg-warning text-dark">Request Sent</span>
                                        @elseif (auth()->user()->hasReceivedRequestFrom($user->id))
                                            <a href="{{ route('friend.request.accept.from.search', $user->id) }}"
                                                class="btn btn-sm btn-success">Accept Request</a>
                                        @else
                                            <a href="{{ route('friend.request.send', $user->id) }}"
                                                class="btn btn-sm btn-primary">Add Friend</a>
                                        @endif

                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('users.index') }}" class="btn btn-link mt-2">View all users</a>
                        @elseif($search)
                            <p>No users found for "{{ $search }}"</p>
                        @else
                            <p>Type a name above to search users.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pending Friend Requests -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark">
                        Pending Friend Requests
                    </div>
                    <div class="card-body">
                        @if ($requests->isEmpty())
                            <p>No pending friend requests.</p>
                        @else
                            <ul class="list-group">
                                @foreach ($requests as $req)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $req->sender->name }}
                                        <a href="{{ route('friend.request.accept', $req->id) }}"
                                            class="btn btn-sm btn-success">Accept</a>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('friend.requests') }}" class="btn btn-link mt-2">View all friend requests</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- My Friends -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        My Friends
                    </div>
                    <div class="card-body">
                        @if ($friends->isEmpty())
                            <p>You have no friends yet.</p>
                        @else
                            <ul class="list-group">
                                @foreach ($friends as $friend)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $friend->name }}
                                        <a href="{{ route('user.profile', $friend->id) }}"
                                            class="btn btn-sm btn-primary">View Profile</a>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('friends.list') }}" class="btn btn-link mt-2">View all friends</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
