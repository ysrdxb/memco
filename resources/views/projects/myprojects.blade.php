@extends('inventory.layout')
@section('title', 'My Projects')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-4">
                        <div class="page-header-title">
                            <i class="ik ik-box bg-green"></i>
                            <div class="d-inline">
                                <h5>My Projects</h5>
                                <span>Switch between your projects</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="text-right">
                            @include('include.backButtons')
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h2>Projects</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($projects as $project)
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $project->name }}</h5>
                                        <form action="{{ route('project.switch') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="project_id" value="{{ encrypt($project->id) }}">
                                            <button type="submit" class="btn btn-{{ session('current_project_id') == $project->id ? 'secondary' : 'danger' }}" {{ session('current_project_id') == $project->id ? 'disabled' : '' }}>
                                                {{ session('current_project_id') == $project->id ? 'Current Project' : 'Switch to this Project' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
