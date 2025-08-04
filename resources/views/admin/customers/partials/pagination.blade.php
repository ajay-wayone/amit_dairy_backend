@if($customers->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
        Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} entries
    </div>
    <div class="pagination-container">
        {{ $customers->appends(request()->query())->links() }}
    </div>
</div>
@endif 