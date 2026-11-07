<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Trade Finance AI Assistant</x-slot>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Ask natural-language questions about expiries, payments, shipments, and portfolio activity for your organization.
            </p>
            <p class="mt-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-200">
                <strong>Disclaimer:</strong> AI suggestions require human review before affecting official LC records.
            </p>
        </x-filament::section>

        <form wire:submit="submitQuestion" class="space-y-4">
            {{ $this->form }}

            <x-filament::button type="submit" icon="heroicon-o-paper-airplane">
                Send question
            </x-filament::button>
        </form>

        <x-filament::section>
            <x-slot name="heading">Conversation</x-slot>

            <div class="max-h-[32rem] space-y-4 overflow-y-auto">
                @forelse ($messages as $message)
                    <div class="@if (($message['role'] ?? '') === 'user') text-right @endif">
                        <div class="inline-block max-w-[85%] rounded-xl px-4 py-3 text-sm @if (($message['role'] ?? '') === 'user') bg-primary-600 text-white @else bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100 @endif">
                            <div class="mb-1 text-xs font-semibold uppercase opacity-70">
                                {{ ($message['role'] ?? 'assistant') === 'user' ? 'You' : 'Assistant' }}
                            </div>
                            <div class="whitespace-pre-wrap">{{ $message['content'] ?? '' }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-500">No messages yet. Ask a question to start the session.</p>
                @endforelse
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
