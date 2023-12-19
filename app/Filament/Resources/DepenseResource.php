<?php

namespace App\Filament\Resources;

use App\Enums\isActive;
use App\Filament\Resources\DepenseResource\Pages;
use App\Filament\Resources\DepenseResource\RelationManagers;
use App\Models\depense;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Date;

class DepenseResource extends Resource
{
    protected static ?string $model = Depense::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Depense Page')
                ->description('Cette page pour créer un enregistrement Depense')
                ->icon('heroicon-o-banknotes')->schema([

                     Select::make('depense_item_id')->label('Depense')
                    ->relationship(name: 'depenseItem', titleAttribute: 'depense')
                    ->required()
                    ->preload()
                    ->native(false)
                    ->searchable()
                    ->optionsLimit(5)
                    ->live()
                    ->editOptionForm([
                        TextInput::make('depense')->required()->label('Nom de Depense'),

                    ])
                    ->createOptionForm([
                        TextInput::make('depense')->required()->label('Nom de Depense '),

                    ]),
                    Select::make('provider_id')->label('Provider')
                    ->relationship(name: 'provider', titleAttribute: 'name')
                    ->preload()
                    ->native(false)
                    ->searchable()
                    ->optionsLimit(5)
                    ->live()
                    ->editOptionForm([
                        TextInput::make('name')->type('text')->required()->unique(ignoreRecord:true),
                     ])
                    ->createOptionForm([
                        TextInput::make('name')->type('text')->required()->unique(ignoreRecord:true),
                    ]),

                    TextInput::make('quantity')
                    ->live(debounce: 1000)
                    ->numeric()
                    ->inputMode('decimal')
                    ->afterStateUpdated(fn (Set $set,Get $get) => $set('totalAmount', floatval($get('quantity')) * floatval($get('unitPrice')))),

                    TextInput::make('unitPrice')->numeric()
                    ->inputMode('decimal')
                    ->live(debounce: 1000)
                    ->afterStateUpdated(fn (Set $set,Get $get) => $set('totalAmount', floatval($get('quantity')) * floatval($get('unitPrice')))),

                    TextInput::make('totalAmount')->label('Montant Total')
                    ->numeric()
                    ->inputMode('decimal'),

                    DatePicker::make('datePurchase')->label('La date de Depense')->native(false)->default(now()),
                    Textarea::make('note'),
                    FileUpload::make('attachement')->enableOpen()->enableDownload()->imageEditor(),
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('depenseItem.depense')->label('Depense')->icon('heroicon-m-cog')->sortable()->toggleable()->searchable(),
                TextColumn::make('provider.name')->label('Provider')->icon('heroicon-o-building-library')->sortable()->toggleable()->searchable(),

                TextColumn::make('totalAmount')->label('Total')->sortable()->toggleable()->searchable()->icon('heroicon-o-banknotes')
                ->summarize(Sum::make()->formatStateUsing(function ($state) {
                    return number_format((float)$state, 2, '.', '') . ' DH';
                }))
                ->formatStateUsing(function ($state, Depense $depense) {
                    return number_format((float)$state, 2, '.', '') . ' DH';
                }),
                TextColumn::make('created_at')->icon('heroicon-o-calendar-days')->sortable()->toggleable()
                ->date(),

            ])->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('created_at')
                        ->form([
                DatePicker::make('created_from'),
                DatePicker::make('created_until')->default(now()),
                           ])
                    ->query(function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['created_from'],
                                     fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                                  )
                                ->when(
                                    $data['created_until'],
                                    fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                                );
                    })
                    ])
            ->actions([
                Tables\Actions\EditAction::make()->hidden(fn($record)=>$record->trashed()),
                Tables\Actions\ViewAction::make()->hidden(fn($record)=>$record->trashed()),
                Tables\Actions\DeleteAction::make()->hidden(fn($record)=>$record->trashed()),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepenses::route('/'),
            'create' => Pages\CreateDepense::route('/create'),
            'edit' => Pages\EditDepense::route('/{record}/edit'),
        ];
    }
}
