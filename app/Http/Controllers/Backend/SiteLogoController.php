<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SiteLogo;
use Illuminate\Support\Facades\DB;
use Exception;

class SiteLogoController extends Controller
{
    public function edit()
    {
        $siteLogo = SiteLogo::find(1); // Assuming there's only one record or use other means to get the right record
        return view('backend.site_logos.edit', compact('siteLogo'));
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $siteLogo = SiteLogo::findOrFail(1); // Assuming there's only one record or use other means to get the right record

            // Handle main logo upload
            if ($request->hasFile('main_logo')) {
                $logo = $request->file('main_logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('upload/site_logos'), $logoName);
                $siteLogo->main_logo = $logoName;
            }

            // Handle favicon upload
            if ($request->hasFile('mail_favicon')) {
                $favicon = $request->file('mail_favicon');
                $faviconName = time() . '.' . $favicon->getClientOriginalExtension();
                $favicon->move(public_path('upload/site_logos'), $faviconName);
                $siteLogo->mail_favicon = $faviconName;
            }

            $siteLogo->save();
            DB::commit();

            return redirect()->route('site_logos.edit')->with('status', 'Site logos updated successfully.');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->route('site_logos.edit')->with('status', $exception->getMessage());
        }
    }
}

