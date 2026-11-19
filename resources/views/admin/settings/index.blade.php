@extends('layouts.admin')
@section('title', 'System Settings')

@section('content')
<div class="section-header">
    <div>
        <h2>System Settings</h2>
        <p>Configure school information, contact details, and system options.</p>
    </div>
</div>

<div class="row g-4">
    {{-- Main Settings --}}
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf

            @foreach($settings as $group => $items)
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    @if($group === 'general')
                        <i class="bi bi-building text-primary"></i> General Information
                    @elseif($group === 'contact')
                        <i class="bi bi-telephone text-success"></i> Contact Details
                    @elseif($group === 'academic')
                        <i class="bi bi-mortarboard text-warning"></i> Academic Settings
                    @else
                        <i class="bi bi-gear text-muted"></i> {{ ucfirst($group) }}
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($items as $setting)
                        <div class="col-md-6">
                            <label class="form-label">{{ $setting->label }}</label>
                            @if($setting->key === 'enrollment_open')
                            <select name="settings[{{ $setting->key }}]" class="form-select">
                                <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Yes — Enrollment is Open</option>
                                <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>No — Enrollment is Closed</option>
                            </select>
                            @elseif(in_array($setting->key, ['enrollment_notice']))
                            <textarea name="settings[{{ $setting->key }}]" rows="2" class="form-control">{{ $setting->value }}</textarea>
                            @else
                            <input type="text" name="settings[{{ $setting->key }}]" class="form-control"
                                   value="{{ old('settings.'.$setting->key, $setting->value) }}">
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            <button type="submit" class="btn text-white fw-semibold px-4"
                    style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:10px;padding:.7rem 2rem;">
                <i class="bi bi-save me-1"></i> Save All Settings
            </button>
        </form>
    </div>

    {{-- Logo Upload --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-image me-2 text-primary"></i>School Logo</div>
            <div class="card-body text-center">
                <div class="mb-3" style="width:100px;height:100px;margin:0 auto;border-radius:20px;overflow:hidden;border:2px solid #e2e8f0;background:#f8fafc;display:flex;align-items:center;justify-content:center;">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}?v={{ filemtime(public_path('images/logo.png')) }}"
                             alt="Logo" style="width:100%;height:100%;object-fit:cover;">
                    @elseif(file_exists(public_path('images/logo.jpg')))
                        <img src="{{ asset('images/logo.jpg') }}?v={{ filemtime(public_path('images/logo.jpg')) }}"
                             alt="Logo" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <span style="font-size:3rem;">🏫</span>
                    @endif
                </div>
                <p class="text-muted" style="font-size:.8rem;">Recommended: 200×200px PNG or JPG</p>
                <form method="POST" action="{{ route('admin.settings.logo') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="logo" accept="image/png,image/jpeg" class="form-control mb-2" required>
                    @error('logo')<div class="text-danger" style="font-size:.8rem;">{{ $message }}</div>@enderror
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-upload me-1"></i> Upload Logo
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>System Info</div>
            <div class="card-body">
                <table class="table table-sm mb-0" style="font-size:.82rem;">
                    <tr>
                        <td class="text-muted">PHP Version</td>
                        <td>{{ phpversion() }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Laravel</td>
                        <td>{{ app()->version() }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Database</td>
                        <td>SQLite</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Timezone</td>
                        <td>{{ config('app.timezone') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">App URL</td>
                        <td>{{ config('app.url') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
