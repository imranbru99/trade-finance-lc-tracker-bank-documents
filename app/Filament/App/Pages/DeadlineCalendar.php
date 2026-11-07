<?php

namespace App\Filament\App\Pages;

use App\Filament\Concerns\HasModuleGatedPage;
use App\Models\Deadline;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class DeadlineCalendar extends Page
{
    use HasModuleGatedPage;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Workflow & Operations';

    protected static ?string $navigationLabel = 'Deadline Calendar';

    protected static ?string $slug = 'deadline-calendar';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.app.pages.deadline-calendar';

    public int $month;

    public int $year;

    public ?string $statusFilter = null;

    public ?string $deadlineTypeFilter = null;

    public static function getModuleSlug(): ?string
    {
        return 'calendar';
    }

    public function mount(): void
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    /**
     * @return Collection<int, Collection<int, Deadline>>
     */
    public function getDeadlinesGroupedProperty(): Collection
    {
        $tenant = Filament::getTenant();
        if (! $tenant) {
            return collect();
        }

        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $query = Deadline::query()
            ->where('organization_id', $tenant->getKey())
            ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('due_date')
            ->orderBy('priority');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->deadlineTypeFilter) {
            $query->where('deadline_type', $this->deadlineTypeFilter);
        }

        return $query->get()->groupBy(fn (Deadline $d) => $d->due_date->toDateString());
    }

    protected function getViewData(): array
    {
        $start = Carbon::create($this->year, $this->month, 1);

        return [
            'monthLabel' => $start->format('F Y'),
            'daysInMonth' => $start->daysInMonth,
            'firstDayOfWeek' => $start->copy()->startOfMonth()->dayOfWeek,
        ];
    }
}
