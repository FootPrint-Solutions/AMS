<div class="btn-group">
    {{-- Edit --}}
    <button class="btn btn-outline-primary btn-sm edit-selected" data-url={{ $editUrl }}
        data-id={{ $idIdx }}>
        <i class="fas fa-pencil"></i> Edit
    </button>

    {{-- Delete --}}
    <button class="btn btn-outline-danger btn-sm delete-selected ml-1" data-url={{ $deleteUrl }}
        data-id={{ $idIdx }}>
        <i class="fas fa-trash"></i> Delete
    </button>
</div>
