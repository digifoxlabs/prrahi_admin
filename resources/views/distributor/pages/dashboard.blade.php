@extends('distributor.layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">


     <div class="grid grid-cols-12 gap-4 md:gap-6">



        </div>
@endsection

    @push('scripts')
        <script>
            window.pageXData = {
                page: 'dashboard',
            };
        </script>

    @endpush
