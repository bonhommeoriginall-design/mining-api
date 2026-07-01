<?php



namespace App\Http\Controllers\Web;



use App\Http\Controllers\Controller;

use App\Models\User;

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\Rule;

use Illuminate\View\View;



class SettingsController extends Controller

{

    private const SETTINGS_TABS = ['general', 'security', 'data', 'account'];



    public function index(): View|RedirectResponse

    {

        $user = auth()->user();

        $tab = request()->query('tab', 'general');

        $tab = in_array($tab, self::SETTINGS_TABS, true) ? $tab : 'general';



        if ($user?->isUtilisateur()) {

            return redirect()

                ->route('chat.index')

                ->with('settings_tab', $tab);

        }



        if (request()->query('view') !== 'full') {

            return $this->redirectStaffWithSettingsTab($tab);

        }



        $users = null;



        if ($user?->role->canManageUsers()) {

            $users = User::query()->orderBy('name')->paginate(15);

        }



        return view('settings.index', [

            'user' => $user,

            'users' => $users,

        ]);

    }



    public function updatePassword(Request $request): RedirectResponse

    {

        $data = $request->validate([

            'current_password' => ['required', 'current_password'],

            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);



        $request->user()->update([

            'password' => Hash::make($data['password']),

        ]);



        if ($request->user()->isUtilisateur()) {

            return redirect()

                ->route('chat.index')

                ->with('status', 'Mot de passe mis à jour avec succès.');

        }



        return $this->redirectStaffAfterUpdate('Mot de passe mis à jour avec succès.', 'security');

    }



    public function updateProfile(Request $request): RedirectResponse

    {

        $user = $request->user();



        $data = $request->validate([

            'name' => ['required', 'string', 'max:255'],

            'username' => [

                'required',

                'string',

                'min:3',

                'max:50',

                'regex:/^[a-zA-Z0-9_]+$/',

                Rule::unique('users')->ignore($user->id),

            ],

            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:2048'],

            'remove_avatar' => ['sometimes', 'boolean'],

        ], [

            'username.regex' => 'Le nom d’utilisateur ne peut contenir que des lettres, chiffres et underscores.',

            'avatar.image' => 'Le fichier doit être une image.',

            'avatar.max' => 'La photo ne doit pas dépasser 2 Mo.',

        ]);



        $updates = [

            'name' => $data['name'],

            'username' => $data['username'],

        ];



        if ($request->boolean('remove_avatar')) {

            $this->deleteUserAvatar($user);

            $updates['avatar_path'] = null;

        }



        if ($request->hasFile('avatar')) {

            $this->deleteUserAvatar($user);

            $updates['avatar_path'] = $request->file('avatar')->store('avatars/'.$user->id, 'public');

        }



        $user->update($updates);



        if ($user->isUtilisateur()) {

            return redirect()

                ->route('chat.index')

                ->with('status', 'Profil mis à jour avec succès.');

        }



        return $this->redirectStaffAfterUpdate('Profil mis à jour avec succès.', 'account');

    }



    private function redirectStaffWithSettingsTab(string $tab): RedirectResponse

    {

        $tab = in_array($tab, self::SETTINGS_TABS, true) ? $tab : 'general';

        $target = url()->previous();



        if (! $target || ! str_starts_with($target, url('/'))) {

            $target = route('dashboard');

        }



        return redirect()->to($target)->with('settings_tab', $tab);

    }



    private function redirectStaffAfterUpdate(string $status, string $tab): RedirectResponse

    {

        $target = url()->previous();



        if (! $target || ! str_starts_with($target, url('/'))) {

            $target = route('dashboard');

        }



        $redirect = redirect()->to($target)->with('status', $status);



        if (! str_contains($target, 'view=full')) {

            $redirect->with('settings_tab', in_array($tab, self::SETTINGS_TABS, true) ? $tab : 'general');

        }



        return $redirect;

    }



    private function deleteUserAvatar(User $user): void

    {

        if (! $user->avatar_path) {

            return;

        }



        Storage::disk('public')->delete($user->avatar_path);

    }

}

