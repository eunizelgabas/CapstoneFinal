<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(){

        $users = User::with('roles')->get();
        return inertia('User/Index', [
            'users' => $users
        ]);
    }

    public function create(){
        $services = Service::all();
        return inertia('User/Create', [
            'roles' => Role::all(),
            'permissions' => Permission::all(),
            'services' => $services
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'middlename' => 'nullable|string',
            'ext_name' => 'nullable|string',
            'gender' => 'required|string',
            'type' => 'required|string',
            'email' => 'required|email',
            'contact_no' => 'required',
            'password' => 'required|string',
            'role' => 'required',
        ]);

        $data['password'] = bcrypt($data['password']); // Hash the password

        unset($data['role']);
        $type = $data['type'];
        $role = $request->role;

        $user = User::create($data);

        $user->assignRole($role);

         if ($type === 'Doctor') {
            // Handle the "doctor" type here.
            $specialization = $request->input('specialization');
            $lic_no = $request->input('lic_no');
            $selectedServiceIds = $request->input('selectedServiceIds');

            // Create a new doctor associated with the user
            $doctor = new Doctor([
                'specialization' => $specialization,
                'lic_no' => $lic_no
            ]);

            // Save the doctor first to get an ID.
            $user->doctor()->save($doctor);

            // Attach selected services to the doctor
            $doctor->services()->attach($selectedServiceIds);
        }

        return redirect()->route('user.index');
    }

    public function edit(User $user){
        // $user = User::with('roles')->find($user->id);
        $roles = Role::all();
        $user->load('doctor', 'doctor.services', 'roles')->find($user->id);
        $services = Service::all();
        $doctor = Doctor::with('user', 'services')->get();
        return inertia('User/Edit', [
            'user' => $user,
            'roles' => $roles,
            'services' => $services,
            'doctor' => $doctor,
            'currentRole' => $user->roles->first()->name,
        ]);
    }

    public function update(Request $request, User $user){
        $data = $request->validate([
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'middlename' => 'nullable|string',
            'ext_name' => 'nullable|string',
            'gender' => 'required|string',
            'type' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'contact_no' => 'required',
            'password' => 'nullable|string',
            'role' => 'required',
        ]);

        $isDoctor = $user->hasRole('Doctor');

        if ($isDoctor && in_array($data['role'], ['Admin'])) {
            // User was previously a doctor, but now changing to "admin"
            // Delete the associated "doctor" data.
            $user->doctor->delete();
        }

        if (isset($data['password']) && $data['password'] !== null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']); // Remove the "password" field from the data array
        }



        $role = $request->role;
        $type = $data['type'];

        $user->update($data);

        $user->syncRoles([$data['role']]);

        $currentRole = $user->getRoleNames()->first();
        $type = $data['type'];

        if ($currentRole === 'Admin' && ($type === 'Doctor')) {
            // If the user is a admin and the new type is  'doctor',
            // update the user's role to the specified role.
            $user->syncRoles([$type]);
        } else {
            // Handle other type and role changes (same as before).
            $user->syncRoles([$data['role']]);
        }
        // dd($currentRole, $type);

        if ($data['type'] === 'Doctor') {
            $specialization = $request->input('specialization');
            $lic_no = $request->input('lic_no');
            $selectedServiceIds = $request->input('selectedServiceIds');

            // Fetch the doctor record associated with the user (if it exists)
            $doctor = $user->doctor;

            // Update the doctor's data
            if ($doctor) {
                $doctor->specialization = $specialization;
                $doctor->lic_no = $lic_no;
                $doctor->save();
            } else {
                // If the doctor record doesn't exist, create a new one
                $doctor = new Doctor([
                    'specialization' => $specialization,
                    'lic_no' => $lic_no
            ]);
                $user->doctor()->save($doctor);
            }

            // Sync selected services
            $doctor->services()->sync($selectedServiceIds);
        } elseif ($data['type'] !== 'Doctor') {
            // If the user's type is not 'doctor,' you can delete the doctor record
            $user->doctor()->delete();
        }


        return redirect()->route('user.index');
    }

    public function destroy(User $user) {
        $user->delete();

        return back();
    }
}
