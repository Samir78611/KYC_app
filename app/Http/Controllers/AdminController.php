<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\AdminInvitation;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    // Show the form to invite an admin
    public function create()
    {
        return view('superadmin.create_admin');
    }

    // Store the admin details in the database
    public function store(Request $request)
    {
        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:Admin,Super Admin',
        ]);

        // Create the new admin in the database
        $admin= User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make('defaultpassword'), // Set a default password or send an invitation to set password
        ]);

         // Send the invitation email
      // Send the invitation email, passing name, email, and role
      Mail::to($admin->email)->send(new AdminInvitation($admin->name, $admin->email, $admin->role));


        return redirect()->route('admin.create')->with('success', 'Admin created successfully!');

        
        
    }


}
