<x-filament-panels::page>
    <div class="max-w-xl mx-auto space-y-6">
        <div class="p-6 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-600 dark:text-amber-400 space-y-2">
            <h3 class="font-bold text-lg">Action Required: Password Change</h3>
            <p class="text-sm">You are logged in with an auto-generated or temporary password. For security purposes, you must change your password before accessing any other sections of the panel.</p>
        </div>

        <form wire:submit="updatePassword" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-800">
            {{ $this->form }}

            <div class="pt-4 flex justify-end">
                <x-filament::button type="submit" color="primary">
                    Update Password & Continue
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
