<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('change-password')
                ->label('Editar Senha')
                ->icon('heroicon-o-lock-closed')
                ->modal()
                ->modalHeading('Alterar Senha')
                ->form([
                    Forms\Components\TextInput::make('password')
                        ->label('Senha:')
                        ->placeholder('Digite a senha do colaborador...')
                        ->password()
                        ->revealable()
                        ->columnSpan(2)
                        ->minLength(8)
                        ->maxLength(255)
                        ->required(),
                ])
                ->modalSubmitActionLabel('Salvar')
                ->action(function ($data, $record) {
                    $record->update($data);

                    Notification::make()
                        ->title('Senha alterada')
                        ->body('Senha deste colaborador foi alterada com sucesso!')
                        ->success()
                        ->send();

                    return $record;
                }),
            Actions\DeleteAction::make()
                ->disabled(fn ($record) => $record->id === Auth::id()),
        ];
    }
}
