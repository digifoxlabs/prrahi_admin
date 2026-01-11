@if (session()->has('toast'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof toastr !== 'undefined') {
        const toast = @json(session('toast'));
        toastr[toast.type](toast.message);
    } else {
        alert(@json(session('toast.message')));
    }
});
</script>
@endif