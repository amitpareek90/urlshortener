@extends('layouts.app')

@section('content')
    <h1>Log in</h1>
    <p class="muted">Super Admin: superadmin@example.com / password</p>
    <form class="stack card" method="POST" action="{{ route('login') }}">
        @csrf
        <label>
            Email
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        </label>
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <label>
            <span><input type="checkbox" name="remember" value="1"> Remember me</span>
        </label>
        <button type="submit">Log in</button>
    </form>
@endsection
