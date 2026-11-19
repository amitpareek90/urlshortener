<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ShortUrl::class);

        $shortUrls = ShortUrl::query()
            ->visibleTo($request->user())
            ->with(['user', 'company'])
            ->latest()
            ->get();

        return view('short-urls.index', [
            'shortUrls' => $shortUrls,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', ShortUrl::class);

        return view('short-urls.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', ShortUrl::class);

        $data = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
        ]);

        $shortUrl = ShortUrl::query()->create([
            'code' => ShortUrl::generateUniqueCode(),
            'original_url' => $data['original_url'],
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Short URL created.')
            ->with('created_code', $shortUrl->code);
    }
}
