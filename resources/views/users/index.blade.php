@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            Search Users
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="mb-3">
                <input type="text" name="search" class="form-control" placeholder="Search by name"
                    value="{{ request('search') }}">
            </form>

            @if ($users->isEmpty())
                <div class="alert alert-info">No users found.</div>
            @else
                @foreach ($users as $user)
                    <div class="card mb-2">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <span>{{ $user->name }}</span>
                            <div>
                                @if (in_array($user->id, $friendIds))
                                    <button class="btn btn-success btn-sm" disabled>Friends</button>
                                @elseif (in_array($user->id, $pendingIds))
                                    <button class="btn btn-warning btn-sm" disabled>Request Sent</button>
                                @elseif (in_array($user->id, $receivedRequestIds))
                                    <a href="{{ route('friend.request.accept', $user->id) }}"
                                        class="btn btn-sm btn-success">Accept</a>
                                @else
                                    <a href="{{ route('friend.request.send', $user->id) }}"
                                        class="btn btn-primary btn-sm">Add Friend</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
