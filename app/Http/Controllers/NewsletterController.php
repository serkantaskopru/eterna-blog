<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NewsletterController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'email' => 'required|email|max:128',
        ]);

        try {
            Newsletter::create([
                'email' => $request->input('email'),
                'ip_address' => $request->ip()
            ]);
            return Redirect::back()->with([
                'data' => 'Newsletter subscribe successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error subscribing newsletter', 'error' => $exception->getMessage()], 500);
        }
    }
}
