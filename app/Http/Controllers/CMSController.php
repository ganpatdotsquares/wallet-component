<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class CMSController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->where('status', '1')->first();
        if (!$page) {
            //   return  redirect()->route('front.home');
        }
        return view('frontendpages.show', compact('page'));
    }
}
