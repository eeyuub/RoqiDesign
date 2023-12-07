<?php

namespace App\Filament\Resources;

use App\Enums\isActive;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\SupplierResource\Schema\supplierSchema;
use App\Models\Product;
use App\Models\productOption;
use Filament\Tables\Actions\ActionGroup ;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as infoSection;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\ToggleColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getModelLabel(): string
    {
        return __('Produit');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product')
                ->description('This page for creating a Product record')
                ->icon('heroicon-o-cube')->schema([
                    TextInput::make('name')->type('text'),
                    TextInput::make('note')->type('text'),
                    Select::make('category_id')->label('Category')
                    ->relationship(name: 'Category', titleAttribute: 'name')
                    ->preload()
                    ->native(false)
                    ->searchable()
                    ->optionsLimit(5)
                    ->live()
                    ->editOptionForm([
                        TextInput::make('name')->required()->label('Category'),
                        TextInput::make('note'),
                    ])
                    ->createOptionForm([
                        TextInput::make('name')->required()->label('Category'),
                        TextInput::make('note'),
                    ]),
                    Select::make('supplier_id')->label('Supplier')
                    ->relationship(name: 'Supplier', titleAttribute: 'name')
                    ->preload()
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
                        TextInput::make('name')->type('text'),
                        TextInput::make('address')->type('text'),
                        TextInput::make('phone')->type('tel'),
                        TextInput::make('contactPerson')->type('text'),
                        TextInput::make('city')->type('text'),
                        TextInput::make('note')->type('text'),
                        Select::make('isActive')
                        ->options(isActive::class)
                        ->native(false)
                    ]),

                    RichEditor::make('description')
                    ->columnSpan(['md' => 1, 'xl' => 2]),
                    FileUpload::make('attachement')
                   /*  ->previewable()->enableDownload()
                    ->image()->enableOpen()->responsiveImages() */
                    ->imageEditor()
                    ->columnSpan(['md' => 1, 'xl' => 2]),





                ])->columns(['md' => 1, 'xl' => 2]),
                Section::make('Options')
                ->description('Settings Options for this Product.')
                ->icon('heroicon-o-view-columns')
                ->schema([
                    Repeater::make('productOptions')->relationship()

                    ->schema([
                         Select::make('warehouse_id')
                        ->relationship(name: 'Warehouse', titleAttribute: 'name')
                        ->native(false)
                        ->preload()
                        ->searchable()
                        ->optionsLimit(5)
                        ->live()
                        ->editOptionForm([
                            TextInput::make('name')->type('text'),
                    TextInput::make('location')->type('text'),
                    TextInput::make('contact')->type('tel'),
                    TextInput::make('note')->type('text'),
                        ])
                        ->createOptionForm([
                            TextInput::make('name')->type('text'),
                    TextInput::make('location')->type('text'),
                    TextInput::make('contact')->type('tel'),
                    TextInput::make('note')->type('text'),
                        ]),

                        Select::make('product_size_id')
                        ->relationship(name: 'productSize', titleAttribute: 'size')
                        ->preload()
                        ->native(false)
                        ->searchable()
                        ->optionsLimit(5)
                        ->live()
                        ->editOptionForm([
                        TextInput::make('size')->required()->label('Size'),
                        ])
                        ->createOptionForm([
                        TextInput::make('size')->required()->label('Size'),
                        ]),

                        Select::make('motif_id')
                        ->relationship(name: 'Motif', titleAttribute: 'motif')
                        ->preload()
                        ->native(false)
                        ->searchable()
                        ->optionsLimit(5)
                        ->live()
                        ->editOptionForm([
                            TextInput::make('motif')->required()->label('Motif'),
                        ])
                        ->createOptionForm([
                        TextInput::make('motif')->required()->label('Motif'),
                        ]),

                        TextInput::make('code'),
                        TextInput::make('option')->label('Option Name')->live(onBlur: true),
                        TextInput::make('note'),
                        TextInput::make('quantity')->type('number'),
                        TextInput::make('unitPrice')->type('number'),
                        Toggle::make('isFactured')->columnSpan(3)->reactive()->requiredWith('qteDispo'),
                        TextInput::make('qteDispo')->requiredWith('isFactured')->type('number')->label('quantité Disponible pour facturée')
                        ->hidden(
                            fn (Get $get): bool => $get('isFactured') == false
                        ),
                        FileUpload::make('attachement')
                        // ->previewable()->enableDownload()->enableOpen()->responsiveImages()
                        ->imageEditor()
                        ->columnSpan(['md' => 1, 'xl' => 3]),
                    ])->reorderable(true)
                    ->deleteAction(
                        fn (Action $action) => $action->requiresConfirmation(),
                    )
                    ->columns(['md' => 1, 'xl' => 3])
                    ->cloneable()
                    ->reorderableWithButtons()
                    // ->grid(1)
                    ->itemLabel(fn (array $state): ?string => $state['option']  ?? null),

                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('attachement')->circular()->toggleable()->default('none-1.png'),
                TextColumn::make('name')->label('Gamme')  ->toggleable()->default('No Content.')->icon('heroicon-o-cube')->searchable(),
                // TextColumn::make('description')->default('No Content.'),
                // TextColumn::make('contact')->default('No Content.'),
                TextColumn::make('productOptions')
                    ->label('Options')
                    ->badge()
                    ->translateLabel()
                    ->bulleted()
                    ->toggleable()
                    ->color('success')
                    ->copyable()
                    ->formatStateUsing(function ($state, productOption $option) {
                        return $state->option . ' - ' . $state->code;
                    }),
                TextColumn::make('Supplier.name')->toggleable()->label('Fournisseur')->default('No Content.')->icon('heroicon-o-building-storefront')->searchable(),
                TextColumn::make('created_at')  ->toggleable()->date()->icon('heroicon-o-calendar-days')->searchable()

            ])
            ->filters([
                //
            ])
            ->actions([

                ActionGroup::make([
                 Tables\Actions\ViewAction::make()->hidden(fn($record)=>$record->trashed()),
                 Tables\Actions\EditAction::make()->hidden(fn($record)=>$record->trashed()),
                 Tables\Actions\DeleteAction::make()->hidden(fn($record)=>$record->trashed()),
                 Tables\Actions\RestoreAction::make(),
                ])->tooltip('Actions'),
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


    public static function infolist(Infolist $infolist): Infolist
        {
            return $infolist
                ->schema([


                    infoSection::make('Produit')
                    // ->description('Prevent abuse by limiting the number of requests per period')
                    ->columns(5)
                    ->schema([
                        ImageEntry::make('attachement')->hiddenLabel()->label('Image')->default('none-1.png')->height(100),

                        TextEntry::make('name')->label('La Gamme')->size(TextEntrySize::Large)->icon('heroicon-o-shopping-cart'),
                        TextEntry::make('Category.name')->label('Catégorie')->size(TextEntrySize::Large)->icon('heroicon-o-tag'),
                        TextEntry::make('Supplier.name')->label('Fournisseur')->size(TextEntrySize::Large)->icon('heroicon-o-building-storefront'),
                        TextEntry::make('note')->size(TextEntrySize::Large)->icon('heroicon-o-document-text'),
                    ]),

                    RepeatableEntry::make('productOptions')
                    ->columnSpan(3)
                    ->columns(6)
                    ->schema([
                        ImageEntry::make('attachement')->hiddenLabel()->circular()->label('Image')->default('none-1.png')->height(60),
                         TextEntry::make('option')->label('Option Gamme')->formatStateUsing(function ($state, productOption $option) {
                            return $option->code . ' - ' . $option->option;
                        }),
                        TextEntry::make('quantity')->formatStateUsing(function ($state, productOption $option) {
                            return $option->quantity . ' ' . $option->productSize->size;
                        }),
                        TextEntry::make('unitPrice')->label('Prix Unitaire')
                            ->formatStateUsing(function ($state, productOption $order) {
                                return number_format((float)$state, 2, '.', '') . ' DH';
                            }),


                            TextEntry::make('Warehouse.name'),
                            TextEntry::make('Motif.motif'),

                            // TextEntry::make('unitPrice')
                            //     ->formatStateUsing(function ($state, productOption $order) {
                            //         return number_format((float)$state, 2, '.', '') . ' DH';
                            //     }),
                    ])


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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
