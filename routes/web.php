<?php

use Livewire\Volt\Volt;

Volt::route('/', 'index');                          // Home
Volt::route('/users', 'users.index');               // User (list)                     
Volt::route('/users/create', 'users.create');       // User (create) 
Volt::route('/users/{user}/edit', 'users.edit');    // User (edit) 



