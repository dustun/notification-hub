<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ 'Состояние системы' }}
        </x-slot>

        <div class="grid gap-4 xl:grid-cols-2">
            @foreach ($checks as $check)
                <div class="rounded-2xl border border-gray-200 bg-white/90 p-5 shadow-sm ring-1 ring-black/5 dark:border-white/10 dark:bg-white/5 dark:ring-white/10">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                                {{ $check['label'] }}
                            </h3>

                            @if (filled($check['message']))
                                <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                    {{ $check['message'] }}
                                </p>
                            @endif
                        </div>

                        <x-filament::badge :color="$check['badge_color']">
                            {{ $check['status_label'] }}
                        </x-filament::badge>
                    </div>

                    @if (! empty($check['context_items']))
                        <dl class="mt-5 grid gap-3 md:grid-cols-2">
                            @foreach ($check['context_items'] as $item)
                                <div class="rounded-xl border border-gray-100 bg-gray-50/80 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                                    <dt class="text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">
                                        {{ $item['label'] }}
                                    </dt>
                                    <dd class="mt-2 break-all text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $item['value'] }}
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
