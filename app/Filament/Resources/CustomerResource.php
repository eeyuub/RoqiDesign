<?php

namespace App\Filament\Resources;

use App\Enums\customerGender;
use App\Enums\isActive;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    // protected static ?string $navigationIcon = 'heroicon-s-users';

    protected static ?string $navigationGroup = 'Ventes';

    public static function getModelLabel(): string
    {
        return __('Clients');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Client')
                ->description('Cette page pour créer un dossier client')
                ->icon('heroicon-s-users')->schema([
                    TextInput::make('name')->type('text')->unique(ignoreRecord:true)->required(),
                    TextInput::make('address')->type('text'),
                    TextInput::make('phone')->type('tel')->unique(ignoreRecord:true),
                    TextInput::make('note')->type('text'),
                    Select::make('gender')
                        ->options(customerGender::class)
                        ->native(false),
                    Select::make('isActive')
                    ->options(isActive::class)
                    ->native(false)
                ])->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->icon('heroicon-o-user')->sortable()->toggleable()->searchable()->copyable(),
                TextColumn::make('address')->icon('heroicon-o-map-pin')->sortable()->toggleable()->searchable()->copyable(),
                TextColumn::make('phone')->icon('heroicon-o-phone')->sortable()->toggleable()->searchable()->copyable(),
                TextColumn::make('note')->icon('heroicon-s-paper-clip')->sortable()->toggleable()->searchable()->copyable(),
                IconColumn::make('isActive')->boolean()->sortable()->toggleable()->searchable(),
                TextColumn::make('created_at')->icon('heroicon-o-calendar-days')->date()->sortable()->toggleable()->searchable()->copyable(),
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
                Tables\Actions\DeleteAction::make()->hidden(fn($record)=>$record->trashed())
                ->action(function($record){

                    if($record->Orders()->count() > 0){
                        Notification::make()
                        ->danger()
                        ->title('Suppression n’est pas possible')
                        ->body('Le client est associé à une commande')
                        ->send();
                        return ;
                    }

                    return $record->delete();


             }),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
