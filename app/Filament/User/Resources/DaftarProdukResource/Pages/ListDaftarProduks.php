<?php

namespace App\Filament\User\Resources\DaftarProdukResource\Pages;

use App\Filament\User\Resources\DaftarProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaftarProduks extends ListRecords
{
    protected static string $resource = DaftarProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
