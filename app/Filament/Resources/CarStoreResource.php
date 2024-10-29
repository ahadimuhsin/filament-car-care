<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarStoreResource\RelationManagers\PhotosRelationManager;
use Filament\Forms;
use Filament\Tables;
use App\Models\CarStore;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\CarStoreResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CarStoreResource\RelationManagers;
use App\Filament\Resources\CarStoreResource\Pages\EditCarStore;
use App\Filament\Resources\CarStoreResource\Pages\ListCarStores;
use App\Filament\Resources\CarStoreResource\Pages\CreateCarStore;
use App\Models\CarService;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class CarStoreResource extends Resource
{
    protected static ?string $model = CarStore::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Nama Toko
                TextInput::make("name")
                ->label("Store Name")
                ->helperText("Nama Toko harus diisi")
                ->required()
                ->maxLength(255),

                // Harga
                TextInput::make("phone_number")
                ->label("Phone Number")
                ->required()
                ->helperText("Nomor Telepon"),

                // Durasi Per Jam
                TextInput::make("cs_name")
                ->label("PIC Name")
                ->required()
                ->helperText("Nama CS atau Manager Toko"),

                // Status Buka
                Select::make("is_open")
                ->options([
                    true => "Open",
                    false => "Closed"
                ])
                ->required(),

                // Status Availability
                Select::make("is_full")
                ->options([
                    true => "Fully Booked",
                    false => "Available"
                ])
                ->required(),

                // Kota
                Select::make("city_id")
                ->relationship("city", "name")
                ->searchable()
                ->preload()
                ->required(),

                Repeater::make("storeServices")
                ->label("Jenis Layanan")
                ->relationship()
                ->schema(
                    [Select::make("car_service_id")
                    ->relationship('service', 'name')
                    ->required()
                    ]
                ),

                // Thumbnail
                FileUpload::make("thumbnail")
                ->image()
                ->required(),

                // Alamat
                Textarea::make("address")
                ->rows(10)
                ->cols(20)
                ->required()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")->searchable(),

                IconColumn::make("is_open")
                ->boolean()
                ->trueColor("success")
                ->falseColor("danger")
                ->trueIcon("heroicon-o-check-circle")
                ->falseIcon("heroicon-o-x-circle")
                ->label("Buka?"),

                IconColumn::make("is_full")
                ->boolean()
                ->trueColor("danger")
                ->falseColor("success")
                ->falseIcon("heroicon-o-check-circle")
                ->trueIcon("heroicon-o-x-circle")
                ->label("Tersedia?"),

                ImageColumn::make("thumbnail")
            ])
            ->filters([
                SelectFilter::make("city_id")
                ->label('City')
                ->relationship('city', 'name'),

                SelectFilter::make("car_service_id")
                ->label("Service")
                ->options(CarService::pluck("name", "id"))
                ->query(function(Builder $query, array $data){
                    if($data["value"])
                    {
                        $query->whereHas('storeServices', function($query) use($data){
                            $query->where("car_service_id", $data['value']);
                        });
                    }
                })
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
            PhotosRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarStores::route('/'),
            'create' => Pages\CreateCarStore::route('/create'),
            'edit' => Pages\EditCarStore::route('/{record}/edit'),
        ];
    }
}
