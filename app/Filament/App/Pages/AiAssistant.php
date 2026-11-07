<?php

namespace App\Filament\App\Pages;

use App\Filament\Concerns\HasModuleGatedPage;
use App\Models\AiAssistantSession;
use App\Services\AiTradeFinanceService;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AiAssistant extends Page implements HasForms
{
    use HasModuleGatedPage;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Intelligence & Reports';

    protected static ?string $navigationLabel = 'AI Assistant';

    protected static ?string $slug = 'ai-assistant';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.app.pages.ai-assistant';

    public ?string $question = null;

    public ?int $sessionId = null;

    /** @var array<int, array{role: string, content: string, timestamp?: string}> */
    public array $messages = [];

    public static function getModuleSlug(): ?string
    {
        return 'ai_intelligence';
    }

    public function mount(): void
    {
        $tenant = Filament::getTenant();
        $user = Filament::auth()->user();

        if ($tenant && $user) {
            $session = AiAssistantSession::query()
                ->where('organization_id', $tenant->getKey())
                ->where('user_id', $user->getKey())
                ->latest()
                ->first();

            if ($session) {
                $this->sessionId = $session->id;
                $this->messages = $session->messages ?? [];
            }
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Textarea::make('question')
                    ->label('Your question')
                    ->placeholder('e.g. Which LCs expire this month? Any delayed shipments?')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function submitQuestion(AiTradeFinanceService $ai): void
    {
        $this->validate([
            'question' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $tenant = Filament::getTenant();
        $user = Filament::auth()->user();

        if (! $tenant || ! $user) {
            Notification::make()->title('Organization context required')->danger()->send();

            return;
        }

        $session = $this->sessionId
            ? AiAssistantSession::query()->find($this->sessionId)
            : null;

        $result = $ai->chatWithAssistant($tenant, $user, trim($this->question), $session);

        $this->sessionId = $result['session_id'];
        $session = AiAssistantSession::query()->find($this->sessionId);
        $this->messages = $session?->messages ?? [];
        $this->question = null;
        $this->form->fill(['question' => null]);
    }
}
