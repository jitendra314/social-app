@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            My Friends
        </div>
        <div class="card-body">
            @if ($friends->isEmpty())
                <div class="alert alert-info">You have no friends yet.</div>
            @else
                <div class="row">
                    @foreach ($friends as $friend)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex">
                                    <img src="{{ $friend->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($friend->name) . '&background=random&size=60' }}"
                                        alt="{{ $friend->name }} avatar" class="rounded-circle me-3" width="60"
                                        height="60">

                                    <div>
                                        <h5 class="card-title mb-1">{{ $friend->name }}</h5>
                                        <small class="text-muted">{{ $friend->mutualFriends()->count() }} mutual
                                            friends</small><br>
                                        @if ($friend->pivot && $friend->pivot->created_at)
                                            <small class="text-muted">Friends since
                                                {{ $friend->pivot->created_at->format('M Y') }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                    <a href="{{ route('user.profile', $friend->id) }}" class="btn btn-sm btn-primary">View
                                        Profile</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
