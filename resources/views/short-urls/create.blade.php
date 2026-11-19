@extends('layouts.app')

@section('content')
    <h1>Create short URL</h1>
    <form class="stack card" method="POST" action="{{ route('short-urls.store') }}">
        @csrf
        <label>
            Original URL
            <input type="url" name="original_url" value="{{ old('original_url') }}" placeholder="https://example.com/page" required>
        </label>
        @error('original_url')
            <p class="error">{{ $message }}</p>
        @enderror
        <div>
            <button type="submit">Create</button>
            <a class="button secondary" href="{{ route('dashboard') }}">Cancel</a>
        </div>
    </form>
@endsection
