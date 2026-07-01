<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        $documents = Document::query()
            ->where('status', Document::STATUS_PUBLISHED)
            ->orderBy('title')
            ->get()
            ->map(fn (Document $doc) => array_merge(
                $doc->toSyncArray(),
                ['download_url' => route('api.documents.download', $doc)],
            ));

        return response()->json([
            'synced_at' => now()->toIso8601String(),
            'documents' => $documents,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        return $this->sync($request);
    }

    public function download(Document $document): BinaryFileResponse|JsonResponse
    {
        if (! $document->isPublished()) {
            return response()->json(['message' => 'Document non publié.'], 404);
        }

        if (! Storage::disk('local')->exists($document->storage_path)) {
            return response()->json(['message' => 'Fichier introuvable.'], 404);
        }

        return response()->download(
            $document->absolutePath(),
            $document->original_filename,
        );
    }
}
