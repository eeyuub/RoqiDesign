<?php

namespace App\Filament\Resources\WarehouseResource\RelationManagers;

use App\Models\productOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Grouping\Group;

class ProductOptionRelationManager extends RelationManager
{
    protected static string $relationship = 'productOptions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->groups([
                Group::make('Product.name')->collapsible()
                    ->getDescriptionFromRecordUsing(fn (productOption $record): string => $record->Product->name),
            ])
            ->columns([
                // Tables\Columns\TextColumn::make('Product.name'),
                Tables\Columns\TextColumn::make('code')
                ->formatStateUsing(function ($state, productOption $productOption) {
                    return $productOption->option . ' (' . $productOption->code.')';
                }),
                Tables\Columns\TextColumn::make('quantity')
                ->formatStateUsing(function ($state, productOption $productOption) {
                    return $productOption->quantity . ' ' . $productOption->productSize->size;
                }),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DetachAction::make(),
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ]);
    }
}
