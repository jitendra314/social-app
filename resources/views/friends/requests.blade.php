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
                            {{ $req->sender->name }}
                            <a href="{{ route('friend.request.accept', $req->id) }}" class="btn btn-sm btn-success">Accept</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
