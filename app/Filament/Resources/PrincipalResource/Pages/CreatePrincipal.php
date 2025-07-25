<?php

namespace App\Filament\Resources\PrincipalResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PrincipalResource;
use Actions\CreateAnotherAction;

class CreatePrincipal extends CreateRecord
{
    protected static string $resource = PrincipalResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure the 'principalId' is set to null before creating a new record
        $data['principalId'] = Str::orderedUuid();
        $data['creator_id'] = auth()->user()->id;
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Principal Data Created')
            ->body('The principal data has been successfully created');
    }
}
