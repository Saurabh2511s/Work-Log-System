<nav class="navbar navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="{{ route('dashboard') }}">WorkLogSystem</a>

  <div class="ms-auto d-flex align-items-center gap-3 text-white">
    <span>{{ auth()->user()->name }}</span>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-sm btn-outline-light">Logout</button>
    </form>
  </div>
</nav>