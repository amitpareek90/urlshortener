<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function create(): View
    {
        return view('invitations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $actor = $request->user();

        if ($actor->isSuperAdmin()) {
            $data = $request->validate([
                'company_name' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $company = Company::query()->create([
                'name' => $data['company_name'],
            ]);

            User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => User::ROLE_ADMIN,
                'company_id' => $company->id,
            ]);

            return redirect()
                ->route('invitations.create')
                ->with('status', 'Admin invited to '.$company->name.'. They can log in with the password you set.');
        }

        if ($actor->isAdmin()) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_MEMBER])],
            ]);

            User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $data['role'],
                'company_id' => $actor->company_id,
            ]);

            $label = $data['role'] === User::ROLE_ADMIN ? 'Admin' : 'Member';

            return redirect()
                ->route('invitations.create')
                ->with('status', $label.' invited to '.$actor->company->name.'.');
        }

        abort(403);
    }
}
