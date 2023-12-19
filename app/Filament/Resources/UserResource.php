<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;


    protected static ?int $navigationSort = 10;



    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function getModelLabel(): string
    {
        return __('Comptes');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Compte Details')
                ->icon('heroicon-o-user-circle')
                ->description('Cette Pour Cree Un nouveau Compte')
                ->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('email')->required()->unique(ignoreRecord:true),
                    TextInput::make('password')
                    ->required()
                    ->password()
                    ->dehydrateStateUsing(fn ($state) =>hash::make($state))
                    ->hiddenOn('edit')
                    ->rule(Password::default()),
                ]),
                Section::make('Compte - nouveau mot de passe')
                ->description('Pour Cree Un nouveau mot de passe')
                ->icon('heroicon-o-hashtag')
                ->schema([
                    TextInput::make('new_password')
                    ->nullable()
                    ->password()
                    ->dehydrateStateUsing(fn ($state) =>hash::make($state))
                    ->rule(Password::default()),
                    TextInput::make('new_password_confirmation')
                    ->same('new_password')
                    ->requiredWith('new_password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) =>hash::make($state))
                    ->rule(Password::default()),
                ])->hiddenOn('create')
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->icon('heroicon-o-user')->copyable()->sortable()->toggleable()->searchable()->copyable(),
                TextColumn::make('email')->icon('heroicon-o-envelope')->copyable()->sortable()->toggleable()->searchable()->copyable(),

            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\RestoreAction::make()->hidden(fn($record)=>!$record->trashed()),
                Tables\Actions\ViewAction::make()->hidden(fn($record)=>$record->trashed()),
                     Tables\Actions\EditAction::make()->hidden(fn($record)=>$record->trashed()),
                     Tables\Actions\DeleteAction::make()->hidden(fn($record)=>$record->trashed())->action(function($record){

                        if($record->count() == 1){
                            Notification::make()
                            ->danger()
                            ->title('Suppression n’est pas possible')
                            ->body('vous ne pouvez pas supprimer le seul compte disponible')
                            ->send();
                            return ;
                        }
                        return $record->delete();

                 })


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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
