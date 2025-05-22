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
                <ul class="list-group">
                    @foreach ($friends as $friend)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $friend->name }}
                            <a href="{{ route('user.profile', $friend->id) }}" class="btn btn-sm btn-primary">View Profile</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
