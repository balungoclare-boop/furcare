<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Report;
use App\Models\User;
use App\Models\Vaccination;
use App\Policies\AppointmentPolicy;
use App\Policies\PetPolicy;
use App\Policies\ReportPolicy;
use App\Policies\UserPolicy;
use App\Policies\VaccinationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Pet::class, PetPolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Vaccination::class, VaccinationPolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        View::composer('layouts.app', function ($view): void {
            $user = auth()->user();

            $pendingAppointments = 0;
            $clientPendingAppointments = 0;
            $unreadNotificationsCount = 0;
            $recentNotifications = collect();

            if ($user && $user->isStaffOrAdmin()) {
                $pendingAppointments = Appointment::query()
                    ->where('status', Appointment::STATUS_PENDING)
                    ->whereNull('accepted_at')
                    ->count();
            }

            if ($user && $user->isClient()) {
                $clientPendingAppointments = $user->appointments()
                    ->where('status', Appointment::STATUS_PENDING)
                    ->whereNull('accepted_at')
                    ->count();
            }

            if ($user) {
                $unreadNotificationsCount = $user->unreadNotifications()->count();
                $recentNotifications = $user->notifications()
                    ->latest()
                    ->limit(5)
                    ->get();
            }

            $view->with(compact(
                'pendingAppointments',
                'clientPendingAppointments',
                'unreadNotificationsCount',
                'recentNotifications'
            ));
        });
    }
}
