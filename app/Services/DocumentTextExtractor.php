<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use ZipArchive;

class DocumentTextExtractor
{
    public function extract(Document $document): string
    {
        if (! Storage::disk('local')->exists($document->storage_path)) {
            return '';
        }

        $path = $document->absolutePath();
        $filename = strtolower($document->original_filename);

        $raw = match (true) {
            str_ends_with($filename, '.docx') => $this->extractDocx($path),
            str_ends_with($filename, '.pdf') => $this->extractPdf($path),
            default => '',
        };

        return DocumentTextNormalizer::normalize($raw);
    }

    private function extractDocx(string $path): string
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            return '';
        }

        $xml = preg_replace('/<w:tab[^>]*\/>/', ' ', $xml) ?? $xml;
        $xml = preg_replace('/<w:br[^>]*\/>/', "\n", $xml) ?? $xml;
        $xml = preg_replace('/<\/w:p>/', "\n", $xml) ?? $xml;
        $text = strip_tags($xml);

        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function extractPdf(string $path): string
    {
        try {
            $parser = new PdfParser;

            return $parser->parseFile($path)->getText();
        } catch (\Throwable) {
            return '';
        }
    }
}
