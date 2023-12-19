<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Filament\Resources\WarehouseResource\RelationManagers\ProductOptionRelationManager;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function getModelLabel(): string
    {
        return __('Entrepôts');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Warehouse')
                ->description('This page for creating a Warehouse record')
                ->icon('heroicon-o-circle-stack')->schema([
                    TextInput::make('name')->type('text'),
                    TextInput::make('location')->type('text'),
                    TextInput::make('contact')->type('tel'),
                    TextInput::make('note')->type('text'),


                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    TextColumn::make('name')->default('No Content.')->icon('heroicon-o-archive-box')->sortable()->toggleable()->searchable(),
                    TextColumn::make('location')->default('No Content.')->icon('heroicon-o-map-pin')->sortable()->toggleable()->searchable(),
                    TextColumn::make('contact')->default('No Content.')->icon('heroicon-o-phone')->sortable()->toggleable()->searchable(),
                    TextColumn::make('note')->default('No Content.')->icon('heroicon-s-paper-clip')->sortable()->toggleable()->searchable(),
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

                    if($record->productOptions()->count() > 0){
                        Notification::make()
                        ->danger()
                        ->title('Suppression n’est pas possible')
                        ->body('LEntrepôt est associé à une ou plusieur Produit')
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
            ProductOptionRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}
