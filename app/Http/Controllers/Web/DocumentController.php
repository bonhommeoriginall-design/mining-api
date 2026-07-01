<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Services\DocumentSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentSearchService $search,
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        if ($user?->isUtilisateur()) {
            abort(403, 'Accès réservé aux éditeurs et administrateurs.');
        }

        $statusFilter = request()->query('status');
        if (! in_array($statusFilter, [Document::STATUS_PUBLISHED, Document::STATUS_DRAFT], true)) {
            $statusFilter = null;
        }

        if ($user?->role->canManageDocuments()) {
            $query = Document::query()->latest();

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }
        } else {
            $query = Document::query()
                ->where('status', Document::STATUS_PUBLISHED)
                ->orderBy('title');
        }

        return view('documents.index', [
            'documents' => $query->paginate(20)->withQueryString(),
            'canManage' => $user?->role->canManageDocuments() ?? false,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function create(): View
    {
        return view('documents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,docx', 'max:51200'],
            'publish' => ['nullable', 'boolean'],
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store('documents', 'local');
        $checksum = hash_file('sha256', $uploaded->getRealPath());
        $publish = $request->boolean('publish');

        Document::query()->create([
            'title' => $data['title'],
            'storage_path' => $path,
            'original_filename' => $uploaded->getClientOriginalName(),
            'mime_type' => $uploaded->getClientMimeType(),
            'file_size' => $uploaded->getSize() ?: 0,
            'version' => 1,
            'checksum' => $checksum,
            'status' => $publish ? Document::STATUS_PUBLISHED : Document::STATUS_DRAFT,
            'published_at' => $publish ? now() : null,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('documents.index')
            ->with('status', 'Document ajouté avec succès.');
    }

    public function replace(Request $request, Document $document): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,docx', 'max:51200'],
            'publish' => ['nullable', 'boolean'],
        ]);

        $uploaded = $request->file('file');
        if ($document->storage_path) {
            Storage::disk('local')->delete($document->storage_path);
        }

        $path = $uploaded->store('documents', 'local');
        $checksum = hash_file('sha256', $uploaded->getRealPath());
        $publish = $request->boolean('publish', $document->isPublished());

        $document->update([
            'storage_path' => $path,
            'original_filename' => $uploaded->getClientOriginalName(),
            'mime_type' => $uploaded->getClientMimeType(),
            'file_size' => $uploaded->getSize() ?: 0,
            'version' => $document->version + 1,
            'checksum' => $checksum,
            'status' => $publish ? Document::STATUS_PUBLISHED : Document::STATUS_DRAFT,
            'published_at' => $publish ? now() : null,
        ]);

        $this->search->forgetDocumentCache($document->fresh());

        return redirect()
            ->route('documents.index')
            ->with('status', 'Document mis à jour (version '.$document->version.').');
    }

    public function publish(Document $document): RedirectResponse
    {
        $document->update([
            'status' => Document::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $this->search->forgetDocumentCache($document->fresh());

        return back()->with('status', 'Document publié.');
    }

    public function download(Document $document)
    {
        $user = auth()->user();

        if ($user?->isUtilisateur()) {
            abort(403);
        }

        if (! $user?->role->canManageDocuments() && ! $document->isPublished()) {
            abort(403);
        }

        if (! Storage::disk('local')->exists($document->storage_path)) {
            abort(404);
        }

        return response()->download(
            $document->absolutePath(),
            $document->original_filename,
        );
    }
}
