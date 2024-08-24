<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Employee;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        return view('users');
    }

    /**
     * Show User List
     *
     * @param Request $request
     * @return mixed
     */

    public function getUserList(Request $request): mixed
    {
        $excludedRoleIds = Role::whereIn('name', ['Super Admin', 'Admin', 'Warehouse Incharge'])->pluck('id')->toArray();
        
        $data = User::whereDoesntHave('roles', function ($query) use ($excludedRoleIds) {
                $query->whereIn('id', $excludedRoleIds);
            })
            ->get();
        $hasManageUser = Auth::user()->can('manage_user');

        return Datatables::of($data)
            ->addColumn('serial_no', function ($data) {
                return $data->id;
            })
            ->addColumn('roles', function ($data) {
                $roles = $data->getRoleNames()->toArray();
                $badge = '';
                if ($roles) {
                    $badge = implode(' , ', $roles);
                }

                return $badge;
            })
            ->addColumn('permissions', function ($data) {
                $roles = $data->getAllPermissions();
                $badges = '';
                foreach ($roles as $key => $role) {
                    $badges .= '<span class="badge badge-dark m-1">' . $role->name . '</span>';
                }

                return $badges;
            })
            ->addColumn('action', function ($data) use ($hasManageUser) {
                $output = '';
                if ($data->name == 'Super Admin') {
                    return '';
                }
                $canEdit = Auth::user()->can('edit_user');
                $canDelete = Auth::user()->can('delete_user');
            
                if ($hasManageUser && ($canEdit || $canDelete)) {
                    $output = '<div class="table-actions">';
                    if ($canEdit) {
                        $output .= '<a href="' . url('user/' . $data->id) . '" ><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>';
                    }
                    if ($canDelete) {
                        $output .= '<a href="' . url('user/delete/' . $data->id) . '"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
                    }
                    $output .= '</div>';
                }

                return $output;
            })
            ->rawColumns(['roles', 'permissions', 'action'])
            ->make(true);
    }

    public function search(Request $request)
    {
        $term = $request->input('term');
    
        $users = Employee::whereNotNull('employee_no')
            ->where(function ($query) use ($term) {
                $query->where('employee_no', 'like', '%' . $term . '%')
                      ->orWhere('name', 'like', '%' . $term . '%');
            })
            ->get();
    
        $formattedUsers = $users->map(function ($user) {
            return ['id' => $user->id, 'text' => $user->employee_no . ' - ' . $user->name];
        });
    
        return response()->json($formattedUsers);
    }
     
    /**
     * User Create
     *
     * @return mixed
     */
    public function create(): mixed
    {
        try {
            $departments = Department::all();
            $roles = Role::where('name', 'Store Incharge')->pluck('name', 'id');

            return view('create-user', compact('roles','departments'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }   

    /**
     * Store User
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request): RedirectResponse
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string',
                'phone' => 'required|numeric',
                'email' => 'required|email',
                'password' => 'required|max:250',
                'role' => 'required',
            ]);
            // Store user information
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'password' => Hash::make($validatedData['password']),
            ]);
    
            if ($user) {
                // Assign new role to the user
                $user->syncRoles($validatedData['role']);
    
                return redirect('users')->with('success', 'New user created!');
            }
    
            return redirect('users')->with('error', 'Failed to create new user! Try again.');
        } catch (ValidationException $e) {
            // Redirect back with errors if validation fails
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            $bug = $e->getMessage();
    
            return redirect()->back()->with('error', $bug);
        }
    }    

    /**
     * Edit User
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $departments = Department::all();
            $user = User::with('roles', 'permissions')->find($id);
            $position = $user->position ? $user->position->first() : null;
            if ($user) {
                $user_role = $user->roles->first();
                $roles = Role::pluck('name', 'id');

                return view('user-edit', compact('user', 'user_role', 'roles', 'departments', 'position'));
            }

            return redirect('404');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update User
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        // update user info
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required | string ',
            'phone' => 'required | numeric',
            'email' => 'required | email',
            'role' => 'required',
            // 'department_id' => 'required|numeric',
            // 'position_id' => 'required|numeric',
            // 'employee_no' => 'nullable|numeric'
        ]);

        // check validation for password match
        if (isset($request->password)) {
            $validator = Validator::make($request->all(), [
                'password' => 'required | confirmed',
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try {
            if ($user = User::find($request->id)) {
                $payload = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    // 'department_id' => $request->department_id,
                    // 'position_id' => $request->position_id,
                    'employee_no' => $request->employee_no
                ];
                // update password if user input a new password
                if (isset($request->password) && $request->password) {
                    $payload['password'] = Hash::make($request->password);
                }

                $update = $user->update($payload);
                // sync user role
                $user->syncRoles($request->role);

                return redirect()->back()->with('success', 'User information updated succesfully!');
            }

            return redirect()->back()->with('error', 'Failed to update user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Delete User
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        if ($user = User::find($id)) {
            $user->delete();

            return redirect('users')->with('success', 'User removed!');
        }

        return redirect('users')->with('error', 'User not found');
    }

    public function import(Request $request)
    {
        
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');

        Excel::import(new UserImport, $file);

        $redirect = url('users');

        return response()->json([
            'message' => 'Users imported successfully!',
            'status' => 'success',
            'redirect' => $redirect
        ]);
    }     
}
