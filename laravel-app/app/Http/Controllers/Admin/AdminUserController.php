<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::withCount(['predictions', 'savedProperties', 'priceAlerts']);

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user = User::findOrFail($id);

        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot demote your own administrator account.');
        }

        $user->role = $request->role;
        $user->save();

        return back()->with('success', "Role for {$user->name} updated to {$user->role}.");
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $user->status = ($user->status === 'active') ? 'blocked' : 'active';
        $user->save();

        $action = $user->status === 'active' ? 'activated' : 'suspended';
        return back()->with('success', "User account for {$user->name} has been {$action}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', "User {$user->name} deleted successfully.");
    }
}
