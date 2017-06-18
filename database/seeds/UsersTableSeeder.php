<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::create([
            'name' => 'Varga Gábor',
            'email' => 'gabor87@outlook.com',
            'password' => Hash::make('qweqwe') ,
        ]);
        
        \App\Task::create([
            'user_id' => $user->id,
            'title' => 'First',
            'description' => 'Some description.',
        ]);
        \App\Task::create([
            'user_id' => $user->id,
            'title' => 'Second',
            'description' => 'Some description.',
            'done' => \App\Task::STATUS_DONE
        ]);
    }
}
