@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            {{ $profileUser->name }}'s Profile
        </div>
        <div class="card-body">
            {{-- Friend Request Actions --}}
            @if ($profileUser->id !== auth()->id())
                {{-- Hide if viewing own profile --}}
                @if ($friendStatus === 'not_friends')
                    <form action="{{ route('friends.send', $profileUser->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-primary">Send Friend Request</button>
                    </form>
                @elseif ($friendStatus === 'request_sent')
                    <span class="badge bg-warning text-dark">Friend Request Sent</span>
                @elseif ($friendStatus === 'request_received')
                    <form action="{{ route('friends.acceptFromSearch', $profileUser->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success">Accept Friend Request</button>
                    </form>
                @elseif ($friendStatus === 'friends')
                    <span class="badge bg-success">You are friends</span>
                @endif
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Mutual Friends
        </div>
        <div class="card-body">
            <h5 class="card-title">Mutual Friends</h5>

            @if ($mutualFriends->isEmpty())
                <div class="alert alert-info">No mutual friends.</div>
            @else
                <ul class="list-group">
                    @foreach ($mutualFriends as $friend)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $friend->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($friend->name) . '&background=random&size=50' }}"
                                    alt="{{ $friend->name }} avatar" class="rounded-circle" width="50" height="50">

                                <div>
                                    <h6 class="mb-0">{{ $friend->name }}</h6>
                                    <small class="text-muted">{{ $friend->email }}</small><br>
                                    {{-- Example bio or status --}}
                                    <small class="text-muted fst-italic">
                                        {{ $friend->bio ?? 'No bio available' }}
                                    </small>
                                </div>
                            </div>

                            <a href="{{ route('user.profile', $friend->id) }}" class="btn btn-sm btn-outline-primary">
                                View Profile
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>


@endsection
