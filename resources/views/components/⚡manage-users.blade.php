<?php

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $search = '';
    public $editingUserId = null;
    public $newRole = 'student';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function editRole($id)
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $id;
        $this->newRole = $user->role;
    }

    public function updateRole()
    {
        $user = User::findOrFail($this->editingUserId);
        $user->update(['role' => $this->newRole]);
        $this->editingUserId = null;
        
        session()->flash('message', 'User role updated successfully.');
    }

    public function deleteUser($id)
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('message', 'User deleted successfully.');
    }

    public function cancelEdit()
    {
        $this->editingUserId = null;
    }

    public function with()
    {
        return [
            'users' => User::withCount('registrations')
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Manage Users</h1>
            <p class="text-sm text-gray-500 font-medium">Manage user identities and platform permissions.</p>
        </div>
        
        <div class="relative w-full md:w-96 group">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="size-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input 
                wire:model.live.debounce.300ms="search"
                type="text" 
                placeholder="Search by name or email..." 
                class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-gray-100 border text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none"
            >
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm text-emerald-700 bg-emerald-50 rounded-xl border border-emerald-100">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-50 rounded-xl border border-red-100">
            {{ session('error') }}
        </div>
    @endif

    <div class="glass-card-lite rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-0">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 border-b border-gray-100">#</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 border-b border-gray-100">User</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 border-b border-gray-100">Role</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 border-b border-gray-100 text-center">Registrations</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 border-b border-gray-100">Joined</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-wider text-gray-400 border-b border-gray-100 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4 text-sm text-gray-400 font-medium whitespace-nowrap">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm ring-2 ring-white">
                                        {{ $user->initials() }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($editingUserId === $user->id)
                                    <div class="flex items-center gap-2">
                                        <select wire:model="newRole" class="text-xs font-bold bg-white border border-gray-200 rounded-lg py-1 px-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none">
                                            <option value="student">Student</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                        <button type="button"
                                            x-data
                                            x-on:click="
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: 'You want to change user role!',
                                                    icon: 'info',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#4caf50',
                                                    cancelButtonColor: '#9e9e9e',
                                                    confirmButtonText: 'Yes',
                                                    cancelButtonText: 'Cancel'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.updateRole();
                                                    }
                                                })
                                            "
                                            class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Save">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                        <button wire:click="cancelEdit" class="p-1.5 text-gray-400 hover:bg-gray-50 rounded-lg transition" title="Cancel">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $user->role }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900 bg-gray-50 px-2 py-1 rounded-lg">{{ $user->registrations_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="editRole({{ $user->id }})" class="p-2 text-indigo-500 hover:bg-indigo-50 rounded-xl transition flex items-center gap-1">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span class="text-[10px] font-black uppercase">Role</span>
                                    </button>
                                    <button type="button"
                                        x-data
                                        x-on:click="
                                            Swal.fire({
                                                title: 'Are you sure?',
                                                text: 'You want to delete this user! This will also remove all their workshop registrations.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#4caf50',
                                                cancelButtonColor: '#9e9e9e',
                                                confirmButtonText: 'Yes',
                                                cancelButtonText: 'Cancel'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.deleteUser({{ $user->id }});
                                                }
                                            })
                                        "
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition flex items-center gap-1">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span class="text-[10px] font-black uppercase">Del</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                No users found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
