@php
    $currentLocale = app()->getLocale();
    $currentLabel = strtoupper($currentLocale);
@endphp

<x-filament::dropdown placement="bottom-end">
    <x-slot name="trigger">
        <x-filament::button
            size="sm"
            color="gray"
            icon="heroicon-m-language"
        >
            {{ $currentLabel }}
        </x-filament::button>
    </x-slot>

    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item>
            <form method="POST" action="{{ route('locale.switch', ['locale' => 'pt']) }}">
                @csrf
                <button type="submit" class="w-full text-left">
                    PT
                </button>
            </form>
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item>
            <form method="POST" action="{{ route('locale.switch', ['locale' => 'en']) }}">
                @csrf
                <button type="submit" class="w-full text-left">
                    EN
                </button>
            </form>
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>
