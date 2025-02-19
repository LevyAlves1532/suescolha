<?php

namespace App\Filament\Pages;

use App\Models\Company;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Str;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Registre sua Empresa';
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome:')
                    ->placeholder('Digite o nome da sua empresa...')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state)))
                    ->minLength(3)
                    ->maxLength(100)
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug:')
                    ->placeholder('Digite o slug da sua empresa...')
                    ->disabled(),
                // ...
            ]);
    }
 
    protected function handleRegistration(array $data): Company
    {
        $data['slug'] = Str::slug($data['name']);

        if (Company::where('slug', $data['slug'])->count() > 0) {
            Notification::make()
                ->title('Nome repetido')
                ->body('Já existe uma empresa com esse nome!')
                ->danger()
                ->send();

            $this->halt();
        }

        $company = Company::create($data);
 
        $company->users()->attach(auth()->user());
 
        return $company;
    }
}
