@extends('layouts.auth')
@section('title','Login')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h4 class="mb-3">Login</h4>

        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
          @csrf

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required minlength="6">
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
          </div>

          <button class="btn btn-dark w-100">Login</button>
        </form>

        <div class="mt-3 text-center">
          <a href="{{ route('register') }}">Create an account</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection