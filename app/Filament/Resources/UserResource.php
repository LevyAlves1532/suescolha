<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $slug = 'collaborators';

    protected static ?string $modelLabel = 'colaborador';
    
    protected static ?string $pluralModelLabel = 'colaboradores';

    protected static ?string $tenantOwnershipRelationshipName = 'companies';

    protected static ?string $tenantRelationshipName = 'users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Colaborador')
                    ->columns(2)
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('avatar')
                            ->collection('collaboratorAvatars')
                            ->label('Foto de Perfil:')
                            ->avatar()
                            ->columnSpan(2)
                            ->alignCenter(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nome:')
                            ->placeholder('Digite o nome do colaborador...')
                            ->minLength(3)
                            ->maxLength(100)
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail:')
                            ->placeholder('Digite o e-mail do colaborador...')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->label('Senha:')
                            ->placeholder('Digite a senha do colaborador...')
                            ->password()
                            ->revealable()
                            ->columnSpan(2)
                            ->minLength(8)
                            ->maxLength(255)
                            ->required()
                            ->visibleOn('create'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('avatar')
                    ->label('Foto de Perfil:')
                    ->collection('collaboratorAvatars')
                    ->defaultImageUrl(asset('assets/img/default-profile.png'))
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome:')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail:')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativo:')
                    ->disabled(fn ($record) => $record->id === Auth::id()),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->disabled(fn ($record) => $record->id === Auth::id()),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->disabled(fn ($record) => $record->id === Auth::id())
                    ->before(function ($record) {
                        Filament::getTenant()->users()->detach($record->id);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'view' => Pages\ViewUser::route('/{record}/view'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
