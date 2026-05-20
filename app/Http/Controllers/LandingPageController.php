<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;

class LandingPageController extends Controller
{
    public function index()
    {
        // Get newly added products (latest 4 products with status 'active')
        $featuredProducts = Product::where('status', 'active')
            ->with(['category', 'reviews'])
            ->latest()
            ->take(4)
            ->get();

        $businesses = User::where('role', User::ROLE_BUSINESS)
            ->where('status', User::STATUS_ACTIVE)
            ->with('businessProfile')
            ->get();
            
        return view('landingpage', compact('featuredProducts', 'businesses'));
    }
}
