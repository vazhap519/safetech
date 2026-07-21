<?php

namespace App\Providers;

use App\Domain\Content\Contracts\ProjectRepository;
use App\Domain\Content\Contracts\ServiceRepository;
use App\Domain\Leads\Contracts\LeadRepository;
use App\Events\LeadCreated;
use App\Infrastructure\Persistence\EloquentLeadRepository;
use App\Infrastructure\Persistence\EloquentProjectRepository;
use App\Infrastructure\Persistence\EloquentServiceRepository;
use App\Listeners\ForwardLeadToCrm;
use App\Listeners\SendLeadNotification;
use App\Models\Author;
use App\Models\Category;
use App\Models\CategoryForService;
use App\Models\ContactLead;
use App\Models\Estimate;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\Post;
use App\Models\PostSection;
use App\Models\PrivacyPolicy;
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
        foreach ($this->auditedModels() as $model) {
            $model::observe(AdminAuditObserver::class);
        }

        RateLimiter::for('contact-leads', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('analytics-events', function (Request $request): Limit {
            return Limit::perMinute(120)->by(
                ($request->string('visitor_id')->value() ?: $request->ip()).
                '|'.
                ($request->input('event_type') ?: 'unknown'),
            );
        });

        Event::listen(LeadCreated::class, SendLeadNotification::class);
        Event::listen(LeadCreated::class, ForwardLeadToCrm::class);
    }

    /** @return array<int, class-string<Model>> */
    private function auditedModels(): array
    {
        return [
            Author::class,
            Category::class,
            CategoryForService::class,
            ContactLead::class,
            Estimate::class,
            Faq::class,
            Partner::class,
            Post::class,
            PostSection::class,
            PrivacyPolicy::class,
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
