<div class="alert {{ $type ?? 'success' }}" role="status" data-auto-dismiss="6000">
    <span class="alert-icon" aria-hidden="true">{{ ($type ?? 'success') === 'error' ? '!' : '✓' }}</span>
    <span class="alert-text">{{ $message }}</span>
    <button type="button" class="alert-dismiss" data-alert-dismiss aria-label="Fermer">&times;</button>
</div>
