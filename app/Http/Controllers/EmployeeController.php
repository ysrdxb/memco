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
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        return view('employees');
    }

    /**
     * Show User List
     *
     * @param Request $request
     * @return mixed
     */

    public function getUserList(Request $request): mixed
    {
        
        $data = Employee::get();
        return Datatables::of($data)
            ->addColumn('id', function ($data) {
                return $data->id;
            })
            ->addColumn('name', function ($data) {
                return $data->name;
            })    
            ->addColumn('employee_no', function ($data) {
                return $data->employee_no;
            })                       
            ->addColumn('phone', function ($data) {
                return $data->phone;
            })            
            ->addColumn('email', function ($data) {
                return $data->email;
            })            
            ->addColumn('action', function ($data)  {
                $output = '';
               
            
                    $output = '<div class="table-actions">';
              
                        $output .= '<a href="' . url('employee/' . $data->id) . '" ><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>';
                    
                        // $output .= '<a href="' . url('employee/delete/' . $data->id) . '"><i class="ik ik-trash-2 f-16 text-red"></i></a>';
                    
                    $output .= '</div>';
                

                return $output;
            })
            ->rawColumns(['action'])
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

            return view('create-employee', compact('departments'));
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
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'required|exists:positions,id',
                'phone' => 'required|numeric',
                'email' => 'required|email',
                'employee_no' => 'required|numeric',
            ]);
            // Store user information
            $user = Employee::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'department_id' => $validatedData['department_id'],
                'position_id' => $validatedData['position_id'],
                'employee_no' => $request->employee_no
            ]);
    
            if ($user) {
                // Assign new role to the user    
                return redirect('employees')->with('success', 'New user created!');
            }
    
            return redirect('employees')->with('error', 'Failed to create new user! Try again.');
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
            $user = Employee::find($id);
            $position = $user->position ? $user->position->first() : null;
            if ($user) {
                return view('employee-edit', compact('user', 'departments', 'position'));
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
        if(Auth::user()->hasRole(['Admin', 'Super Admin']))
        {

            // update user info
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required | string ',
                'phone' => 'required | numeric',
                'email' => 'required | email',
                'department_id' => 'required|numeric',
                'position_id' => 'required|numeric',
                'employee_no' => 'nullable|numeric'
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
                if ($user = Employee::find($request->id)) {
                    $payload = [
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'department_id' => $request->department_id,
                        'position_id' => $request->position_id,
                        'employee_no' => $request->employee_no
                    ];

                    $update = $user->update($payload);

                    return redirect()->back()->with('success', 'User information updated succesfully!');
                }

                return redirect()->back()->with('error', 'Failed to update user! Try again.');
            } catch (\Exception $e) {
                $bug = $e->getMessage();

                return redirect()->back()->with('error', $bug);
            }
        } else {
            return redirect()->back()->with('error', 'You are not authorized to update information!');
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
        if ($user = Employee::find($id)) {
            $user->delete();

            return redirect('employees')->with('success', 'User removed!');
        }

        return redirect('employees')->with('error', 'User not found');
    }

    // public function import(Request $request)
    // {
        
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls,csv',
    //     ]);

    //     $file = $request->file('file');

    //     Excel::import(new UserImport, $file);

    //     $redirect = url('users');

    //     return response()->json([
    //         'message' => 'Users imported successfully!',
    //         'status' => 'success',
    //         'redirect' => $redirect
    //     ]);
    // }     
}
