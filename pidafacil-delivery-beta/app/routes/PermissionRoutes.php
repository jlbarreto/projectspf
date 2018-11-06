<?php

Route::get('/start', function()
{
    $admin = new Role();
    $admin->name = 'Admin';
    $admin->save();
  
    $user = new Role();
    $user->name = 'User';
    $user->save();
  
    $read = new Permission();
    $read->name = 'can_read';
    $read->display_name = 'Can Read Data';
    $read->save();
  
    $edit = new Permission();
    $edit->name = 'can_edit';
    $edit->display_name = 'Can Edit Data';
    $edit->save();
  
    $user->attachPermission($read);
    $admin->attachPermission($read);
    $admin->attachPermission($edit);
 
    $adminRole = DB::table('roles')->where('name', '=', 'Admin')->pluck('id');
    $userRole = DB::table('roles')->where('name', '=', 'User')->pluck('id');
    // print_r($userRole);
    // die();
  
    $user1 = User::where('username','=','imron02')->first();
    $user1->roles()->attach($adminRole);
    $user2 = User::where('username','=','asih')->first();
    $user2->roles()->attach($userRole);
    $user3 = User::where('username','=','sarah')->first();
    $user3->roles()->attach($userRole);
    return 'Woohoo!';
});