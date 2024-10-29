<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarServiceResource\Pages;
use App\Filament\Resources\CarServiceResource\RelationManagers;
use App\Models\CarService;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarServiceResource extends Resource
{
    protected static ?string $model = CarService::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Nama Kota
                TextInput::make("name")
                ->helperText("Nama kota harus diisi")
                ->required()
                ->maxLength(255),

                // Harga
                TextInput::make("price")
                ->numeric()
                ->required()
                ->prefix("IDR")
                ->helperText("Harga"),

                // Durasi Per Jam
                TextInput::make("duration_in_hour")
                ->required()
                ->numeric()
                ->helperText("Harga Per Jam"),

                // Deskripsi
                Textarea::make("about")
                ->required()
                ->rows(5)
                ->columns(10),

                // File Foto
                FileUpload::make("photo")
                ->image()
                ->required(),

                // Icon
                FileUpload::make("icon")
                ->image()
                ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")->searchable(),
                TextColumn::make('price')->formatStateUsing(fn (string $state): string => number_format($state, 0, '.', '.'))->prefix("Rp "),
                TextColumn::make('duration_in_hour'),
                ImageColumn::make('icon')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCarServices::route('/'),
            'create' => Pages\CreateCarService::route('/create'),
            'edit' => Pages\EditCarService::route('/{record}/edit'),
        ];
    }
}
