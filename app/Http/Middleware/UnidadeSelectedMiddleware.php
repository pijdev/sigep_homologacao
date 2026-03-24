<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnidadeSelectedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se for uma requisição de login, armazenar a unidade selecionada
        if ($request->isMethod('POST') && $request->route()->named('login')) {
            $unidadeId = $request->input('unidade_id');
            if ($unidadeId) {
                session(['unidade_selecionada' => $unidadeId]);
            }
        }

        return $next($request);
    }
}
