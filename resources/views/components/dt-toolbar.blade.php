<div class="btn-group">
    {{-- Edit --}}
    <button class="btn btn-outline-primary btn-sm edit-selected" data-url={{ $editUrl }}
        data-id={{ $idIdx }}>
        <i class="fas fa-pencil"></i> Edit
    </button>

    {{-- Delete or Toggle --}}
    @if (isset($deleteUrl) && $deleteUrl !== '' && $deleteUrl !== null)
        <button class="btn btn-outline-danger btn-sm delete-selected ml-1" data-url={{ $deleteUrl }}
            data-id={{ $idIdx }}>
            <i class="fas fa-trash"></i> Delete
        </button>
    @else
        <button class="btn btn-outline-info btn-sm toggle-selected ml-1" data-url={{ $toggleUrl }}
            data-id={{ $idIdx }}>
            <i class="fas fa-circle-dot"></i> Toggle Status
        </button>
    @endif
</div>
