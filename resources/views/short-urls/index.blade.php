@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $roleLabel = match ($user->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'member' => 'Member',
            default => $user->role,
        };
    @endphp

    <h1>Short URLs</h1>
    <p class="muted">
        {{ $roleLabel }}
        @if ($user->company)
            · {{ $user->company->name }}
        @endif
    </p>

    <p>
        @if ($user->canCreateShortUrls())
            <a class="button" href="{{ route('short-urls.create') }}">Create short URL</a>
        @endif
        @if ($user->isSuperAdmin() || $user->isAdmin())
            <a class="button secondary" href="{{ route('invitations.create') }}">Invite user</a>
        @endif
    </p>

    @if ($shortUrls->isEmpty())
        <p class="card">No short URLs to show.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Short URL</th>
                    <th>Original</th>
                    <th>Created by</th>
                    @if ($user->isSuperAdmin())
                        <th>Company</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($shortUrls as $shortUrl)
                    <tr>
                        <td>
                            <a href="{{ url($shortUrl->code) }}">{{ url($shortUrl->code) }}</a>
                            @if (session('created_code') === $shortUrl->code)
                                <div class="muted">Just created</div>
                            @endif
                        </td>
                        <td>{{ $shortUrl->original_url }}</td>
                        <td>{{ $shortUrl->user->name }}</td>
                        @if ($user->isSuperAdmin())
                            <td>{{ $shortUrl->company->name }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
