<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Handles the viewing of a collection of items in a table.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\
     */
    public function list()
    {
        return view('admin.user.list', ['models' => User::with('roles')->get()]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function makeadmin(Request $request, User $user)
    {
        $currentUser = Auth::user();
        if ($currentUser !== null && $currentUser->name === 'Admin') {
            if (!$user->hasRole('admin')) {
                $user->attachRole('admin');
            }

            // Message to the user
            \Session::flash('status', sprintf(__('User %s is now an admin'), $user->name));
        }

        return redirect()->route('admin.users');
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function makeuser(Request $request, User $user)
    {
        $currentUser = Auth::user();
        if ($currentUser !== null && $currentUser->name === 'Admin') {
            $user->detachRoles($user->roles);

            $user->attachRole('user');

            // Message to the user
            \Session::flash('status', sprintf(__('User %s is now a user'), $user->name));
        }

        return redirect()->route('admin.users');
    }
}
