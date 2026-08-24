<?php

namespace App\Providers;

use App\Domain\Content\Contracts\ProjectRepository;
use App\Domain\Content\Contracts\ServiceRepository;
use App\Domain\Leads\Contracts\LeadRepository;
use App\Filament\Support\CmsMediaUpload;
use App\Infrastructure\Persistence\EloquentLeadRepository;
use App\Infrastructure\Persistence\EloquentProjectRepository;
use App\Infrastructure\Persistence\EloquentServiceRepository;
use App\Models\AiKnowledgeCandidate;
use App\Models\AiKnowledgeItem;
use App\Models\CategoryForService;
use App\Models\Concerns\FlushesPublicContentCache;
use App\Models\ContactLead;
use App\Models\Faq;
use App\Models\LocalServiceLanding;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ReviewInvitation;
use App\Models\SeoPage;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use App\Observers\AdminAuditObserver;
use App\Observers\ProjectSocialAutomationObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

        Project::observe(ProjectSocialAutomationObserver::class);

        $this->registerPublicContentMediaInvalidation();

        RateLimiter::for('contact-leads', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('ai-chat', function (Request $request): array {
            $ip = $request->ip() ?: 'unknown';
            $conversation = mb_substr((string) $request->input('conversation_id', 'new'), 0, 60);

            return [
                Limit::perMinute(20)->by("ai-chat-ip|{$ip}"),
                Limit::perMinute(10)->by("ai-chat-conversation|{$ip}|{$conversation}"),
            ];
        });

        RateLimiter::for('ai-feedback', function (Request $request): Limit {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('review-invitations', function (Request $request): Limit {
            return Limit::perMinute(10)->by($request->ip());
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
    }

    private function registerPublicContentMediaInvalidation(): void
    {
        Event::listen(
            MediaHasBeenAddedEvent::class,
            fn (MediaHasBeenAddedEvent $event) => $this->refreshPublicContentForMedia($event->media),
        );

        $mediaModel = config('media-library.media_model', Media::class);

        if (is_string($mediaModel) && is_a($mediaModel, Media::class, true)) {
            $mediaModel::deleted(
                fn (Media $media) => $this->refreshPublicContentForMedia($media),
            );
        }
    }

    private function refreshPublicContentForMedia(Media $media): void
    {
        $owner = $media->model;

        if (! $owner instanceof Model) {
            return;
        }

        if (! in_array(FlushesPublicContentCache::class, class_uses_recursive($owner), true)) {
            return;
        }

        $owner::refreshPublicContent();
    }

    /** @return array<int, class-string<Model>> */
    private function auditedModels(): array
    {
        return [
            AiKnowledgeCandidate::class,
            AiKnowledgeItem::class,
            CategoryForService::class,
            ContactLead::class,
            Faq::class,
            LocalServiceLanding::class,
            Partner::class,
            Project::class,
            ProjectCategory::class,
            ReviewInvitation::class,
            SeoPage::class,
            Service::class,
            SiteSetting::class,
            TeamMember::class,
            Testimonial::class,
            User::class,
        ];
    }
}
