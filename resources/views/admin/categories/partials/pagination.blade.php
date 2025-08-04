@if($categories->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
        Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} entries
    </div>
    <div class="pagination-container">
        {{ $categories->appends(request()->query())->links() }}
    </div>
</div>
@endif 