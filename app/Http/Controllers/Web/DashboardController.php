<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user?->isUtilisateur()) {
            return redirect()->route('chat.index');
        }

        return view('dashboard', [
            'publishedCount' => Document::query()->where('status', Document::STATUS_PUBLISHED)->count(),
            'draftCount' => Document::query()->where('status', Document::STATUS_DRAFT)->count(),
            'canManageDocuments' => $user?->role->canManageDocuments() ?? false,
            'canManageUsers' => $user?->role->canManageUsers() ?? false,
        ]);
    }
}
