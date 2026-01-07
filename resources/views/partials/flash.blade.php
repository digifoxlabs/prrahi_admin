<div class="space-y-3">
    @if (session('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            class="rounded-lg bg-green-100 px-4 py-3 text-green-800"
        >
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            class="rounded-lg bg-yellow-100 px-4 py-3 text-red-800"
        >
            {{ session('error') }}
        </div>
    @endif
</div>
