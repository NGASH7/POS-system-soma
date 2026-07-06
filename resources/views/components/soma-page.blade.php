@props([
    'title',
    'subtitle' => null,
])

<div class="soma-page">
    <div class="soma-page-inner">
        @if ($title)
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-950">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
                    @endif
                </div>
                @isset($actions)
                    <div class="flex flex-wrap items-center gap-2">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
