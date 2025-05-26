@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">🔍 Search Users</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                <input type="text" name="search" class="form-control form-control-lg"
                    placeholder="Enter name to search users..." value="{{ request('search') }}" autofocus>
            </form>

            @if ($users->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No users found matching your search criteria.
                </div>
            @else
                @foreach ($users as $user)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $user->name }}</strong>
                                <br>
                                <small class="text-muted">Joined {{ $user->created_at->diffForHumans() }}</small>
                            </div>
                            <div>
                                @if (in_array($user->id, $friendIds))
                                    <button class="btn btn-success btn-sm" disabled data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="You are already friends with {{ $user->name }}">
                                        ✓ Friends
                                    </button>
                                @elseif (in_array($user->id, $pendingIds))
                                    <button class="btn btn-warning btn-sm text-dark" disabled data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Friend request sent and awaiting confirmation">
                                        ⏳ Request Sent
                                    </button>
                                @elseif (in_array($user->id, $receivedRequestIds))
                                    <a href="{{ route('friend.request.accept', $user->id) }}" class="btn btn-success btn-sm"
                                        title="Accept friend request from {{ $user->name }}">
                                        🤝 Accept Request
                                    </a>
                                @else
                                    <a href="{{ route('friend.request.send', $user->id) }}" class="btn btn-primary btn-sm"
                                        title="Send a friend request to {{ $user->name }}">
                                        + Add Friend
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Enable Bootstrap tooltips --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection
