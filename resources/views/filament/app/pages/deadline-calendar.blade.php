<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex items-center gap-2">
                <x-filament::button wire:click="previousMonth" color="gray" size="sm" icon="heroicon-m-chevron-left" />
                <span class="text-lg font-semibold text-gray-950 dark:text-white">{{ $monthLabel }}</span>
                <x-filament::button wire:click="nextMonth" color="gray" size="sm" icon="heroicon-m-chevron-right" />
            </div>

            <div class="flex flex-wrap gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select wire:model.live="statusFilter" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-900">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="overdue">Overdue</option>
                        <option value="waived">Waived</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Deadline type</label>
                    <select wire:model.live="deadlineTypeFilter" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-900">
                        <option value="">All</option>
                        <option value="lc_expiry">LC Expiry</option>
                        <option value="latest_shipment">Latest Shipment</option>
                        <option value="presentation_deadline">Document Presentation</option>
                        <option value="payment_maturity">Payment Maturity</option>
                        <option value="discrepancy_resolution">Discrepancy Resolution</option>
                        <option value="insurance_expiry">Insurance Expiry</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mb-4 grid grid-cols-7 gap-1 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div>{{ $day }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-1">
                @for ($pad = 0; $pad < $firstDayOfWeek; $pad++)
                    <div class="min-h-24 rounded-lg bg-gray-50 dark:bg-gray-800/50"></div>
                @endfor

                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $dayDeadlines = $this->deadlinesGrouped->get($dateKey, collect());
                    @endphp
                    <div class="min-h-24 rounded-lg border border-gray-100 p-2 text-sm dark:border-gray-800 {{ $dayDeadlines->isNotEmpty() ? 'bg-primary-50/50 dark:bg-primary-950/20' : 'bg-white dark:bg-gray-900' }}">
                        <div class="mb-1 font-semibold text-gray-900 dark:text-white">{{ $day }}</div>
                        <ul class="space-y-1">
                            @foreach ($dayDeadlines->take(3) as $deadline)
                                <li class="truncate rounded px-1 text-xs text-gray-700 dark:text-gray-300" title="{{ $deadline->title }}">
                                    <span class="font-medium">{{ $deadline->title }}</span>
                                    <span class="text-gray-500">· {{ str_replace('_', ' ', $deadline->deadline_type) }}</span>
                                </li>
                            @endforeach
                            @if ($dayDeadlines->count() > 3)
                                <li class="text-xs text-gray-500">+{{ $dayDeadlines->count() - 3 }} more</li>
                            @endif
                        </ul>
                    </div>
                @endfor
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-950 dark:text-white">Deadlines this month (detail)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Priority</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($this->deadlinesGrouped->flatten()->sortBy('due_date') as $deadline)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $deadline->due_date->format('M j, Y') }}</td>
                                <td class="px-4 py-2">{{ $deadline->title }}</td>
                                <td class="px-4 py-2">{{ str_replace('_', ' ', $deadline->deadline_type) }}</td>
                                <td class="px-4 py-2 capitalize">{{ $deadline->status }}</td>
                                <td class="px-4 py-2 capitalize">{{ $deadline->priority }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">No deadlines for the selected filters this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
