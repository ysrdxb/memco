@extends('inventory.layout')
@section('title', 'My Projects')

@section('content')
@push('head')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .projects-wrapper {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
        }

        .header-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 18px;
            padding: 24px 28px;
            color: #ffffff;
            margin-bottom: 26px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .header-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(2, 132, 199, 0.35);
            margin-right: 18px;
            flex-shrink: 0;
        }

        .header-title-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 2px;
            letter-spacing: -0.4px;
        }

        .header-sub-text {
            color: #94a3b8;
            font-size: 0.88rem;
            margin-bottom: 0;
            font-weight: 500;
        }

        /* GRID LAYOUT */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
            gap: 22px;
            margin-bottom: 30px;
        }

        .project-card-item {
            background: #ffffff;
            border-radius: 18px;
            border: 1.5px solid #e2e8f0;
            padding: 24px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 200px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
        }

        .project-card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 36px -8px rgba(15, 23, 42, 0.12);
            border-color: #cbd5e1;
        }

        .project-card-item.is-active-project {
            border-color: #0284c7 !important;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 8px 24px -4px rgba(2, 132, 199, 0.2);
        }

        .active-project-tag {
            position: absolute;
            top: 18px;
            right: 18px;
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
        }

        .project-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .project-name {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .project-code {
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .btn-switch-action {
            width: 100%;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 10px;
            padding: 11px 16px;
            font-weight: 700;
            font-size: 0.88rem;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .btn-switch-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(2, 132, 199, 0.45);
        }

        .btn-current-active {
            width: 100%;
            background: #f1f5f9;
            color: #64748b !important;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 11px 16px;
            font-weight: 700;
            font-size: 0.88rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: default;
        }

        .empty-projects-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            padding: 45px 30px;
            text-align: center;
            box-shadow: 0 4px 18px rgba(0,0,0,0.03);
        }
    </style>
@endpush

<div class="container-fluid projects-wrapper">
    <!-- Header Banner -->
    <div class="header-banner d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="header-icon-box">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <h3 class="header-title-text">My Assigned Projects</h3>
                <p class="header-sub-text">Switch between your active site projects to manage inventory & material requests</p>
            </div>
        </div>

        <div>
            @include('include.backButtons')
        </div>
    </div>

    <!-- Projects Grid Container -->
    @if(count($projects) > 0)
        <div class="projects-grid">
            @foreach($projects as $project)
                @php
                    $isCurrent = (session('current_project_id') == $project->id);
                @endphp
                
                <div class="project-card-item {{ $isCurrent ? 'is-active-project' : '' }}">
                    <div>
                        @if($isCurrent)
                            <span class="active-project-tag">
                                <i class="fas fa-circle-check mr-1"></i> Active Project
                            </span>
                        @endif

                        <div class="project-icon-wrapper">
                            <i class="fas fa-city"></i>
                        </div>

                        <h5 class="project-name">{{ $project->name }}</h5>
                        <p class="project-code">
                            <i class="fas fa-hashtag mr-1"></i> Store ID: #{{ $project->id }}
                        </p>
                    </div>

                    <div class="mt-3">
                        <form action="{{ route('project.switch') }}" method="POST">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ encrypt($project->id) }}">
                            
                            @if($isCurrent)
                                <button type="button" class="btn-current-active" disabled>
                                    <i class="fas fa-check-circle mr-2 text-success"></i> Currently Active
                                </button>
                            @else
                                <button type="submit" class="btn-switch-action">
                                    <i class="fas fa-right-to-bracket mr-2"></i> Switch to Project
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-projects-card">
            <div class="mb-3">
                <i class="fas fa-folder-open text-muted" style="font-size: 3rem;"></i>
            </div>
            <h5 class="font-weight-bold text-dark mb-2">No Assigned Projects Found</h5>
            <p class="text-muted mb-0">You currently do not have any active project stores assigned to your account.</p>
        </div>
    @endif
</div>
@endsection
