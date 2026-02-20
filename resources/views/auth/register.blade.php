@extends('layouts.auth')
@section('title','Register')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h4 class="mb-3">Register</h4>

        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}" id="regForm">
          @csrf

          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required maxlength="100">
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required minlength="6">
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="6">
          </div>

          <button class="btn btn-dark w-100">Register</button>
        </form>

        <div class="mt-3 text-center">
          <a href="{{ route('login') }}">Back to login</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection