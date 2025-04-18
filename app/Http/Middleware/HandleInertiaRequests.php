<?php

namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            'auth' => [
                'user' => $request->user() ? [
                    'USER_ID'   => $request->user()->USER_ID,
                    'full_name' => $request->user()->userProfile->full_name,
                    'ID_NUMBER' => $request->user()->userProfile->ID_NUMBER,
                    'course' => $request->user()->course,
                ] : null,
            ],

            'ziggy' => fn (): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],

            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
