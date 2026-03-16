<?php

declare(strict_types=1);

namespace Quicktane\Notification;

use App\Customer\Events\AfterCustomerRegister;
use App\Order\Events\AfterOrderStatusChange;
use Illuminate\Support\Facades\Event;
use Quicktane\Core\Module\ModuleServiceProvider;
use Quicktane\Notification\Contracts\NotificationFacade as NotificationFacadeContract;
use Quicktane\Notification\Facades\NotificationFacade;
use Quicktane\Notification\Listeners\SendCustomerWelcomeListener;
use Quicktane\Notification\Listeners\SendOrderConfirmationListener;
use Quicktane\Notification\Listeners\SendOrderStatusUpdateListener;
use Quicktane\Notification\Repositories\MysqlNotificationLogRepository;
use Quicktane\Notification\Repositories\NotificationLogRepository;

class NotificationServiceProvider extends ModuleServiceProvider
{
    public function moduleName(): string
    {
        return 'notification';
    }

    public function register(): void
    {
        $this->app->bind(NotificationLogRepository::class, MysqlNotificationLogRepository::class);

        $this->app->bind(NotificationFacadeContract::class, NotificationFacade::class);
    }

    public function boot(): void
    {
        $this->loadModuleMigrations();
        $this->loadModuleRoutes();
        $this->loadModuleConfig();
        $this->loadViewsFrom($this->packagePath().'/resources/views', 'notification');

        $this->registerEventListeners();
    }

    private function registerEventListeners(): void
    {
        Event::listen(AfterOrderStatusChange::class, [SendOrderConfirmationListener::class, 'handle']);
        Event::listen(AfterOrderStatusChange::class, [SendOrderStatusUpdateListener::class, 'handle']);
        Event::listen(AfterCustomerRegister::class, [SendCustomerWelcomeListener::class, 'handle']);
    }
}
