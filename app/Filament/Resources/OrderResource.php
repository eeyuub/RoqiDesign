<?php

namespace App\Filament\Resources;

use App\Enums\customerGender;
use App\Enums\isActive;
use App\Enums\payment;
use App\Enums\Status;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\SupplierResource\Schema\supplierSchema;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\orderProduct;
use App\Models\productOption;
use Closure;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Actions\Modal\Actions\ButtonAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;
use function Livewire\after;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    // protected static ?string $navigationIcon = 'heroicon-s-cube';

    protected static ?string $navigationGroup = 'Ventes';

    public static function getModelLabel(): string
    {
        return __("Vente de Produit");
    }


    public static function form(Form $form): Form
    {

        $products = productOption::get();


        return $form
            ->schema([

                Section::make('Créer une commande')
                ->description('Cette page pour créer un  commande')
                ->icon('heroicon-o-shopping-bag')->schema([
                    Wizard::make([
                        Step::make('Customer')
                        ->label('Client details')
                        ->icon('heroicon-s-user')
                            ->schema([

                                TextInput::make('orderNumber')
                                ->prefix('Numero de Commande')
                                ->hiddenLabel()

                                ->unique(ignoreRecord:true)
                                ->default(
                                    function ()  {
                                        $latestOrder = Order::latest('id')->first();

                                        $orderNumber = 'ORDER';
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
                                Select::make('customer_id')
                                ->prefix('Client')
                                ->hiddenLabel()
                                ->required()
                                ->relationship(name: 'Customer', titleAttribute: 'name')
                                ->preload()
                                ->native(false)
                                ->searchable()
                                ->optionsLimit(5)
                                ->live()

                                ->editOptionForm([
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

                                ])
                                ->createOptionForm([
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

                                ])
                                ,
                                select::make('orderPayment')
                                ->options(payment::class)
                                ->prefix('Type de Paiement')
                                ->hiddenLabel()
                                ->native(false),
                                Select::make('orderStatus')
                                ->prefix('Status de Commande')
                                ->hiddenLabel()
                                ->options(Status::class)
                                ->native(false),
                                DatePicker::make('orderDate')
                                ->prefix('Date de Commande')
                                ->hiddenLabel()->native(false)->default(now()),
                                DatePicker::make('shippedDate')->prefix('Date denvoi')
                                ->hiddenLabel()->native(false)->default(now()),
                                DatePicker::make('deliveredDate')->prefix('Date de Livraison')
                                ->hiddenLabel()->native(false)->default(now()),


                            ])
                            ,
                        Step::make('Items')
                            ->icon('heroicon-m-shopping-bag')
                            ->label('Articles')
                            ->schema([
                                Repeater::make('orderProducts')->relationship()
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
                                        function (Forms\Set $set, $state, ?orderProduct $record,Get $get): void {
                                            if ($record !== null) {
                                                return;
                                            }

                                            $sku = productOption::whereKey($state)->first();

                                            if ($sku === null) {
                                                return;
                                            }

                                            $set('unitPrice', $sku->unitPrice);
                                            $set('quantity', $sku->quantity);
                                            self::updateItemTotal($get, $set);
                                        }
                                    )
                                    ->reactive(),
                                    TextInput::make('unitPrice')->numeric()
                                    ->reactive()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateItemTotal($get, $set);
                                    }),
                                    TextInput::make('quantity')->numeric()
                                    ->reactive()
                                    ->live(true)

                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::updateItemTotal($get, $set);
                                    })
                                    ,

                                    TextInput::make('totalAmount')->numeric()->columnSpan(3),

                                ])->reorderable(true)
                                ->mutateRelationshipDataBeforeSaveUsing(function (array $data,get $get): array {
                                    $optionID=$data['product_option_id'];
                                    $orderID = $get('id');

                                    $option =  productOption::where('id',$optionID)->first();
                                    $item = orderProduct::where(['order_id'=>$orderID ,'product_option_id'=>$optionID])->first();

                                    if($data['quantity']==$item['quantity']){
                                        return $data;
                                    }

                                    $quantityDifference  =  $item->quantity - $data['quantity'];
                                    $option->quantity += $quantityDifference;

                                    if($option->isFactured) $option->qteDispo -= $quantityDifference;

                                    $option->save();

                                    return $data;
                                })
                                 ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {

/*   dd($data['product_option_id']);
                                        return null; */
                                        $item =  productOption::where('id',$data['product_option_id'])->first();
                                        // dd($item->isFactured);
                                        // dd($get('orderProducts'));
                                        $item->quantity -= $data['quantity'];

                                        // $item->isFactured ? $item->isFactured->quantityToFacture += $data['quantity'] : null;
                                        if($item->isFactured) $item->qteDispo += $data['quantity'];
                                        $item->save();
                                        return $data;


                                })
                                ->deleteAction(
                                    fn (Action $action) => $action->requiresConfirmation(),

                                 )->columns(3)
                                ->cloneable()

                                ->reorderableWithButtons()



                            ])

                    ])
                                ])->columnSpan(2),

                Section::make('Details')
                ->description('Prevent abuse by limiting the number of requests per period')

                ->schema([
                    Textarea::make('note'),
                    TextInput::make('totalAmount')->numeric()->reactive()->prefix('MAD'),
                ])->columnSpan(1)


           ])->columns(3);
    }

    public static function updateItemTotal(Get $get, Set $set):void{
        $total = floatval($get('quantity')) * floatval($get('unitPrice'));
        $set('totalAmount',  number_format($total, 2, '.', ''));

        $TotalItems = floatval(collect($get('../../orderProducts'))->pluck('totalAmount')->sum());

        $set('../../totalAmount', number_format($TotalItems, 2, '.', ''));
    }

    public static function updateTotals(Get $get, Set $set): void
{
    // Retrieve all selected products and remove empty rows
    $selectedProducts = collect($get('orderProducts'));
    dd($selectedProducts);
    // Retrieve prices for all selected products
     $prices = productOption::find($selectedProducts->pluck('product_id'))->pluck('price', 'id');
/*
    // Calculate subtotal based on the selected products and quantities
    $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
        return $subtotal + ($prices[$product['product_id']] * $product['quantity']);
    }, 0);

    // Update the state with the new values
    $set('subtotal', number_format($subtotal, 2, '.', ''));
    $set('total', number_format($subtotal + ($subtotal * ($get('taxes') / 100)), 2, '.', '')); */
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('orderNumber'),
                TextColumn::make('customer.name')->icon('heroicon-o-user'),
                TextColumn::make('totalAmount')->summarize(Sum::make()->formatStateUsing(function ($state) {
                    return number_format((float)$state, 2, '.', '') . ' DH';
                }))
                ->formatStateUsing(function ($state, Order $order) {
                    return number_format((float)$order->totalAmount, 2, '.', '') . ' DH';
                }),
                Tables\Columns\TextColumn::make('orderStatus')
                ->badge(),

                TextColumn::make('orderDate')->icon('heroicon-o-calendar-days'),

            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ButtonAction::make('asdsa')->url(route('downPDF')),
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-printer')
                    ->label('Facture')
                    ->url(fn (Order $record): string => route('downPDF',['id'=> $record->id]), shouldOpenInNewTab: true),
                Tables\Actions\EditAction::make()->hidden(fn($record)=>$record->trashed()),
                Tables\Actions\viewAction::make()->hidden(fn($record)=>$record->trashed()),
                Tables\Actions\deleteAction::make()->hidden(fn($record)=>$record->trashed()),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
