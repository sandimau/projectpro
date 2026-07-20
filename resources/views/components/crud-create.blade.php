@props([
    'permission',
    'url',
    'label' => 'Tambah',
])

@can($permission)
    <a href="{{ $url }}" {{ $attributes->merge(['class' => 'btn btn-primary']) }}>
        {{ $label }}
    </a>
@endcan
