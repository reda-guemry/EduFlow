<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test('un utulusateur peut se connecter avec des identifiants valides', function () {
    
    $user = User::factory()->create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com' , 
        'password' => bcrypt('password') , 
        'role' => 'student',
    ]);

    $reponse = $this -> postJson('/api/login' , [
        'email' => 'test@example.com',
        'password' => 'password',
    ]) ; 

    $reponse -> assertStatus(200) ; 

});

