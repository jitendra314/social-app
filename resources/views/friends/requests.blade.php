@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            Pending Friend Requests
        </div>
        <div class="card-body">
            @if ($requests->isEmpty())
                <div class="alert alert-info">You have no pending friend requests.</div>
            @else
                <ul class="list-group">
                    @foreach ($requests as $req)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $req->sender->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($req->sender->name) . '&background=random&size=40' }}"
                                    alt="{{ $req->sender->name }} avatar" class="rounded-circle me-2" width="40"
                                    height="40">
                                <span>{{ $req->sender->name }}</span>
                            </div>


                            <div class="d-flex gap-2">
                                <a href="{{ route('friend.request.accept', $req->id) }}" class="btn btn-sm btn-success">
                                    Accept
                                </a>

                                <form action="{{ route('friend.reject', $req->id) }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Reject friend request?')">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
