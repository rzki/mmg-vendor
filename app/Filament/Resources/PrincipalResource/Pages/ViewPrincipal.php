<?php

namespace App\Filament\Resources\PrincipalResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PrincipalResource;

class ViewPrincipal extends ViewRecord
{
    protected static string $resource = PrincipalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn ($record) => is_null($record->approver_id) && auth()->user()->hasRole(['Super Admin', 'Approver']))
                ->action(function ($record) {
                    $record->approver_id = auth()->id();
                    $record->save();
                    Notification::make()
                        ->title('Principal approved successfully.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('unapprove')
                ->label('Unapprove')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn ($record) => !is_null($record->approver_id) && auth()->user()->hasRole(['Super Admin', 'Approver']) && $record->approver_id === auth()->id())
                ->action(function ($record) {
                    $record->approver_id = null;
                    $record->save();
                    Notification::make()
                        ->title('Principal unapproved successfully.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('checked')
                ->label('Checked')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->visible(fn ($record) => is_null($record->checker_id) && auth()->user()->hasRole(['Super Admin', 'Checker']))
                ->action(function ($record) {
                    $record->checker_id = auth()->id();
                    $record->save();
                    Notification::make()
                        ->title('Principal checked successfully.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('unchecked')
                ->label('Unchecked')
                ->icon('heroicon-o-eye-slash')
                ->color('warning')
                ->visible(fn ($record) => !is_null($record->checker_id) && auth()->user()->hasRole(['Super Admin', 'Checker']) && $record->checker_id === auth()->id())
                ->action(function ($record) {
                    $record->checker_id = null;
                    $record->save();
                    Notification::make()
                        ->title('Principal unchecked successfully.')
                        ->success()
                        ->send();
                }),
            Actions\EditAction::make()
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->color('primary')
                ->visible(fn () => auth()->user()->hasRole(['Super Admin']))

        ];
    }
}
