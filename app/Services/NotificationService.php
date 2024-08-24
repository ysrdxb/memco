<?php

namespace App\Services;

use Pusher\Pusher;
use App\Models\User;
use App\Models\ProjectUser;
use Spatie\Permission\Models\Role;

class NotificationService
{
    protected $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true,
            ]
        );
    }

    public function notifyAdmins($message)
    {
        $roles = ['Super Admin', 'Admin', 'Warehouse Incharge'];

        $users = User::role($roles)->get();

        foreach($users as $user) {
            $this->pusher->trigger("user-{$user->id}-channel", 'timetable-updated', ['message' => $message]);
        }
    }

    public function notifyStore($message, $project_id)
    {
        $proejctUsers = ProjectUser::where('project_id', $project_id)->get();

        foreach($proejctUsers as $user) {
            $this->pusher->trigger("user-{$user->user_id}-channel", 'timetable-updated', ['message' => $message]);
        }
    }    
}
