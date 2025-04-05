<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = request()->user();

        $shareData = [
            'message' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'title' => config('app.name'),
            'auth' => [
                'user' => $user,
            ],
            'request_id' => str()->uuid(),
        ];

        if ($user) {
            $shareData['auth']['is_admin'] = $user->id === 1;
            $shareData['is_expired'] = false; // $user->isExpired();
        }

        return [
            ...parent::share($request),
            ...$shareData
        ];
    }
}
