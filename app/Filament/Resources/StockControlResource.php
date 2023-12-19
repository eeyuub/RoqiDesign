<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockControlResource\Pages;
use App\Filament\Resources\StockControlResource\RelationManagers;
use App\Models\productOption;
use App\Models\stockControl;
use App\Models\stockControleproduct;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockControlResource extends Resource
{
    protected static ?string $model = StockControl::class;

    protected static ?string $navigationIcon = 'heroicon-m-adjustments-horizontal';
    protected static ?int $navigationSort = 5;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('code')->label('')
                ->default(
                    function ()  {
                        $latestOrder = StockControl::latest('id')->first();

                        $orderNumber = 'STOCK';
                        $dateComponent = now()->format('Ymd');
                        $increment = 1;

                        if ($latestOrder) {
                            $latestOrderDateComponent = substr($latestOrder->orderNumber, 5, 8);


                            if ($latestOrderDateComponent === $dateComponent) {
                                $increment = (int)substr($latestOrder->orderNumber, -4) + 1;
                            }
                        }

                        $orderNumber .= $dateComponent . str_pad($increment, 4, '0', STR_PAD_LEFT);

                        return $orderNumber;
                    }
                )->readOnly(),
                Section::make('Stock Controle Items')
                ->description('add any item has been exported')
                ->icon('heroicon-m-adjustments-horizontal')
                ->schema([


                    Repeater::make('stockControleproducts')->relationship()
                    ->label('Selected Items')
                    ->schema([

                        Select::make('product_option_id')
                        ->relationship(name: 'productOption', titleAttribute: 'option')
                        ->getOptionLabelFromRecordUsing(fn (productOption $record) => "{$record->option} ({$record->code})")
                        ->preload()
                        ->native(false)
                        ->searchable()
                        ->optionsLimit(5)
                        ->afterStateUpdated(
                            function (Forms\Set $set, $state, ?stockControleproduct $record): void {
                                if ($record !== null) {
                                    return;
                                }

                                $sku = productOption::whereKey($state)->first();

                                if ($sku === null) {
                                    return;
                                }

                                $set('unitPrice', $sku->unitPrice);
                            }
                        )
                        ->reactive(),

                        TextInput::make('unitPrice')->numeric()
                        ->reactive()
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            $set('totalAmount', floatval($get('quantity')) * floatval($get('unitPrice')));
                        }),
                        TextInput::make('quantity')->numeric()
                        ->reactive()
                        ->afterStateUpdated(function (Set $set, Get $get) {
                            $set('totalAmount', floatval($get('quantity')) * floatval($get('unitPrice')));
                        }),

                        TextInput::make('totalAmount')->numeric(),

                    ])->reorderable(true)
                    /*  ->mutateRelationshipDataBeforeSaveUsing(function (array $data,get $get): array {
                        $optionID=$data['product_option_id'];
                        $orderID = $get('id');

                        $option =  productOption::where('id',$optionID)->first();
                        $item = stockControleproduct::where(['stock_control_id'=>$orderID ,'product_option_id'=>$optionID])->first();

                        if($data['quantity']==$item['quantity']){
                            return $data;
                        }

                        $quantityDifference  =  $item->quantity - $data['quantity'];
                        $option->quantity += $quantityDifference;

                        $option->save();

                        return $data;
                    })
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            $item =  productOption::where('id',$data['product_option_id'])->first();
                            $item->quantity -= $data['quantity'];
                            $item->save();
                            return $data;
                    }) */
                    ->deleteAction(
                        fn (Action $action) => $action->requiresConfirmation(),

                     )->columns(4)
                    ->cloneable()

                    ->reorderableWithButtons()

                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Stock Number')->icon('heroicon-m-adjustments-horizontal'),
                TextColumn::make('created_at')->label('Date Export')->date()->icon('heroicon-o-calendar-days'),
                TextColumn::make('stockControleproducts_exists')->label('Total Articles')->default(function (StockControl $record) {
                    $count = StockControleproduct::where('stock_control_id', $record->id)->count();
                    return strval($count) . ' Item';
                })->icon('heroicon-o-puzzle-piece'),

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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListStockControls::route('/'),
            'create' => Pages\CreateStockControl::route('/create'),
            'edit' => Pages\EditStockControl::route('/{record}/edit'),
        ];
    }
}
