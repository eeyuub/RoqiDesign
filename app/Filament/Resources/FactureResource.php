<?php

namespace App\Filament\Resources;

use App\Enums\customerGender;
use App\Enums\isActive;
use App\Filament\Resources\FactureResource\Pages;
use App\Filament\Resources\FactureResource\RelationManagers;
use App\Models\facture;
use App\Models\factureItem;
use App\Models\Order;
use App\Models\productOption;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FactureResource extends Resource
{
    protected static ?string $model = Facture::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 4;

    public get $get;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Facture Deatils')
                ->description('Cette page pour créer un enregistrement Facture')
                ->icon('heroicon-o-clipboard-document-list')->schema([
                    TextInput::make('numeroFacture')
                    ->label('N* Facture')
                    // ->prefix('N* Facture')
                    // ->hiddenLabel()

                    ->unique(ignoreRecord:true)
                    ->default(
                        function ()  {
                            $latestOrder = facture::latest('id')->first();

                            $orderNumber = 'FACTURE';
                            $dateComponent = now()->format('Ymd');
                            $increment = 1;

                            if ($latestOrder) {
                                $latestOrderDateComponent = substr($latestOrder->numeroFacture, 7, 8);


                                if ($latestOrderDateComponent === $dateComponent) {
                                    $increment = (int)substr($latestOrder->numeroFacture, -4) + 1;
                                }
                            }

                            $orderNumber .= $dateComponent . str_pad($increment, 4, '0', STR_PAD_LEFT);

                            return $orderNumber;
                        }
                    ),
                    Select::make('customer_id')
                                // ->prefix('Client')
                                // ->hiddenLabel()
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

                    DatePicker::make('factureDate')->label('La date de Facture')->native(false)->default(now()),
                    Textarea::make('note'),

                ])->columnSpan(2),


                Section::make('Calcule de facture')
                ->description('Deatils des calcule')
                ->icon('heroicon-o-banknotes')
                ->schema([

                    TextInput::make('totalHT')->label('TOTAL HT')->inputMode('decimal')->numeric()->reactive()->prefix('MAD'),
                    TextInput::make('tva')->label('TVA')->inputMode('decimal')->numeric()->reactive()->prefix('%')->default(20)->maxValue(100),
                    // TextInput::make('remise')->label('REMISE')->inputMode('decimal')->numeric()->reactive()->prefix('%')->maxValue(100)->default(0),
                    TextInput::make('totalTTC')->label('TOTAL TTC')->inputMode('decimal')->numeric()->reactive()->prefix('MAD'),
                ])->columnSpan(1),

                Section::make('Facture Produit')
                ->description('Les produit relier a ce Facture')
                ->icon('heroicon-o-cube')->schema([

                    Repeater::make('factureItems')
                    ->relationship()

                    ->label('Selected Items')

                    ->schema([

                        Select::make('product_option_id')->required()
                        ->relationship('productOption', 'option',fn (Builder $query) => $query->where('isFactured',true)->where('qteDispo','>',0))
                        ->getOptionLabelFromRecordUsing(fn (productOption $record) => "{$record->option} ({$record->code})")
                        ->preload()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->native(false)
                        ->searchable()
                        ->optionsLimit(5)
                        ->afterStateUpdated(
                            function (Forms\Set $set, $state, ?factureItem $record,Get $get): void {
                                if ($record !== null) {
                                    return;
                                }

                                $sku = productOption::whereKey($state)->first();

                                if ($sku === null) {
                                    return;
                                }

                                $set('unitPrice', $sku->unitPrice);
                                $set('quantity', $sku->qteDispo);
                                $set('qteDisponible', $sku->qteDispo);
                                self::updateItemTotal($get, $set);
                            }
                        )
                        ->reactive(),
                        TextInput::make('unitPrice')->inputMode('decimal')->numeric()
                        ->reactive()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::updateItemTotal($get, $set);
                        }),
                        TextInput::make('designation'),

                        TextInput::make('qteDisponible')->inputMode('decimal')->numeric()->readOnly()
                        ->disabled(),
                        TextInput::make('quantity')->inputMode('decimal')->numeric()->default(1)->minValue(1)->maxValue(function(get $get){
                            return $get('qteDisponible');
                        })
                        ->reactive()
                        ->live(onBlur: true)

                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::updateItemTotal($get, $set);
                        }),

                        TextInput::make('totalAmount')->numeric()->columnSpan(1),

                    ])
                    ->deleteAction(function (Get $get, Set $set) {
                        self::updateItemTotal($get, $set);
                    })
                    ->reorderable(true)
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data,get $get): array {

                        $optionID=$data['product_option_id'];

                        $orderID = $get('id');

                        $option =  productOption::where('id',$optionID)->first();
                        $item = factureItem::where(['facture_id'=>$orderID ,'product_option_id'=>$optionID])->first();

                        if($data['quantity']==$item['quantity']){
                            return $data;
                        }

                        $quantityDifference  =  $item['quantity'] - $data['quantity'];
                        // $option->quantity += $quantityDifference;

                        if($option->isFactured) $option->qteDispo += $quantityDifference;

                        $option->save();

                        return $data;
                    })
                     ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {

                            $item =  productOption::where('id',$data['product_option_id'])->first();

                            $item->qteDispo -= $data['quantity'];

                            $item->save();
                            return $data;

                    })
                     ->columns(3)
                    ->cloneable()
                    ->defaultItems(0)
                    ->reorderableWithButtons()

                ])->columnSpan(2),





                Section::make('Étendu Produit')
                ->description('Les produit relier a ce Facture et n\'est pas enregistré en stock')
                ->icon('heroicon-o-cube')->schema([

                    Repeater::make('factureExtends')->relationship()
                    ->label('Selected Items')
                    ->schema([

                        TextInput::make('designation')->required(),

                        TextInput::make('unitPrice')->numeric()
                        ->reactive()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::updateItemTotal($get, $set);
                        }),

                        TextInput::make('quantity')->numeric()->default(1)->minValue(1)
                        ->reactive()
                        ->live(onBlur: true)

                        ->afterStateUpdated(function (Get $get, Set $set) {
                            self::updateItemTotal($get, $set);
                        }),



                        TextInput::make('productSize')->required(),
                        TextInput::make('totalAmount')->numeric()->columnSpan(2),

                    ])->reorderable(true)
                     ->columns(3)
                    ->cloneable()
                    ->deleteAction(function (Forms\Get $get, Forms\Set $set) {
                        Notification::make()
                        ->success()
                        ->title('User restored')
                        ->body('The user has been restored successfully.');
                    })
                    ->defaultItems(0)
                    ->reorderableWithButtons()

                ])->columnSpan(2),


            ])->columns(3);
    }

    public static function updateItemTotal(Get $get, Set $set):void{
        $total = floatval($get('quantity')) * floatval($get('unitPrice'));
        $set('totalAmount',  number_format($total, 2, '.', ''));

        $TotalItems = floatval(collect($get('../../factureItems'))->pluck('totalAmount')->sum());
        $TotalItemsExtends = floatval(collect($get('../../factureExtends'))->pluck('totalAmount')->sum());


       $totalFacture=  $set('../../totalHT', number_format(($TotalItems + $TotalItemsExtends), 2, '.', ''));

        $tva = $get('../../tva');

        $totalTTC = $totalFacture + ($totalFacture*$tva)/100;

        $set('../../totalTTC', number_format($totalTTC, 2, '.', ''));
    }



    public static function updateTotals(Get $get, Set $set): void
    {
    // Retrieve all selected products and remove empty rows
    $selectedProducts = collect($get('factureItems'));
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
                TextColumn::make('numeroFacture'),
                TextColumn::make('customer.name')->icon('heroicon-o-user'),
                TextColumn::make('totalTTC')->label('Total')->summarize(Sum::make()->formatStateUsing(function ($state) {
                    return number_format((float)$state, 2, '.', '') . ' DH';
                }))
                ->formatStateUsing(function ($state, facture $facture) {
                    return number_format((float)$facture->totalTTC, 2, '.', '') . ' DH';
                }),
                // Tables\Columns\TextColumn::make('factureStatus')
                // ->badge(),

                TextColumn::make('factureDate')->icon('heroicon-o-calendar-days'),

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
                Tables\Actions\Action::make('view')
                ->icon('heroicon-m-printer')
                ->label('Facture')->hidden(fn($record)=>$record->trashed())
                ->url(fn (facture $record): string => route('facturePDF',['id'=> $record->numeroFacture]), shouldOpenInNewTab: true),
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
            'index' => Pages\ListFactures::route('/'),
            'create' => Pages\CreateFacture::route('/create'),
            'edit' => Pages\EditFacture::route('/{record}/edit'),
        ];
    }
}
