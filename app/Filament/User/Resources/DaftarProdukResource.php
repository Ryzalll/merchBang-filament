<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\DaftarProdukResource\Pages;
use App\Filament\User\Resources\DaftarProdukResource\RelationManagers;
use App\Models\DaftarProduk;
use App\Models\Orders;
use App\Models\User;
use App\Models\DetailOrders;
use App\Models\Products;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DaftarProdukResource extends Resource
{
    protected static ?string $model = Orders::class;

    protected static ?string $modelLabel = 'Pesanan';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pesanan';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Daftar Pesanan';
    protected static ?string $slug = 'daftar-pesanan';
    protected static ?string $label = 'pesanan';

    
    public static function form(Form $form): Form
    {
        $user = Auth::user();
        return $form
            ->schema([
                TextInput::make('user_id')
                    ->label('User ID')
                    ->required()
                    ->numeric()
                    ->default($user->id),
                Repeater::make('details')
                    ->label('Detail Pesanan')
                    ->relationship('details')
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->reactive()
                            ->relationship('product', 'name')
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $product = Products::find($get('product_id'));
                                if ($product) {
                                    $set('price', $product->price);
                                    $set('total_sub', $get('quantity') * $product->price);
                                } else {
                                    $set('price', null);
                                    $set('total_sub', null);
                                }
                            })
                            ->required(),
                        TextInput::make('quantity')
                            ->reactive()
                            ->label('Jumlah')
                            ->numeric()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $price = $get('price') ?? 0;
                                $quantity = $get('quantity') ?? 1;
                                $set('total_sub', $quantity * $price);
                            })
                            ->required(),
                        TextInput::make('price')
                            ->prefix('Rp ')
                            ->suffix(',-')
                            ->label('Harga')
                            ->numeric()
                            ->required(),
                        TextInput::make('total_sub')
                            ->reactive()
                            ->prefix('Rp ')
                            ->suffix(',-')
                            ->label('Total Harga')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->createItemButtonLabel('Tambah Detail Pesanan'),
                TextInput::make('discount')
                    ->reactive()
                    ->label('Diskon')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('total')
                    ->label('Total Harga')
                    ->reactive()
                    ->prefix('Rp ')
                    ->suffix(',-')
                    ->numeric()
                    ->required()
                    ->placeholder(function (Set $set, Get $get){
                        $total_all = collect($get('details'))->pluck('total_sub')->sum();
                        $discount = $get('discount') * $total_all / 100 ?? 0;
                        $set('total', $total_all - $discount);
                    })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Pesanan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Nama User')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('details_count')
                    ->label('Jumlah Detail Pesanan')
                    ->counts('details'),
                TextColumn::make('total')
                    ->label('Total Harga')
                    ->prefix('Rp ')
                    ->suffix(',-')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDaftarProduks::route('/'),
            'create' => Pages\CreateDaftarProduk::route('/create'),
            'edit' => Pages\EditDaftarProduk::route('/{record}/edit'),
        ];
    }
}
