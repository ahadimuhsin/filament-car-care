<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\CarStore;
use Filament\Forms\Form;
use App\Models\CarService;
use Filament\Tables\Table;
use App\Models\StoreService;
use Filament\Resources\Resource;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookingTransactionResource\Pages;
use App\Filament\Resources\BookingTransactionResource\RelationManagers;

class BookingTransactionResource extends Resource
{
    protected static ?string $model = BookingTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make("name")
                ->required()
                ->maxLength(255),

                TextInput::make("trx_id")
                ->required()
                ->maxLength(255),

                TextInput::make("phone_number")
                ->required()
                ->maxLength(255),

                TextInput::make("total_amount")
                ->numeric()
                ->required()
                ->prefix("IDR"),

                DatePicker::make("started_at")
                ->required(),

                TimePicker::make("time_at")
                ->datalist([
                    "09:00",
                    "10:00",
                    "11:00",
                    "13:00",
                    "14:00",
                    "15:00",
                    "16:00",
                    "17:00",
                    "18:00",
                    "19:00",
                    "20:00",
                    "21:00",
                ])
                ->required(),

                Select::make("is_paid")
                ->label('Status Bayar')
                ->options(
                    [
                        true => "Paid",
                        false => "Not Paid"
                    ]
                )->required(),

                Select::make("car_store_id")
                ->relationship("store_details", "name")
                ->searchable()
                ->preload()
                ->required()
                ->live(),

                Select::make("car_service_id")
                // ->options(function (Get $get){
                //     dd($get);
                //     $store = CarStore::find($get('car_store_id'));

                //     Log::info($store);
                //     dd($store);
                //     $service_id = StoreService::where("car_store_id", $store->id)->pluck("car_service_id")->toArray();

                //     $services = CarService::whereIn("id", $service_id)->pluck("name", "id");

                //     return $services;
                //     dd($store);
                // })
                ->relationship("service_details", "name")
                ->searchable()
                ->preload()
                ->required(),


                FileUpload::make("proof")
                ->acceptedFileTypes(["application/pdf", "image/png", "image/jpeg"])
                ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make("trx_id")
                ->searchable(),

                TextColumn::make("make")
                ->searchable(),

                TextColumn::make("service_details.name"),
                TextColumn::make("started_at"),
                TextColumn::make("time_at"),
                IconColumn::make("is_paid")
                ->boolean()
                ->trueColor('success')
                ->falseColor("danger")
                ->trueIcon("heroicon-o-check-circle")
                ->falseIcon("heroicon-o-x-circle")
                ->label("Status Pembayaran"),

                ImageColumn::make("proof")
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
            'index' => Pages\ListBookingTransactions::route('/'),
            'create' => Pages\CreateBookingTransaction::route('/create'),
            'edit' => Pages\EditBookingTransaction::route('/{record}/edit'),
        ];
    }
}
