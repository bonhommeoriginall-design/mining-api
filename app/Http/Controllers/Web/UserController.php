<?php

namespace App\Http\Controllers\Web;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->to(route('settings.index').'#utilisateurs');
    }

    public function create(): View
    {
        return view('users.create', [
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_active' => true,
        ]);

        return redirect()
            ->to(route('settings.index').'#utilisateurs')
            ->with('status', 'Utilisateur créé.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $isSelf = $user->id === $request->user()->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if (! $isSelf) {
            $rules['role'] = ['required', Rule::enum(UserRole::class)];
            $rules['is_active'] = ['nullable', 'boolean'];
        }

        $data = $request->validate($rules);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! $isSelf) {
            $user->role = $data['role'];
            $user->is_active = $request->boolean('is_active');
        }

        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()
            ->to(route('settings.index').'#utilisateurs')
            ->with('status', 'Utilisateur mis à jour.');
    }
}
