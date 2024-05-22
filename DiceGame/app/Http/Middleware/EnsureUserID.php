<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserID
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $idGoal = $request->route()->parameter('id');
        $idUser = Auth::user()->id;
        if($idGoal == $idUser){
            return $next($request); 
        }
        
        return response()->json(['message' => 'User does not own the resource'], 403);
    }
}
