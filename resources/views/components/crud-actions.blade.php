@props([
    'edit' => null,
    'delete' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'confirm' => 'Yakin hapus data ini?',
    'editLabel' => 'Edit',
    'deleteLabel' => 'Delete',
])

<div {{ $attributes->merge(['class' => 'd-flex gap-1 flex-wrap']) }}>
    @if ($editUrl && $edit)
        @can($edit)
            <a href="{{ $editUrl }}" class="btn btn-info btn-sm">
                <i class="bx bxs-edit"></i> {{ $editLabel }}
            </a>
        @endcan
    @endif

    @if ($deleteUrl && $delete)
        @can($delete)
            <form action="{{ $deleteUrl }}" method="post"
                onsubmit="return confirm(@js($confirm))">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bx bxs-trash"></i> {{ $deleteLabel }}
                </button>
            </form>
        @endcan
    @endif

    {{ $slot }}
</div>
