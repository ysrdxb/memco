@extends('inventory.layout')
@section('title', 'Settings')
@section('content')

<div class="container-fluid">
    <!-- Header Banner -->
    <div class="header-dashboard-clean">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="header-icon-box-clean">
                <i class="ik ik-settings"></i>
            </div>
            <div>
                <h3 class="header-title-text-clean">System Settings</h3>
                <p class="header-sub-text-clean">Manage global system configurations and API keys</p>
            </div>
        </div>
        <div class="d-flex align-items-center" style="gap: 12px;"></div>
    </div>

    <div class="row">
        @include('include.message')
        <div class="col-md-8 mx-auto">
            <div class="table-card">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    
                    <h5 class="mb-4" style="color: var(--color-primary); font-weight: 700;">AI Assistant Configuration</h5>
                    
                    <div class="form-group">
                        <label for="groq_api_key" style="font-weight: 600;">Groq API Key</label>
                        <input type="password" class="form-control" id="groq_api_key" name="groq_api_key" 
                               value="{{ $settings['groq_api_key'] ?? '' }}" 
                               placeholder="gsk_..." 
                               style="border-radius: 8px; border: 1.5px solid var(--color-border); padding: 10px 14px;">
                        <small class="form-text text-muted mt-2">
                            Enter your free Groq API key (starts with <code>gsk_</code>) to power the AI Assistant. You can get one from <a href="https://console.groq.com/keys" target="_blank" style="color: var(--color-accent); text-decoration: underline;">console.groq.com</a>.
                        </small>
                    </div>

                    <div class="form-group mt-4 mb-0 text-right">
                        <button type="submit" class="btn btn-primary-memco" style="padding: 10px 24px; font-size: 0.95rem;">
                            <i class="ik ik-save mr-2"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
