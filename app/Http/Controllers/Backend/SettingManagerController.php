<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Plan;
use Validator;
use Illuminate\Http\Response;

class SettingManagerController extends Controller
{
    // Get smtp setting
    public function getSmtpSetting()
    {
        $smtp = Setting::where('manager', 'smtp')->get();
        return view('backend.settings.smtp', compact('smtp'));
    }

    // Update smtp setting
    public function updateSmtpSetting(Request $request)
    {
        foreach($request->input('setting') as $setting) {
            $newUser = Setting::updateOrCreate(
                [
                'slug'   => $setting['slug'],
                ],
                $setting
            );

        }
        return redirect()->route('admin.setting.smtp')->with(['success' => 'Settings has been updated successfully.']);
    }

    //Get all general settings.
    public function getGeneralSetting(Request $request)
    {
        $allowed_columns = ['id', 'title', 'slug'];
        $sort = in_array($request->get('sort'), $allowed_columns) ? $request->get('sort') : 'created_at';
        $order = $request->get('direction') === 'asc' ? 'asc' : 'desc';
        $settings = Setting::orderBy($sort, $order)->where('manager', 'general')->paginate(config('GET.admin_page_limit'));
        return view('backend.settings.general.general-setting', compact('settings'));
    }

    // Show specified general setting
    public function showGeneralSetting($id)
    {
        $settings = Setting::find($id);
        return view('backend.settings.general.show', compact('settings'));
    }

    //Show create general setting form
    public function addGeneralSetting()
    {
        return view('backend.settings.general.createdOrUpdate');
    }

    // Store general setting
    public function storeGeneralSetting(Request $request)
    {
        try {
            $this->validate($request, [
                'title' => 'required',
                'slug' => 'unique:settings,id,' . $request->segment(3),
                'config_value' => 'required',
            ]);
            Setting::create($request->all());
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.setting.general')->with('success', 'General setting created successfully');
    }

    // Show edit form for specified general setting
    public function editGeneralSetting($id)
    {
        $settings = Setting::find($id);
        return view('backend.settings.general.createdOrUpdate', compact('settings'));
    }

    // upadte specified general setting ..
    public function updateGeneralSetting(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'title' => 'required',
                'slug' => 'unique:settings,id,' . $request->segment(3),
                'config_value' => 'required',
            ]);

            $setting = Setting::find($id);
            $setting->fill($request->all());
            $setting->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.setting.general')->with('success', 'Setting updated successfully.');
    }

    public function getlogos(Request $request)
    {
        $settings = Setting::where('manager', '=', 'theme_images')->get();
       
        return view('backend.settings.logo-setting', compact('settings'));
    }
    public function storeLogos(Request $request)
    {
        // dd(!empty($request->File('setting')), $request);
        try {
            $data = $request->input('setting');
            // dd($data, $request->file('setting'));
            $main_logoid = Setting::where('slug', 'main_logo')->select('id')->first();
            $main_faviconid = Setting::where('slug', 'main_favicon')->select('id')->first();
            foreach ($data as $setting) {

                if (!empty($request->file('setting'))) {
                    foreach ($request->file('setting') as $k => $value) {

                        if ($k == $setting['id']) {
                            // $imageName = random_int(1000000, 9999999).'.'.$value['config_value']->extension();
                            // Public Folder
                            if ($k == $main_logoid->id) {
                                $mainlogo = time() . '.' . $value['config_value']->getClientOriginalName();

                                $value['config_value']->move(public_path('assets/photo'), $mainlogo);
                                Setting::where('slug', 'main_logo')->update(['config_value' => $mainlogo]);
                            } elseif ($k == $main_faviconid->id) {

                                $favicon = time() . '.' . $value['config_value']->getClientOriginalName();
                                // dd($imageName);
                                $value['config_value']->move(public_path('assets/photo'), $favicon);

                                Setting::where('slug', 'main_favicon')->update(['config_value' => $favicon]);
                            }
                        }
                    }
                }
                $newUser = Setting::updateOrCreate(
                    [
                        'id' => (isset($setting['id']) && !empty($setting['id'])) ? $setting['id'] : 0,
                    ],
                    $setting
                );
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }

        return redirect()->route('admin.settingtheme')->with(['success' => 'Setting saved successfully.']);
    }
    public function getSocial(Request $request)
    {
        $settings = Setting::where('manager', '=', 'social')->get();

        return view('backend.settings.social', compact('settings'));
    }
    public function storeSocial(Request $request)
    {
        // dd(!empty($request->File('setting')), $request);
        try {
            $data = $request->input('setting');

            foreach ($data as $setting) {
               
                $newUser = Setting::updateOrCreate(
                    [
                        'id' => $setting['id'],
                    ],
                    $setting
                );
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }

        return redirect()->route('admin.social')->with(['success' => 'Setting saved successfully.']);
    }

    
}
