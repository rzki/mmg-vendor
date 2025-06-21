<?php

namespace App\Filament\Resources\PrincipalResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PrincipalResource;

class ViewPrincipal extends ViewRecord
{
    protected static string $resource = PrincipalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('checker_id')
                ->label('Mark as Checked')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => auth()->user()->hasRole(['Super Admin', 'Admin', 'Checker']))
                ->action(function ($record) {
                    $record->checker_id = auth()->id();
                    $record->save();
                }),

            Actions\Action::make('approver_id')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => auth()->user()->hasRole(['Super Admin', 'Manager', 'Head', 'BOD']))
                ->action(function ($record) {
                    $record->checker_id = auth()->id();
                    $record->save();
                }),
            Actions\EditAction::make(),

        ];
    }
}
