<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RolesTableSeeder
 *
 * @author m0rf3o
 */
class RolesTableSeeder extends Seeder {
    function run() {
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

        $user1 = User::find(1);
        $user2 = User::find(79);

        $user1->attachRole($admin);
        $user2->attachRole($user);
    }
}
