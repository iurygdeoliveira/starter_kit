<x-filament::page>
    {{ $this->form }}

    <div class="mt-4">
        <x-filament::button wire:click="submit">
            Salvar
        </x-filament::button>
    </div>
</x-filament::page>
