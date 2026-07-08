<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('admin.dashboard', [
            'user' => $user,
            'store' => $user->store,
        ]);
    }
}
