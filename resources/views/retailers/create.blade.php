@extends($layout)

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

    <div
        class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
        <div class="mx-auto w-full max-w-6xl">

            <h2 class="text-xl font-semibold mb-4  text-gray-800 dark:text-white/90">Create Retailer</h2>

            @include('retailers._form', [
            'action' => route($routePrefix.'.retailers.store'),
            'method' => 'POST',
            'buttonText' => 'Create',
            'retailer' => null
            ])

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    window.pageXData = {
        page: 'retailers',
    };
</script>

@endpush