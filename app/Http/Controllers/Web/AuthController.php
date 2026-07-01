<?php

namespace App\Http\Controllers\Web;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()?->isUtilisateur() ? 'chat.index' : 'dashboard');
        }

        return view('auth.sliding', ['panel' => 'login']);
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()?->isUtilisateur() ? 'chat.index' : 'dashboard');
        }

        return view('auth.sliding', ['panel' => 'register']);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::Utilisateur,
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('chat.index')
            ->with('status', 'Compte créé. Bienvenue sur MINING IA !');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Identifiants incorrects.']);
        }

        $request->session()->regenerate();
        $user = Auth::user();
        if ($user !== null && ! $user->is_active) {
            Auth::logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Ce compte est désactivé.']);
        }

        return redirect()->intended(
            $user?->isUtilisateur() ? route('chat.index') : route('dashboard')
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
