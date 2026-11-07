<x-filament-panels::page>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-filament::section>
            <div class="text-2xl font-bold text-gray-950 dark:text-white">{{ number_format($stats['active_import_lcs'] ?? 0) }}</div>
            <div class="text-sm text-gray-500">Active Import LCs</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-2xl font-bold text-gray-950 dark:text-white">{{ number_format($stats['active_export_lcs'] ?? 0) }}</div>
            <div class="text-sm text-gray-500">Active Export LCs</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-2xl font-bold text-gray-950 dark:text-white">
                ${{ number_format((float) ($stats['outstanding_exposure'] ?? 0), 2) }}
            </div>
            <div class="text-sm text-gray-500">Outstanding exposure (approx. USD)</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-2xl font-bold text-warning-600">{{ number_format($stats['expiring_30_days'] ?? 0) }}</div>
            <div class="text-sm text-gray-500">Expiring in 30 days</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-2xl font-bold text-danger-600">{{ number_format($stats['open_discrepancies'] ?? 0) }}</div>
            <div class="text-sm text-gray-500">Open discrepancies</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-2xl font-bold text-danger-600">${{ number_format((float) ($stats['overdue_payments'] ?? 0), 2) }}</div>
            <div class="text-sm text-gray-500">Overdue payments</div>
        </x-filament::section>
    </div>

    @if (! empty($stats['currency_wise_exposure']))
        <x-filament::section class="mt-6">
            <x-slot name="heading">Currency-wise exposure</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="pb-2 pr-4">Currency</th>
                            <th class="pb-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stats['currency_wise_exposure'] as $currency => $amount)
                            <tr>
                                <td class="py-1 pr-4 font-medium">{{ $currency }}</td>
                                <td class="py-1">{{ number_format((float) $amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif

    <x-filament::section class="mt-6">
        <x-slot name="heading">Registers & exports</x-slot>
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Download the Import LC register as a Unicode PDF, or use the summary cards above for operational review.
        </p>
        <p class="text-sm text-gray-500">
            Upcoming maturities (30d): <strong>${{ number_format((float) ($stats['upcoming_maturities_30d'] ?? 0), 2) }}</strong>
        </p>
    </x-filament::section>
</x-filament-panels::page>
