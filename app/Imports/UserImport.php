<?php 

namespace App\Imports;

use App\Models\User;
use App\Models\Position;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class UserImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $existingUser = User::where('name', $row['name'])->where('employee_no', $row['code'])->first();

        if ($existingUser) {
            return null;
        }
        
        $position = Position::firstOrCreate([
            'name' => $row['position'], 
        ]);

        $positionId = $position->id; 

        return new User([
            'employee_no' => $row['code'],
            'name' => $row['name'],
            'position_id' => $positionId,
            'department_id' => 1,
            'email' => $row['code'].'@memcouae.com',
            'password' => Hash::make('111'),
            'phone' => 123456,
        ]);
    }
}
