<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'storage_path',
    'original_filename',
    'mime_type',
    'file_size',
    'version',
    'checksum',
    'status',
    'published_at',
    'uploaded_by',
])]
class Document extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'file_size' => 'integer',
            'version' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function absolutePath(): string
    {
        return Storage::disk('local')->path($this->storage_path);
    }

    public function toSyncArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'filename' => $this->original_filename,
            'version' => $this->version,
            'checksum' => $this->checksum,
            'file_size' => $this->file_size,
            'mime_type' => $this->mime_type,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
