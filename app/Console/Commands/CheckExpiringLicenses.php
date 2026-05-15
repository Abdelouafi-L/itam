<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Models\User;
use App\Notifications\LicenseExpiryNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('itam:check-expiring-licenses')]
#[Description('Send email notifications for licenses expiring within 30 days — RF-20')]
class CheckExpiringLicenses extends Command
{
    /**
     * Execute the console command.
     * Raw PHP equivalent: a cron job running a PHP script daily
     */
    public function handle(): void
    {
        $this->info('Checking expiring licenses...');

        // Find all licenses expiring within 30 days
        // that are still Active
        $expiringLicenses = License::with(['software.product'])
            ->where('status', 'Active')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now()->startOfDay())
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->get();

        if ($expiringLicenses->isEmpty()) {
            $this->info('No expiring licenses found.');
            return;
        }

        $this->info("Found {$expiringLicenses->count()} expiring license(s).");

        // Find all Administrators to notify
        $admins = User::role('Administrateur')
                    ->where('is_active', true)
                    ->get();

        if ($admins->isEmpty()) {
            $this->warn('No active administrators found to notify.');
            return;
        }

        // Send notification to each administrator
        foreach ($admins as $admin) {
            $admin->notify(
                new LicenseExpiryNotification($expiringLicenses)
            );
            $this->info("Notified: {$admin->full_name}");
        }

        $this->info('Done.');
    }
}