<?php

namespace App\Providers;

use App\Domain\Content\Contracts\ProjectRepository;
use App\Domain\Content\Contracts\ServiceRepository;
use App\Domain\Leads\Contracts\LeadRepository;
use App\Events\LeadCreated;
use App\Filament\Support\CmsMediaUpload;
use App\Infrastructure\Persistence\EloquentLeadRepository;
use App\Infrastructure\Persistence\EloquentProjectRepository;
use App\Infrastructure\Persistence\EloquentServiceRepository;
use App\Listeners\ForwardLeadToCrm;
use App\Listeners\SendLeadNotification;
use App\Models\CategoryForService;
use App\Models\ContactLead;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use App\Observers\AdminAuditObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LeadRepository::class, EloquentLeadRepository::class);
        $this->app->bind(ServiceRepository::class, EloquentServiceRepository::class);
        $this->app->bind(ProjectRepository::class, EloquentProjectRepository::class);
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            $appUrl = rtrim((string) config('app.url'), '/');

            if ($appUrl !== '') {
                URL::forceRootUrl($appUrl);
            }

            URL::forceScheme('https');
        }

        CmsMediaUpload::registerDefaults();
        CmsMediaUpload::ensureTemporaryDirectory();
        CmsMediaUpload::installProductionNginxLimits();

        foreach ($this->auditedModels() as $model) {
            $model::observe(AdminAuditObserver::class);
        }

        RateLimiter::for('contact-leads', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('analytics-events', function (Request $request): array {
            $ip = $request->ip() ?: 'unknown';
            $visitor = mb_substr($request->string('visitor_id')->value(), 0, 100) ?: $ip;
            $eventType = mb_substr((string) $request->input('event_type', 'unknown'), 0, 50);

            return [
                Limit::perMinute(120)->by("analytics-ip|{$ip}"),
                Limit::perMinute(30)->by(
                    'analytics-visitor|'.hash('sha256', $visitor)."|{$eventType}",
                ),
            ];
        });

        Event::listen(LeadCreated::class, SendLeadNotification::class);
        Event::listen(LeadCreated::class, ForwardLeadToCrm::class);
    }

    /** @return array<int, class-string<Model>> */
    private function auditedModels(): array
    {
        return [
            CategoryForService::class,
            ContactLead::class,
            Faq::class,
            Partner::class,
            Project::class,
            ProjectCategory::class,
            SeoPage::class,
            Service::class,
            SiteSetting::class,
            TeamMember::class,
            Testimonial::class,
            User::class,
        ];
    }
}
