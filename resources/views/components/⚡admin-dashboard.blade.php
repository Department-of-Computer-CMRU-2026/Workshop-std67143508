<?php

use Livewire\Component;
use App\Models\Workshop;
use Livewire\Attributes\Rule;

new class extends Component
{
    // List variables
    public $viewMode = 'list'; // list | create | edit | registrants
    
    // Form variables
    public $editingId = null;
    
    #[Rule('required|min:3')]
    public $title = '';
    
    #[Rule('required|min:3')]
    public $speaker = '';
    
    #[Rule('required|min:2')]
    public $location = '';
    
    #[Rule('required|integer|min:1')]
    public $total_seats = 10;
    
    // View Registrant variables
    public $viewingWorkshopId = null;
    public $registrants = [];
    
    public function with()
    {
        return [
            'workshops' => Workshop::withCount('registrations')->latest()->get(),
        ];
    }
    
    public function create()
    {
        $this->resetValidation();
        $this->reset(['title', 'speaker', 'location', 'total_seats', 'editingId']);
        $this->viewMode = 'create';
    }
    
    public function store()
    {
        $this->validate();
        
        Workshop::create([
            'title' => $this->title,
            'speaker' => $this->speaker,
            'location' => $this->location,
            'total_seats' => $this->total_seats,
        ]);
        
        $this->viewMode = 'list';
    }
    
    public function edit($id)
    {
        $this->resetValidation();
        $workshop = Workshop::findOrFail($id);
        $this->editingId = $workshop->id;
        $this->title = $workshop->title;
        $this->speaker = $workshop->speaker;
        $this->location = $workshop->location;
        $this->total_seats = $workshop->total_seats;
        
        $this->viewMode = 'edit';
    }
    
    public function update()
    {
        $this->validate();
        
        $workshop = Workshop::findOrFail($this->editingId);
        $workshop->update([
            'title' => $this->title,
            'speaker' => $this->speaker,
            'location' => $this->location,
            'total_seats' => $this->total_seats,
        ]);
        
        $this->viewMode = 'list';
    }
    
    public function delete($id)
    {
        Workshop::findOrFail($id)->delete();
    }
    
    public function viewRegistrants($id)
    {
        $workshop = Workshop::with('registrations.user')->findOrFail($id);
        $this->viewingWorkshopId = $id;
        $this->registrants = $workshop->registrations->map(fn($reg) => $reg->user)->toArray();
        $this->title = $workshop->title; // to show in header
        
        $this->viewMode = 'registrants';
    }
    
    public function backToList()
    {
        $this->viewMode = 'list';
    }
    
    public function deleteRegistrant($userId)
    {
        \App\Models\Registration::where('workshop_id', $this->viewingWorkshopId)
            ->where('user_id', $userId)
            ->delete();
        
        // Refresh the registrants list
        $workshop = Workshop::with('registrations.user')->findOrFail($this->viewingWorkshopId);
        $this->registrants = $workshop->registrations->map(fn($reg) => $reg->user)->toArray();
    }
};
?>

<div class="p-6 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm w-full">
    @if($viewMode === 'list')
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Manage Workshops</h2>
            <button wire:click="create" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Create Workshop
            </button>
        </div>
        
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-neutral-800 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Title</th>
                        <th scope="col" class="px-6 py-3">Speaker</th>
                        <th scope="col" class="px-6 py-3">Location</th>
                        <th scope="col" class="px-6 py-3 text-center">Seats</th>
                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workshops as $workshop)
                        <tr class="bg-white border-b dark:bg-neutral-900 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-800">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $workshop->title }}
                            </td>
                            <td class="px-6 py-4">{{ $workshop->speaker }}</td>
                            <td class="px-6 py-4">{{ $workshop->location }}</td>
                            <td class="px-6 py-4 text-center">
                                @php $remaining = $workshop->total_seats - $workshop->registrations_count; @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $remaining > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $workshop->registrations_count }} / {{ $workshop->total_seats }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <button wire:click="viewRegistrants({{ $workshop->id }})" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Registrants</button>
                                <button wire:click="edit({{ $workshop->id }})" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">Edit</button>
                                <button wire:click="delete({{ $workshop->id }})" wire:confirm="Are you sure you want to delete this workshop?" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                    @if(count($workshops) === 0)
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No workshops created yet.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
    @elseif($viewMode === 'create' || $viewMode === 'edit')
        <div>
            <div class="flex items-center mb-6">
                <button wire:click="backToList" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back
                </button>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $viewMode === 'create' ? 'Create New Workshop' : 'Edit Workshop' }}
                </h2>
            </div>
            
            <form wire:submit="{{ $viewMode === 'create' ? 'store' : 'update' }}" class="max-w-2xl bg-gray-50 dark:bg-neutral-800/50 p-6 rounded-lg border border-gray-100 dark:border-neutral-700/50 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                        <input type="text" wire:model="title" class="w-full rounded-lg border-gray-300 dark:bg-neutral-900 dark:border-neutral-600 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        @error('title') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Speaker Name</label>
                        <input type="text" wire:model="speaker" class="w-full rounded-lg border-gray-300 dark:bg-neutral-900 dark:border-neutral-600 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        @error('speaker') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location / Room</label>
                        <input type="text" wire:model="location" class="w-full rounded-lg border-gray-300 dark:bg-neutral-900 dark:border-neutral-600 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        @error('location') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Capacity (Seats)</label>
                        <input type="number" wire:model="total_seats" class="w-full md:w-1/2 rounded-lg border-gray-300 dark:bg-neutral-900 dark:border-neutral-600 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        @error('total_seats') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Total number of students that can register for this workshop.</p>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-gray-200 dark:border-neutral-700 flex items-center space-x-3">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 transition shadow-sm">
                        {{ $viewMode === 'create' ? 'Save Workshop' : 'Update changes' }}
                    </button>
                    <button type="button" wire:click="backToList" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 focus:ring-4 focus:ring-gray-100 transition shadow-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
        
    @elseif($viewMode === 'registrants')
        <div>
            <div class="flex items-center mb-6">
                <button wire:click="backToList" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back
                </button>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Registrants for "{{ $title }}"
                </h2>
            </div>
            
            <div class="bg-indigo-50 border-l-4 border-indigo-500 dark:bg-indigo-900/30 dark:border-indigo-400 p-4 rounded-r-lg mb-6">
                <p class="text-indigo-800 dark:text-indigo-200 font-medium flex items-center">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Total Registered: {{ count($registrants) }} students
                </p>
            </div>
            
            <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm border border-gray-200 dark:border-neutral-700 overflow-hidden">
                @if(count($registrants) > 0)
                    <ul class="divide-y divide-gray-200 dark:divide-neutral-700">
                        @foreach($registrants as $index => $user)
                            <li class="p-4 hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-800 dark:text-indigo-300 font-bold text-sm">
                                            {{ substr($user['name'], 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $user['name'] }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            {{ $user['email'] }}
                                        </p>
                                    </div>
                                    <div class="inline-flex items-center space-x-3">
                                        <span class="text-base font-semibold text-gray-900 dark:text-white">#{{ $index + 1 }}</span>
                                        <button 
                                            wire:click="deleteRegistrant({{ $user['id'] }})" 
                                            wire:confirm="ยืนยันการลบ? คุณต้องการเอานักศึกษา '{{ $user['name'] }}' ออกจากการลงทะเบียนนี้หรือไม่?"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-xs font-medium rounded-md border border-red-200 dark:border-red-800 transition">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-12 px-4 text-gray-500 dark:text-gray-400">
                        ไม่มีผู้ลงทะเบียนกิจกรรม
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>