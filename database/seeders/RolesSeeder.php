<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin     =   Role::create(['name' =>'Admin']);
        // $role_patient   =   Role::create(['name' =>'patient']);
        $role_doctor    =   Role::create(['name' =>'Doctor']);

        $manage_medicine = Permission::create(['name' => 'manage-medicine']);
        $manage_patient = Permission::create(['name' => 'manage-patient']);
        $manage_service = Permission::create(['name' => 'manage-service']);
        $manage_doctors = Permission::create(['name' => 'manage-doctors']);
        $manage_category = Permission::create(['name' => 'manage-category']);
        $manage_type = Permission::create(['name' => 'manage-type']);
        $manage_dispense = Permission::create(['name' => 'manage-dispense']);
        $manage_purchase = Permission::create(['name' => 'manage-purchase']);
        $manage_inventory = Permission::create(['name' => 'manage-inventory']);
        $manage_appointment = Permission::create(['name' => 'manage-appointment']);
        $manage_user =  Permission::create(['name' => 'manage-user']);
        $manage_healthForm =  Permission::create(['name' => 'manage-healthForm']);
        $add_appointment =  Permission::create(['name' => 'add-appointment']);
        $edit_appointment =  Permission::create(['name' => 'edit-appointment']);
        $show_appointment =  Permission::create(['name' => 'show-appointment']);
        $delete_appointment =  Permission::create(['name' => 'delete-appointment']);


        $permission_admin =  [ $manage_medicine, $manage_patient,$manage_service,$manage_doctors,
                                $manage_appointment, $manage_category, $manage_type, $manage_dispense,
                                $manage_purchase, $manage_inventory, $add_appointment, $manage_user,
                                $edit_appointment, $show_appointment, $delete_appointment,
                                $manage_healthForm ];

        $permission_doctor =  [$add_appointment,  $manage_appointment, $manage_patient,$edit_appointment,
                                 $show_appointment, $delete_appointment,
                                 $manage_healthForm ];

        $role_admin->syncPermissions($permission_admin);
        $role_doctor->syncPermissions($permission_doctor);
        // $role_patient->givePermissionTo($add_appointment, $edit_appointment, $delete_appointment);

        User::find(1)->assignRole($role_admin);
        User::find(2)->assignRole($role_doctor);
        User::find(3)->assignRole($role_doctor);
    }
}
