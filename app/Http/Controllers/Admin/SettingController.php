<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings'   => 'required|array',
            'settings.*' => 'nullable|string|max:1000',
        ]);

        foreach ($data['settings'] as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
            Cache::forget("setting_{$key}");
        }

        ActivityLog::recordAdmin('settings.updated', 'System settings updated.');

        return back()->with('success', 'Settings saved successfully.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $file = $request->file('logo');
        $ext  = $file->getClientOriginalExtension();
        $file->move(public_path('images'), "logo.{$ext}");

        // Remove old logo of different ext
        $exts = ['png', 'jpg', 'jpeg'];
        foreach ($exts as $e) {
            if ($e !== $ext && file_exists(public_path("images/logo.{$e}"))) {
                unlink(public_path("images/logo.{$e}"));
            }
        }

        ActivityLog::recordAdmin('settings.logo_uploaded', 'School logo updated.');

        return back()->with('success', 'Logo uploaded successfully.');
    }
}
