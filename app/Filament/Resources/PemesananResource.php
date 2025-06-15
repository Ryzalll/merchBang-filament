<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemesananResource\Pages;
use App\Filament\Resources\PemesananResource\RelationManagers;
use App\Models\Orders;
use App\Models\Pemesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PemesananResource extends Resource
{
    protected static ?string $model = Orders::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Completed' => 'Completed',
                        'Cancelled' => 'Cancelled',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('ID_pemesanan'),
            Tables\Columns\TextColumn::make('details')
                ->label('Product')
                ->html()
                ->formatStateUsing(fn ($record) =>
                    $record->details->map(function($detail) {
                        $name = $detail->product ? $detail->product->name : '-';
                        $qty = $detail->quantity;
                        $price = 'Rp ' . number_format($detail->price, 0, ',', '.');
                        return "{$name} (Qty: {$qty}, {$price})";
                    })->implode('<br>')
                ),
            Tables\Columns\TextColumn::make('status')
                ->label('Status'),
            Tables\Columns\TextColumn::make('discount')
                ->label('Diskon')
                ->formatStateUsing(fn ($record) => $record->discount ? $record->discount . '%' : 'Tidak ada diskon'),
            Tables\Columns\TextColumn::make('total')
                ->label('Harga Total')
                ->formatStateUsing(fn ($record) => 'Rp ' . number_format($record->total, 0, ',', '.')),
            Tables\Columns\TextColumn::make('order_date')
                ->label('Tanggal'),
            Tables\Columns\TextColumn::make('user.name')
                ->label('User'),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->color(fn (string $state): string => match ($state) {
                    'Pending' => 'warning',
                    'Completed' => 'success',
                    'Cancelled' => 'danger',
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'Pending'),
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
            'index' => Pages\ListPemesanans::route('/'),
            // 'create' => Pages\CreatePemesanan::route('/create'),
            'edit' => Pages\EditPemesanan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['details.product']);
    }
}
