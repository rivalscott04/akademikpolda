<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $packages = Package::active()->ordered()->get();

        return view('subscription.packages', compact('packages'));
    }
}
