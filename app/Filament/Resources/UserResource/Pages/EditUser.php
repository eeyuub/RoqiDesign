<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function mutateFormDataBeforeSave(array $data):array
    {
        if(password_verify('', $data['new_password'])) {
            data_forget($data,'new_password');
            data_forget($data,'new_password_confirmation');
            return $data;
        }

        if(array_key_exists('new_password',$data) || filled($data['new_password'])){
            $data['password']=$data['new_password'];
            data_forget($data,'new_password');
            data_forget($data,'new_password_confirmation');
        }
            return $data;
    }
}
