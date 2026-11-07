<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ShipmentResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\ImportLc;
use App\Models\Shipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Trade Finance Operations';

    protected static ?string $navigationLabel = 'Shipments';

    protected static ?int $navigationSort = 5;

    public static function getModuleSlug(): ?string
    {
        return 'shipment_tracking';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Shipment Details')->tabs([
                    Forms\Components\Tabs\Tab::make('Vessel & Booking')->schema([
                        Forms\Components\TextInput::make('shipment_reference')
                            ->label('Shipment Ref')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('shipment_number')
                            ->label('Partial Shipment Sequence')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\MorphToSelect::make('trackable')
                            ->label('Related Letter of Credit')
                            ->types([
                                Forms\Components\MorphToSelect\Type::make(ImportLc::class)
                                    ->titleAttribute('lc_number'),
                            ])
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Planned' => 'Planned',
                                'Booked' => 'Booked',
                                'Ready for Shipment' => 'Ready for Shipment',
                                'Shipped' => 'Shipped',
                                'In Transit' => 'In Transit',
                                'Arrived' => 'Arrived at Destination Port',
                                'Customs Processing' => 'Customs Processing',
                                'Delivered' => 'Delivered',
                                'Delayed' => 'Delayed',
                                'Cancelled' => 'Cancelled',
                            ])
                            ->default('Planned')
                            ->required(),
                        Forms\Components\TextInput::make('carrier')
                            ->label('Shipping Line / Carrier'),
                        Forms\Components\TextInput::make('freight_forwarder')
                            ->label('Freight Forwarder'),
                        Forms\Components\TextInput::make('vessel_name')
                            ->label('Vessel & Voyage Name'),
                        Forms\Components\TextInput::make('bill_of_lading_number')
                            ->label('Bill of Lading / Airway Bill No'),
                        Forms\Components\TextInput::make('container_number')
                            ->label('Container Numbers (e.g. MSCU1234567)'),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Schedule & Cargo')->schema([
                        Forms\Components\TextInput::make('port_of_loading')
                            ->label('Port of Loading'),
                        Forms\Components\TextInput::make('port_of_discharge')
                            ->label('Port of Discharge'),
                        Forms\Components\DatePicker::make('etd')
                            ->label('Estimated Departure (ETD)'),
                        Forms\Components\DatePicker::make('eta')
                            ->label('Estimated Arrival (ETA)'),
                        Forms\Components\DatePicker::make('actual_departure_date')
                            ->label('Actual Departure Date'),
                        Forms\Components\DatePicker::make('actual_arrival_date')
                            ->label('Actual Arrival Date'),
                        Forms\Components\TextInput::make('shipment_value')
                            ->label('Cargo Consignment Value')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('currency')
                            ->default('USD'),
                        Forms\Components\Textarea::make('goods_description')
                            ->label('Goods Summary & Packaging')
                            ->columnSpanFull(),
                    ])->columns(2),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shipment_reference')
                    ->label('Shipment Ref')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('vessel_name')
                    ->label('Vessel / Carrier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bill_of_lading_number')
                    ->label('B/L #')
                    ->searchable(),
                Tables\Columns\TextColumn::make('eta')
                    ->label('ETA')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => ['Planned', 'Booked'],
                        'info' => ['Ready for Shipment', 'Shipped', 'In Transit'],
                        'warning' => 'Customs Processing',
                        'success' => ['Arrived', 'Delivered'],
                        'danger' => ['Delayed', 'Cancelled'],
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'In Transit' => 'In Transit',
                        'Arrived' => 'Arrived',
                        'Customs Processing' => 'Customs Processing',
                        'Delivered' => 'Delivered',
                        'Delayed' => 'Delayed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}
