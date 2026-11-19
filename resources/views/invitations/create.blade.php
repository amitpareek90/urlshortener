@extends('layouts.app')

@section('content')
    <h1>Invite user</h1>

    @if (auth()->user()->isSuperAdmin())
        <p class="muted">Create a company and invite its Admin.</p>
        <form class="stack card" method="POST" action="{{ route('invitations.store') }}">
            @csrf
            <label>
                Company name
                <input type="text" name="company_name" value="{{ old('company_name') }}" required>
            </label>
            @error('company_name')
                <p class="error">{{ $message }}</p>
            @enderror
            <label>
                Admin name
                <input type="text" name="name" value="{{ old('name') }}" required>
            </label>
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
            <label>
                Email
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
            <label>
                Password
                <input type="password" name="password" required>
            </label>
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
            <label>
                Confirm password
                <input type="password" name="password_confirmation" required>
            </label>
            <div>
                <button type="submit">Invite admin</button>
                <a class="button secondary" href="{{ route('dashboard') }}">Back</a>
            </div>
        </form>
    @else
        <p class="muted">Invite an Admin or Member to {{ auth()->user()->company->name }}.</p>
        <form class="stack card" method="POST" action="{{ route('invitations.store') }}">
            @csrf
            <label>
                Name
                <input type="text" name="name" value="{{ old('name') }}" required>
            </label>
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
            <label>
                Email
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
            <label>
                Role
                <select name="role" required>
                    <option value="member" @selected(old('role', 'member') === 'member')>Member</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                </select>
            </label>
            @error('role')
                <p class="error">{{ $message }}</p>
            @enderror
            <label>
                Password
                <input type="password" name="password" required>
            </label>
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
            <label>
                Confirm password
                <input type="password" name="password_confirmation" required>
            </label>
            <div>
                <button type="submit">Invite</button>
                <a class="button secondary" href="{{ route('dashboard') }}">Back</a>
            </div>
        </form>
    @endif
@endsection
