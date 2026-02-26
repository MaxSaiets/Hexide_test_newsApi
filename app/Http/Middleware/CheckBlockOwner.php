<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\NewsBlock;

class CheckBlockOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $block = $request->route('new_block');

        if($block){
            $block = NewsBlock::find($block);
            if($block->news->user_id !== $request->user()->id){
                return response()->json([
                    'message' => 'Forbidden',
                ], 403);
            }
        }
        return $next($request);
    }
}
