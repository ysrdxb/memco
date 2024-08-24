<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class SwitchProject
{
    /**
     * Get the current project name.
     *
     * @return string|null
     */
    public function getCurrentProjectName()
    {
        if (session('current_project_id')) {
            $project = Project::find(session('current_project_id'));
            return $project ? $project->name : null;
        } else {
            $user = Auth::user();
            $project = $user->projects()->find($user->current_project_id) ?? $user->projects()->first();
            return $project ? $project->name : null;
        }
    }

    /**
     * Switch to another project.
     *
     * @param int $project_id
     * @return bool
     */
    public function switchToProject($project_id)
    {
        $user = Auth::user();
        
        // Check if the project belongs to the current user
        if ($user->projects()->where('project_id', $project_id)->exists()) {
            // Update session and user current project
            session(['current_project_id' => $project_id]);
            $user->update(['current_project_id' => $project_id]);
            return true;
        }

        return false;
    }

    /**
     * Get the current project ID.
     *
     * @return int|null
     */
    public function getCurrentProjectId()
    {
        if (session('current_project_id')) {
            return session('current_project_id');
        } else {
            $user = Auth::user();
            $project_id = $user->current_project_id ?? $user->projects()->first()->id ?? null;
            return $project_id;
        }
    }

    /**
     * Get the list of projects available to the user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserProjects()
    {
        return Auth::user()->projects()->get();
    }
    
    /**
     * Get the list of projects available to the user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserCurrentProject()
    {
        $user = Auth::user();
        $project = $user->projects()->where('project_id', $user->current_project_id)->first();
        return $project;
    }
    
}