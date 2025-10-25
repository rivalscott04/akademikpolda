<?php

namespace App\Http\Controllers;

use Lab404\Impersonate\Controllers\ImpersonateController as BaseImpersonateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonateController extends BaseImpersonateController
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function take(Request $request, $id, $guardName = null)
    {
        // Call parent method
        $response = parent::take($request, $id, $guardName);
        
        // Redirect to user profile after impersonation
        return redirect()->route('user.profile', ['userId' => $id]);
    }
    
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function leave()
    {
        // Call parent method
        $response = parent::leave();
        
        // Force redirect to admin dashboard
        return redirect()->route('admin.dashboard');
    }
} 