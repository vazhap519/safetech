<?php

namespace App\Providers;

use App\Events\LeadCreated;
use App\Domain\Leads\Contracts\LeadRepository;
use App\Domain\Content\Contracts\ProjectRepository;
use App\Domain\Content\Contracts\ServiceRepository;
use App\Listeners\ForwardLeadToCrm;
use App\Listeners\SendLeadNotification;
use App\Infrastructure\Persistence\EloquentProjectRepository;
use App\Infrastructure\Persistence\EloquentServiceRepository;
use App\Infrastructure\Persistence\EloquentLeadRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LeadRepository::class, EloquentLeadRepository::class);
        $this->app->bind(ServiceRepository::class, EloquentServiceRepository::class);
        $this->app->bind(ProjectRepository::class, EloquentProjectRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('contact-leads', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('analytics-events', function (Request $request): Limit {
            return Limit::perMinute(120)->by(
                ($request->string('visitor_id')->value() ?: $request->ip()) .
                '|' .
                ($request->input('event_type') ?: 'unknown'),
            );
        });

        Event::listen(LeadCreated::class, SendLeadNotification::class);
        Event::listen(LeadCreated::class, ForwardLeadToCrm::class);
    }
}
