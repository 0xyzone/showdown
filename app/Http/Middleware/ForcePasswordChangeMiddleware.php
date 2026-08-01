<?php

namespace App\Http\Middleware;

use App\Filament\Pages\ForcePasswordChange;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChangeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->must_change_password) {
            $forcePasswordChangeUrl = ForcePasswordChange::getUrl();

            // Allow access strictly to force-password-change page, livewire updates for it, and logout action
            if (
                ! $request->fullUrlIs($forcePasswordChangeUrl) &&
                ! $request->is('*/livewire/*') &&
                ! $request->is('*/logout') &&
                ! str_ends_with($request->path(), '/logout')
            ) {
                return redirect()->to($forcePasswordChangeUrl);
            }
        }

        return $next($request);
    }
}
