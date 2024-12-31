<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

class Spotlight
{
    public function search(Request $request)
    {
        // Filter users by name
        // Transform the result to be compliant with Spotlight contract
        return User::query()
            ->where('name', 'like', "%$request->search%")
            ->take(5)
            ->get()
            ->map(function (User $user) {
                return [
                    'avatar' => $user->avatar ?? '/empty-user.jpg',
                    'name' => $user->name,
                    'description' => $user->email,
                    'link' => "/users/{$user->id}/edit"
                ];
            });
    }
}
