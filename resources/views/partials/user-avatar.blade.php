@php
    /** @var \App\Models\User $user */
    $class = $class ?? 'agent-sidebar-avatar';
    $avatarUrl = $user->avatarUrl();
@endphp

@if ($avatarUrl)
    <img src="{{ $avatarUrl }}" alt="" class="{{ $class }} user-avatar-photo" aria-hidden="true">
@else
    <span class="{{ $class }} user-avatar-initials" style="background-color: {{ $user->avatarColor() }}" aria-hidden="true">{{ $user->initials() }}</span>
@endif
