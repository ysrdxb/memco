<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password' ,'department_id', 'position_id', 'employee_no', 'otp', 'current_project_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function project()
    {
        return $this->hasManyThrough(Project::class, ProjectUser::class, 'user_id', 'id', 'id', 'project_id');
    }    

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_users', 'user_id', 'project_id');
    }     

    public function stores()
    {
        return $this->hasManyThrough(Project::class, ProjectUser::class, 'user_id', 'id', 'id', 'project_id');
    }
    
    public function warehouses()
    {
        return $this->hasManyThrough(Warehouse::class, WarehouseUser::class, 'user_id', 'id', 'id', 'warehouse_id');
    }    
     
    public function warehouse()
    {
        return $this->hasManyThrough(Warehouse::class, WarehouseUser::class, 'user_id', 'id', 'id', 'warehouse_id');
    }
    
    public function get_roles()
    {
        $roles = [];
        foreach ($this->getRoleNames() as $key => $role) {
            $roles[$key] = $role;
        }

        return $roles;
    }

    public function routeNotificationForDatabase()
    {
        return $this->id; // Assuming the user ID is stored in a column named 'id'
    }  
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }    
}
