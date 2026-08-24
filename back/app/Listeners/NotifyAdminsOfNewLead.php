<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Filament\Resources\ContactLeadResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

final class NotifyAdminsOfNewLead
{
    public function handle(LeadCreated $event): void
    {
        $lead = $event->lead;
        $fullName = trim($lead->name ?: trim($lead->first_name.' '.$lead->last_name));
        $summary = collect([
            $fullName !== '' ? $fullName : null,
            filled($lead->phone) ? $lead->phone : null,
            filled($lead->service) ? $lead->service : null,
        ])->filter()->implode(' • ');

        User::query()
            ->where('is_admin', true)
            ->eachById(function (User $admin) use ($lead, $summary): void {
                try {
                    Notification::make()
                        ->title('ახალი მოთხოვნა')
                        ->body($summary !== ''
                            ? $summary
                            : 'SafeTech-ის საიტიდან ახალი მოთხოვნა შემოვიდა.')
                        ->success()
                        ->actions([
                            Action::make('view')
                                ->label('მოთხოვნის გახსნა')
                                ->url(ContactLeadResource::getUrl('edit', ['record' => $lead])),
                        ])
                        ->sendToDatabase($admin);
                } catch (Throwable $exception) {
                    Log::warning('Unable to create admin lead notification.', [
                        'lead_id' => $lead->getKey(),
                        'admin_id' => $admin->getKey(),
                        'exception' => $exception::class,
                    ]);
                }
            });
    }
}
