<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\Woocommerce\ProductSyncEvent;
use App\Events\Woocommerce\OrderSyncEvent;
use App\Events\Woocommerce\CategorySyncEvent;
use App\Listeners\Woocommerce\HandleProductSync;
use App\Listeners\Woocommerce\HandleOrderSync;
use App\Listeners\Woocommerce\HandleCategorySync;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ProductSyncEvent::class => [
            HandleProductSync::class,
        ],
        OrderSyncEvent::class => [
            HandleOrderSync::class,
        ],
        CategorySyncEvent::class => [
            HandleCategorySync::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
