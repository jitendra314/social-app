@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            {{ $profileUser->name }}'s Profile
        </div>
        <div class="card-body">
            <h5 class="card-title">Mutual Friends</h5>

            @if ($mutualFriends->isEmpty())
                <div class="alert alert-info">No mutual friends.</div>
            @else
                <ul class="list-group">
                    @foreach ($mutualFriends as $friend)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $friend->name }}
                            <a href="{{ route('user.profile', $friend->id) }}" class="btn btn-sm btn-outline-primary">View
                                Profile</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
