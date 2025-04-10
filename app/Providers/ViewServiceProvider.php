<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $cartItemCount = Auth::user()->cart ? Auth::user()->cart->items->sum('quantity') : 0;
                $view->with('cartItemCount', $cartItemCount);
            }
        });
    }
}
