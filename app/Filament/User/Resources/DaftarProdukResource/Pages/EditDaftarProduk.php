<?php

namespace App\Filament\User\Resources\DaftarProdukResource\Pages;

use App\Filament\User\Resources\DaftarProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDaftarProduk extends EditRecord
{
    protected static string $resource = DaftarProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
