<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'My Social App')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background: #f0f2f5;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            height: 100vh;
            position: fixed;
            width: 250px;
            top: 0;
            left: 0;
            background-color: #fff;
            border-right: 1px solid #ddd;
            padding-top: 1rem;
        }

        .sidebar a {
            text-decoration: none;
            color: #050505;
            font-weight: 500;
            padding: 12px 20px;
            display: block;
            border-radius: 8px;
        }

        .sidebar a:hover {
            background-color: #e7f3ff;
            color: #1877f2;
        }

        .content-area {
            margin-left: 250px;
            padding: 1rem 2rem;
        }

        .topbar {
            height: 56px;
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            position: fixed;
            width: calc(100% - 250px);
            top: 0;
            left: 250px;
            z-index: 1030;
        }

        .topbar .search-input {
            width: 300px;
            border-radius: 20px;
            border: 1px solid #ddd;
            padding-left: 1rem;
            height: 36px;
        }

        .profile-menu img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
        }

        .rightbar {
            position: fixed;
            top: 56px;
            right: 0;
            width: 280px;
            height: calc(100vh - 56px);
            background: #fff;
            border-left: 1px solid #ddd;
            padding: 1rem;
            overflow-y: auto;
        }

        .friend-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .btn-primary-custom {
            background-color: #1877f2;
            border: none;
        }

        .btn-primary-custom:hover {
            background-color: #155db2;
        }
    </style>
    @stack('styles')
</head>

<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <a href="{{ route('dashboard') }}" class="mb-3 d-block fs-4 text-primary fw-bold px-3">MySocial</a>
        <a href="{{ route('dashboard') }}"><i class="bi bi-house-door-fill me-2"></i> Home</a>
        <a href="{{ route('friends.list') }}"><i class="bi bi-people-fill me-2"></i> Friends</a>
        <a href="{{ route('friend.requests') }}"><i class="bi bi-person-plus-fill me-2"></i> Friend Requests</a>
        <a href="{{ route('users.index') }}"><i class="bi bi-person-lines-fill me-2"></i> Users</a>
        <form action="{{ route('logout') }}" method="POST" class="mt-5 px-3">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
        </form>
    </nav>

    <!-- Topbar -->
    <header class="topbar d-flex justify-content-between align-items-center">
        <form method="GET" action="{{ route('dashboard') }}">
            <input type="search" name="search" placeholder="Search users" class="search-input"
                value="{{ request('search') }}" />
        </form>
        <div class="profile-menu d-flex align-items-center gap-2">
            <span class="d-none d-md-inline text-muted fw-semibold">{{ auth()->user()->name }}</span>
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random&size=36"
                alt="Profile" />
        </div>
    </header>

    <!-- Main Content -->
    <main class="content-area pt-5">

        @yield('content')

    </main>

    <!-- Right Sidebar -->
    <aside class="rightbar">
        <h6 class="fw-bold mb-3">👥 Friends</h6>
        @if ($friends->isEmpty())
            <p class="text-muted">No friends added yet.</p>
        @else
            <ul class="list-unstyled">
                @foreach ($friends as $friend)
                    <li class="d-flex align-items-center mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($friend->name) }}&background=random&size=40"
                            alt="{{ $friend->name }}" class="friend-avatar me-2" />
                        <a href="{{ route('user.profile', $friend->id) }}"
                            class="text-decoration-none">{{ $friend->name }}</a>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('friends.list') }}" class="btn btn-sm btn-outline-primary w-100">View all friends</a>
        @endif
    </aside>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
