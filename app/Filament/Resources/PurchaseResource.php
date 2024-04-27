<?php

namespace App\Filament\Resources;

use App\Enums\isActive;
use App\Enums\payment;
use App\Enums\status;
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
use Filament\Tables\Filters\Filter;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    // protected static ?string $navigationIcon = 'heroicon-s-shopping-cart';

    protected static ?string $navigationGroup = 'Achats';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('Achat de Produit');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Créer une Achat')
                ->description("Cette page pour créer un  commande d'Achat")
                ->icon('heroicon-s-cube')->schema([
                    Wizard::make([
                        Step::make('Customer')
                        ->icon('heroicon-o-building-storefront')
                            ->schema([

                                TextInput::make('purchaseNumber')
                                ->unique(ignoreRecord:true)
                                ->default(
                                    function ()  {
                                        $latestOrder = Purchase::latest('id')->first();

                                        $purchaseNumber = 'PURCHASE';
                                        $dateComponent = now()->format('Ymd');
                                        $increment = 1;

                                        if ($latestOrder) {
                                            $latestOrderDateComponent = substr($latestOrder->purchaseNumber, 8, 8);



                                            if ($latestOrderDateComponent === $dateComponent) {
                                                $increment = (int)substr($latestOrder->purchaseNumber, -4) + 1;
                                            }
                                        }

                                        $purchaseNumber .= $dateComponent . str_pad($increment, 4, '0', STR_PAD_LEFT);

                                        return $purchaseNumber;
                                    }
                                ),
                                Select::make('supplier_id')
                                ->relationship(name: 'Supplier', titleAttribute: 'name')
                                ->preload()
                                ->required()
                                ->native(false)
                                ->searchable()
                                ->optionsLimit(5)
                                ->live()
                                ->editOptionForm([
                                    TextInput::make('name')->type('text')->required(),
                                    TextInput::make('address')->type('text'),
                                    TextInput::make('phone')->type('tel'),
                                    TextInput::make('contactPerson')->type('text'),
                                    TextInput::make('city')->type('text'),
                                    TextInput::make('note')->type('text'),
                                    Select::make('isActive')
                                    ->options(isActive::class)
                                    ->native(false)
                                 ])
                                ->createOptionForm([
                                    TextInput::make('name')->type('text')->required(),
                                    TextInput::make('address')->type('text'),
                                    TextInput::make('phone')->type('tel'),
                                    TextInput::make('contactPerson')->type('text'),
                                    TextInput::make('city')->type('text'),
                                    TextInput::make('note')->type('text'),
                                    Select::make('isActive')
                                    ->options(isActive::class)
                                    ->native(false)
                                ]),
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
                                    ->getOptionLabelFromRecordUsing(fn (productOption $record) => "{$record->option} ({$record->code})")
                                    ->columnSpan(2)
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

                                    TextInput::make('unitPrice')->inputMode('decimal')->numeric()->columnSpan(2)
                                    ->reactive()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateItemTotal($get, $set);
                                    }),
                                    /* ->afterStateUpdated(function (Set $set, Get $get) {
                                        $set('totalAmount', floatval($get('quantity')) * floatval($get('unitPrice')));
                                    }) */
                                    TextInput::make('quantity')->inputMode('decimal')->numeric()->columnSpan(2)
                                    ->reactive()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateItemTotal($get, $set);
                                    })
                                    /* ->afterStateUpdated(function (Set $set, Get $get) {
                                        $set('totalAmount', floatval($get('quantity')) * floatval($get('unitPrice')));
                                    }) */
                                    ,

                                    TextInput::make('totalAmount')->inputMode('decimal')->numeric()->columnSpan(2)->prefix('MAD'),

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

                                TextInput::make('totalAmount')->inputMode('decimal')->numeric()->reactive(),

                            ])

                    ])
                                ])->columnSpan(2),

                Section::make('Calcule de Commande dAchat')
                ->description('Deatils des calcule')
                ->icon('heroicon-o-banknotes')

                ->schema([
                    Textarea::make('note'),
                    TextInput::make('totalAmount')->inputMode('decimal')->numeric()->reactive()->prefix('MAD'),
                   /*  Placeholder::make('Total Amount')->reactive()
                    ->content(
                        function (Get $get,Set $set)  {

                            $TotalAmount =  number_format(collect($get('purchaseProducts'))
                            ->pluck('totalAmount')
                            ->sum(), 2, '.', '');

                            $set('totalAmount', floatval($TotalAmount));
                            return 'DH '. $TotalAmount ;

                        }
                    ), */

                ])->columnSpan(1)


            ])->columns(3);
    }

    public static function updateItemTotal(Get $get, Set $set):void{
        $total = floatval($get('quantity')) * floatval($get('unitPrice'));
        $set('totalAmount',  number_format($total, 2, '.', ''));

        $TotalItems = floatval(collect($get('../../purchaseProducts'))->pluck('totalAmount')->sum());

        $set('../../totalAmount', number_format($TotalItems, 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('purchaseNumber')->label("Numero d'Achat"),
                TextColumn::make('Supplier.name')->label('Fournisseur')->icon('heroicon-o-building-storefront'),
                TextColumn::make('totalAmount')->icon('heroicon-o-banknotes')->summarize(Sum::make()->formatStateUsing(function ($state) {
                    return number_format((float)$state, 2, '.', '') . ' DH';
                }))
                ->formatStateUsing(function ($state, Purchase $order) {
                    return number_format((float)$order->totalAmount, 2, '.', '') . ' DH';
                }),
                TextColumn::make('purchaseStatus')->badge()->label('Achat Status'),
                TextColumn::make('purchaseDate')->label("Date d'Achat")->date()->icon('heroicon-o-calendar-days'),
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
