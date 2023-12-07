<?php

namespace App\Filament\Resources;

use App\Enums\payment;
use App\Enums\Status;
use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers;
use App\Models\productOption;
use App\Models\Purchase;
use App\Models\purchaseProduct;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    // protected static ?string $navigationIcon = 'heroicon-s-shopping-cart';

    protected static ?string $navigationGroup = 'Achats';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('Commande de Produit');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Create an Order')
                ->description('This page for creating an Order record')
                ->icon('heroicon-s-cube')->schema([
                    Wizard::make([
                        Step::make('Customer')
                        ->icon('heroicon-s-user')
                            ->schema([

                                TextInput::make('purchaseNumber')->default(
                                    function ()  {
                                        $latestOrder = Purchase::latest('id')->first();

                                        $orderNumber = 'PURCHASE';
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
                                ),
                                Select::make('supplier_id')
                                ->relationship(name: 'Supplier', titleAttribute: 'name')
                                ->preload()
                                ->native(false)
                                ->searchable()
                                ->optionsLimit(5)
                                ->live(),
                                select::make('purchasePayment')
                                ->options(payment::class)
                                ->native(false),
                                Select::make('purchaseStatus')
                                ->options(Status::class)
                                ->native(false),
                                DatePicker::make('purchaseDate')->native(false)->default(now()),
                                DatePicker::make('shippedDate')->native(false)->default(now()),
                                DatePicker::make('deliveredDate')->native(false),


                            ])->columns(2)
                            ,
                        Step::make('Items')
                            ->icon('heroicon-m-shopping-bag')
                            ->schema([
                                Repeater::make('purchaseProducts')->relationship()
                                ->label('Selected Items')
                                ->schema([

                                    Select::make('product_option_id')
                                    ->relationship(name: 'productOption', titleAttribute: 'option')
                                    ->preload()
                                    ->native(false)
                                    ->searchable()
                                    ->optionsLimit(5)
                                    ->afterStateUpdated(
                                        function (Forms\Set $set, $state, ?purchaseProduct $record): void {
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
                                ->mutateRelationshipDataBeforeSaveUsing(function (array $data,get $get): array {
                                    $optionID=$data['product_option_id'];
                                    $orderID = $get('id');

                                    $option =  productOption::where('id',$optionID)->first();
                                    $item = purchaseProduct::where(['purchase_id'=>$orderID ,'product_option_id'=>$optionID])->first();

                                    if($data['quantity']==$item['quantity']){
                                        return $data;
                                    }

                                    $quantityDifference  =  $item->quantity - $data['quantity'];
                                    $option->quantity -= $quantityDifference;

                                    $option->save();

                                    return $data;
                                })
                                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                        $item =  productOption::where('id',$data['product_option_id'])->first();
                                        $item->quantity += $data['quantity'];
                                        $item->save();
                                        return $data;
                                })
                                ->deleteAction(
                                    fn (Action $action) => $action->requiresConfirmation(),

                                 )->columns(4)
                                ->cloneable()

                                ->reorderableWithButtons(),

                                TextInput::make('totalAmount')->numeric()->reactive(),

                            ])

                    ])
                                ]),

                Section::make('Details')
                ->description('Prevent abuse by limiting the number of requests per period')

                ->schema([
                    Textarea::make('note'),
                    TextInput::make('totalAmount')->numeric()->reactive(),
                    Placeholder::make('Total Amount')->reactive()
                    ->content(
                        function (Get $get,Set $set)  {

                            $TotalAmount =  number_format(collect($get('purchaseProducts'))
                            ->pluck('totalAmount')
                            ->sum(), 2, '.', '');

                            $set('totalAmount', floatval($TotalAmount));
                            return 'DH '. $TotalAmount ;

                        }
                    ),

                ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('purchaseNumber'),
                TextColumn::make('Supplier.name'),
                TextColumn::make('totalAmount')->summarize(Sum::make())->money(),
                TextColumn::make('purchaseStatus')->badge(),
                TextColumn::make('purchaseDate')->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->hidden(fn($record)=>$record->trashed()),
                Tables\Actions\ViewAction::make()->hidden(fn($record)=>$record->trashed()),
                Tables\Actions\DeleteAction::make()->hidden(fn($record)=>$record->trashed()),
                Tables\Actions\RestoreAction::make(),
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
