<div>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="fi-form-actions flex justify-end">
            <x-filament::button type="submit">
                {{ __('filament-edit-profile::default.save') }}
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
