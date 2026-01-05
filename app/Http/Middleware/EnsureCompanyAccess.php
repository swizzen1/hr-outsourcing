<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $company = $request->route('company');

        if ($company instanceof Company) {
            $companyId = $company->id;
        } else {
            $companyId = (int) $company;
        }

        if ($user && $user->isAdminOrHr()) {
            return $next($request);
        }

        if ($user && $user->company_id === $companyId) {
            return $next($request);
        }

        abort(403, 'Forbidden');
    }
}
