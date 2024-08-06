<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $modelLabel = 'ユーザー';
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'ユーザー管理';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('personal_id')
                    ->label('個人番号')
                    ->disabled(fn (Page $livewire) => $livewire instanceof EditRecord),
                TextInput::make('password')->hidden()->default('password'),

                // dateのフォームのサンプル
                // Forms\Components\DatePicker::make('date')
                //     ->required()
                //     ->afterOrEqual(now()->format('Y-m-d')),

                // Using Select Component
                Select::make('roles')
                    ->relationship('roles', 'name')
                    // ->multiple()
                    ->preload(),

                // Using CheckboxList Component
                // CheckboxList::make('roles')
                //     ->relationship('roles', 'name')
                //     ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('personal_id')->sortable(),
                Tables\Columns\TextColumn::make('roles.name'),
                // トグルパターン（アクション付き）
                // Tables\Columns\ToggleColumn::make('is_registered'),
                // アイコンパターン（アクションはaction関数で追加）
                Tables\Columns\IconColumn::make('is_registered')
                    ->icon(fn (): string => 'heroicon-o-check-circle')
                    ->color(fn (bool $state): string => match ($state) {
                        true => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->sortable(),
            ])
            ->filters([
                SelectFilter::make('personal_id')
                    ->options(User::all()->pluck('personal_id', 'personal_id'))
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('registered')
                    ->accessSelectedRecords()
                    ->action(function (Model $record, Collection $selectedRecords) {
                        User::where(['id' => $record->id])->update(['is_registered'=>1]);
                    })
                    ->icon('heroicon-o-user')
                    ->label('本登録する'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->persistFiltersInSession();

        if (!Auth::user()->is_teacher) {
            $table->modifyQueryUsing(fn (Builder $query) => $query->where(['personal_id' => Auth::user()->personal_id]));
        }

        return $table;
    }

    public static function getRelations(): array
    {
        return [
            // UserRolesRelationManager::class,
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
