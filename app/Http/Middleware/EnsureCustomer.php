<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::Customer) {
            if ($user?->role === UserRole::Technician) {
                return redirect()->route('technician.dashboard');
            }

            if ($user?->isStaffMember()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/');
        }

        return $next($request);
    }
}
