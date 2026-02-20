<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','WorkLogSystem')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-3">
  <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">WorkLogSystem</a>

  <div class="d-flex align-items-center gap-3 text-white">
    <span>{{ auth()->user()->name ?? '' }}</span>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
    </form>
  </div>
</nav>

<div class="container-fluid mt-3">
  <div class="row">

    <aside class="col-md-2 mb-3">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-3">Menu</h6>
          <div class="list-group">
            <a class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">Dashboard</a>

            <a class="list-group-item list-group-item-action {{ request()->routeIs('time_logs.*') ? 'active' : '' }}"
               href="{{ route('time_logs.index') }}">Time Logs</a>

            <a class="list-group-item list-group-item-action {{ request()->routeIs('leaves.*') ? 'active' : '' }}"
               href="{{ route('leaves.index') }}">Leaves</a>
          </div>
        </div>
      </div>
    </aside>

    <main class="col-md-10">
      {{-- ✅ Flash messages shown ONCE (global) --}}
      @include('layouts.partials.flash')

      @yield('content')
    </main>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>