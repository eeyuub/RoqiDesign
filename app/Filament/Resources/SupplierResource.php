<?php

namespace App\Filament\Resources;

use App\Enums\customerGender;
use App\Enums\isActive;
use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Filament\Resources\SupplierResource\RelationManagers\ProductsRelationManager;
use App\Models\Supplier;
use App\Observers\SupplierObserver;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup ;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    // protected static ?string $navigationIcon = 'heroicon-s-building-storefront';

    protected static ?string $navigationGroup = 'Achats';

    public static function getModelLabel(): string
    {
        return __('Fournisseur');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Suppplier')
                ->description('This page for creating a Suppplier record')
                ->icon('heroicon-s-building-storefront')->schema([
                    TextInput::make('name')->type('text'),
                    TextInput::make('address')->type('text'),
                    TextInput::make('phone')->type('tel'),
                    TextInput::make('contactPerson')->type('text'),
                    TextInput::make('city')->type('text'),
                    TextInput::make('note')->type('text'),
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

                TextColumn::make('name'),
                TextColumn::make('address') ->visibleFrom('md'),
                TextColumn::make('phone') ->visibleFrom('md'),
                TextColumn::make('contactPerson') ->visibleFrom('md'),
                TextColumn::make('city') ->visibleFrom('md'),
                TextColumn::make('note') ->visibleFrom('md'),
                IconColumn::make('isActive')
                ->boolean(),

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
                Tables\Actions\RestoreAction::make()->hidden(fn($record)=>!$record->trashed()),
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()->hidden(fn($record)=>$record->trashed()),
                     Tables\Actions\EditAction::make()->hidden(fn($record)=>$record->trashed()),

                     Tables\Actions\DeleteAction::make()->hidden(fn($record)=>$record->trashed())->action(function($record){

                        if($record->Products()->count() > 0){
                            Notification::make()
                            ->danger()
                            ->title('Suppression n’est pas possible')
                            ->body('Fournissuer est associé à une ou plusieur Achats')
                            ->send();
                            return ;
                        }
                        return $record->delete();

                 })
                 /* Tables\Actions\DeleteAction::make()->disabled(function(Supplier $supplier){
                  return  $supplier->deleteObserver();
                 }) */

                ])
                ->tooltip('Actions'),
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
