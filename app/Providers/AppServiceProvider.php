<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //DEFAULT RESPONSE RATELIMITER
    //   RateLimiter::for('login', function (Request $request) {
    //     return Limit::perMinute(5)->by($request->input('email'));  
        
    //   });
    // }
    // }
        
      // //2. Allow only 3 attempts every 2 minutes:
    // return Limit::perMinutes(2, 3)->by($request->ip());



   // 5.Track by email address instead of IP:
    // RateLimiter::for('login', function (Request $request) {
    //     return Limit::perMinute(5)->by($request->input('email'));
   

    // RATE LIMITER WITH ERROR MESSAGE
  
    RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip())->response(function()
    {
      return response()->json([
        'message' => 'Too many login attempts. Try after 60 seconds.'
      ], 429);
    });
  });
}
}
