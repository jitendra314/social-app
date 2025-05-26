@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <h2 class="mb-4">👋 Welcome back, <span class="text-primary">{{ auth()->user()->name }}</span>!</h2>

        <!-- Stats Summary -->
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card shadow-sm border-success">
                    <div class="card-body text-center text-success">
                        <h5 class="card-title fw-bold">👥 Total Friends</h5>
                        <p class="display-4">{{ $friends->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-warning">
                    <div class="card-body text-center text-warning">
                        <h5 class="card-title fw-bold">🕒 Pending Requests</h5>
                        <p class="display-4">{{ $requests->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-info">
                    <div class="card-body text-center text-info">
                        <h5 class="card-title fw-bold">💡 Suggestions</h5>
                        <p class="display-4">{{ $totalSuggestionsCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <!-- Search Users -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white fw-bold">🔍 Search Users</div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard') }}" class="mb-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by name"
                                    value="{{ $search ?? '' }}" autofocus>
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>

                        @if (isset($users) && $users->count())
                            <ul class="list-group">
                                @foreach ($users as $user)
                                    @if ($user->id !== auth()->id())
                                        <li class="list-group-item d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=40' }}"
                                                    alt="{{ $user->name }} avatar" class="rounded-circle" width="40"
                                                    height="40">

                                                <strong>{{ $user->name }}</strong>
                                            </div>
                                            <div>
                                                @if (auth()->user()->isFriendWith($user->id))
                                                    <span class="badge bg-success px-3 py-2">
                                                        <i class="bi bi-check2-circle me-1"></i> Friends
                                                    </span>
                                                @elseif (auth()->user()->hasReceivedRequestFrom($user->id))
                                                    <!-- Accept Friend Request -->
                                                    <form
                                                        action="{{ route('friend.request.accept.from.search', $user->id) }}"
                                                        method="GET" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm px-3">
                                                            <i class="bi bi-person-check"></i> Accept
                                                        </button>
                                                    </form>
                                                @elseif (auth()->user()->hasPendingRequestWith($user->id))
                                                    <!-- Cancel Friend Request -->
                                                    <span class="btn btn-warning btn-sm px-3">
                                                        <i class="bi bi-person-x"></i> Request Sent
                                                    </span>
                                                    <form action="{{ route('friend.cancel', $user->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-sm px-3"
                                                            onclick="return confirm('Cancel friend request?')">
                                                            <i class="bi bi-person-x"></i> Cancel
                                                        </button>
                                                    </form>
                                                @else
                                                    <!-- Send Friend Request -->
                                                    <a href="{{ route('friend.request.send', $user->id) }}"
                                                        class="btn btn-primary btn-sm"
                                                        title="Send a friend request to {{ $user->name }}">
                                                        + Add Friend
                                                    </a>
                                                @endif
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                            <a href="{{ route('users.index') }}" class="btn btn-link mt-3">🔗 View all users</a>
                        @elseif ($search)
                            <div class="alert alert-warning text-center">
                                <i class="bi bi-search"></i> No results found for "<strong>{{ $search }}</strong>".
                            </div>
                        @else
                            <p class="text-muted text-center">Start typing to search and connect with other users.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pending Friend Requests -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-dark fw-bold">🕒 Pending Friend Requests</div>
                    <div class="card-body">
                        @if ($requests->isEmpty())
                            <p class="text-center text-muted fs-6">You have no pending friend requests at this moment.</p>
                        @else
                            <ul class="list-group">
                                @foreach ($requests as $req)
                                    <li class="list-group-item d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $req->sender->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($req->sender->name) . '&background=random&size=40' }}"
                                                alt="{{ $req->sender->name }} avatar" class="rounded-circle" width="40"
                                                height="40">
                                            <strong>{{ $req->sender->name }}</strong>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('friend.request.accept', $req->id) }}" method="GET">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm px-3">
                                                    <i class="bi bi-check-circle"></i> Accept
                                                </button>
                                            </form>

                                            <form action="{{ route('friend.reject', $req->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm px-3"
                                                    onclick="return confirm('Reject friend request?')">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('friend.requests') }}" class="btn btn-link mt-3">🔗 View all requests</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- My Friends -->
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white fw-bold">👥 My Friends</div>
                    <div class="card-body">
                        @if ($friends->isEmpty())
                            <p class="text-center text-muted fs-6">You haven't added any friends yet.</p>
                        @else
                            <ul class="list-group">
                                @foreach ($friends as $friend)
                                    <li class="list-group-item d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $friend->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($friend->name) . '&background=random&size=40' }}"
                                                alt="{{ $friend->name }} avatar" class="rounded-circle" width="40"
                                                height="40">
                                            <strong>{{ $friend->name }}</strong>



                                        </div>
                                        <a href="{{ route('user.profile', $friend->id) }}"
                                            class="btn btn-outline-secondary btn-sm px-3">
                                            <i class="bi bi-person-circle"></i> View Profile
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('friends.list') }}" class="btn btn-link mt-3">🔗 View all friends</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Suggested Friends -->
        @if (isset($suggestions) && $suggestions->count())
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white fw-bold">💡 People You May Know</div>
                        <div class="card-body">
                            <div class="row g-4 justify-content-center">
                                @foreach ($suggestions as $user)
                                    @if ($user->id !== auth()->id() && !auth()->user()->isFriendWith($user->id))
                                        <div class="col-6 col-md-3 col-lg-2 text-center">
                                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=40' }}"
                                                alt="{{ $user->name }} avatar" class="rounded-circle" width="40"
                                                height="40">
                                            <h6>{{ $user->name }}</h6>
                                            <a href="{{ route('friend.request.send', $user->id) }}"
                                                class="btn btn-primary btn-sm"
                                                title="Send a friend request to {{ $user->name }}">
                                                + Add Friend
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
