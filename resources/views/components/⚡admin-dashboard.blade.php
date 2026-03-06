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

<div class="space-y-8 animate-in fade-in duration-700">
    <style>
        .glass-card-lite {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .glass-card-lite:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
            border-color: rgba(59, 130, 246, 0.4);
        }
        .gradient-text {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-card {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3);
        }
    </style>

    @if($viewMode === 'list')
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-2">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    <span class="gradient-text">Manage Workshops</span>
                </h2>
                <p class="text-gray-500 mt-1">เครื่องมือจัดการกิจกรรมเวิร์กชอป พี่สอนน้อง</p>
            </div>
            <button wire:click="create" class="group flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Create New Workshop
            </button>
        </div>

        <!-- Quick Stats Area (Optional but looks premium) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card">
                <div class="text-blue-100 text-sm font-semibold uppercase tracking-wider mb-1">Total Activities</div>
                <div class="text-4xl font-black">{{ count($workshops) }}</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);">
                <div class="text-green-100 text-sm font-semibold uppercase tracking-wider mb-1">Total Registrations</div>
                <div class="text-4xl font-black">{{ $workshops->sum('registrations_count') }}</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.3);">
                <div class="text-purple-100 text-sm font-semibold uppercase tracking-wider mb-1">Total Seats</div>
                <div class="text-4xl font-black">{{ $workshops->sum('total_seats') }}</div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($workshops as $workshop)
                @php $remaining = $workshop->total_seats - $workshop->registrations_count; @endphp
                <div class="glass-card-lite rounded-2xl p-5 flex flex-col h-full relative overflow-hidden group">
                    <div class="mb-3">
                        <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-widest rounded-full {{ $remaining > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                            {{ $remaining > 0 ? $remaining . ' Seats Left' : 'Full' }}
                        </span>
                    </div>

                    <div class="flex flex-1 gap-4">
                        <!-- Left Content -->
                        <div class="flex-1">
                            <h3 class="text-base font-extrabold text-gray-900 mb-2 leading-tight group-hover:text-blue-600 transition-colors line-clamp-2">{{ $workshop->title }}</h3>
                            
                            <div class="space-y-2 text-[11px] text-gray-600 font-medium">
                                <div class="flex items-center gap-1.5">
                                    <svg class="size-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="truncate">P' {{ $workshop->speaker }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="size-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    <span class="truncate">{{ $workshop->location }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 font-bold {{ $remaining > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    <svg class="size-3.5 {{ $remaining > 0 ? 'text-emerald-500' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <span>{{ $workshop->registrations_count }} / {{ $workshop->total_seats }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Actions (Vertical) -->
                        <div class="flex flex-col gap-1 pl-4 border-l border-gray-100 flex-shrink-0">
                            <button wire:click="viewRegistrants({{ $workshop->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition flex flex-col items-center gap-0.5" title="View">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span class="text-[8px] font-black uppercase">View</span>
                            </button>
                            <button wire:click="edit({{ $workshop->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition flex flex-col items-center gap-0.5" title="Edit">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span class="text-[8px] font-black uppercase">Edit</span>
                            </button>
                            <button x-data x-on:click="
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: 'You want to delete this workshop!',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#4caf50',
                                    cancelButtonColor: '#9e9e9e',
                                    confirmButtonText: 'Yes',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $wire.delete({{ $workshop->id }})
                                    }
                                })
                            " class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition flex flex-col items-center gap-0.5" title="Delete">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span class="text-[8px] font-black uppercase">Del</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            @if(count($workshops) === 0)
                <div class="col-span-full py-20 text-center glass-card-lite rounded-[40px]">
                    <div class="mb-4 inline-flex p-6 bg-gray-50 rounded-full">
                        <svg class="size-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">No workshops created yet</h3>
                    <p class="text-gray-500 mt-2">Start by creating your first senior-to-junior activity!</p>
                </div>
            @endif
        </div>
        
    @elseif($viewMode === 'create' || $viewMode === 'edit')
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <button wire:click="backToList" class="p-4 bg-white hover:bg-gray-50 text-gray-600 rounded-2xl shadow-sm border border-gray-100 transition">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <h2 class="text-3xl font-black text-gray-900 leading-none">
                    {{ $viewMode === 'create' ? 'Create Workshop' : 'Edit Workshop' }}
                </h2>
            </div>
                       <div class="glass-card-lite rounded-[40px] p-8 md:p-12">
                <form 
                    x-data
                    x-on:submit.prevent="
                        if ('{{ $viewMode }}' === 'edit') {
                            Swal.fire({
                                title: 'ยืนยันการแก้ไข?',
                                text: 'คุณต้องการบันทึกการเปลี่ยนแปลงของเวิร์กชอปนี้ใช่หรือไม่?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#4caf50',
                                cancelButtonColor: '#9e9e9e',
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $wire.update();
                                }
                            })
                        } else {
                            $wire.store();
                        }
                    " 
                    class="space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                        <div class="col-span-2">
                            <label class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-blue-600 mb-4">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Workshop Title
                            </label>
                            <input type="text" wire:model="title" class="w-full px-6 py-4 bg-gray-50 border-gray-100 border rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-gray-900 placeholder:text-gray-300" placeholder="e.g. Intro to Modern Web Design">
                            @error('title') <span class="text-red-500 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="space-y-8">
                            <div>
                                <label class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-blue-600 mb-4">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Speaker Name
                                </label>
                                <input type="text" wire:model="speaker" class="w-full px-6 py-4 bg-gray-50 border-gray-100 border rounded-2xl focus:ring-4 focus:ring-blue-500/10 font-bold text-gray-700 placeholder:text-gray-300" placeholder="P' John Doe">
                                @error('speaker') <span class="text-red-500 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-blue-600 mb-4">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    Location
                                </label>
                                <input type="text" wire:model="location" class="w-full px-6 py-4 bg-gray-50 border-gray-100 border rounded-2xl focus:ring-4 focus:ring-blue-500/10 font-bold text-gray-700 placeholder:text-gray-300" placeholder="e.g. Lab 102">
                                @error('location') <span class="text-red-500 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="bg-blue-50/50 rounded-3xl p-8 border border-blue-100/50">
                            <label class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-blue-600 mb-6">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Total Participant Capacity
                            </label>
                            
                            <div class="relative group">
                                <input type="number" wire:model="total_seats" class="w-full pl-8 pr-20 py-6 bg-white border-blue-100 border-2 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-black text-3xl text-blue-600 transition-all outline-none">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-6 pointer-events-none">
                                    <span class="text-blue-300 font-black uppercase tracking-widest text-sm">Seats</span>
                                </div>
                            </div>
                            <p class="mt-4 text-[11px] text-blue-400 font-bold leading-relaxed">
                                หมายเหตุ: จำนวนที่นั่งจะมีผลต่อการคำนวณที่นั่งว่างแบบเรียลไทม์
                            </p>
                            @error('total_seats') <span class="text-red-500 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="pt-8 flex flex-col md:flex-row items-center justify-end gap-4 border-t border-gray-100">
                        <button type="button" wire:click="backToList" class="w-full md:w-auto px-8 py-4 bg-white border border-gray-200 text-gray-500 font-bold uppercase tracking-widest text-[10px] rounded-2xl hover:bg-gray-50 transition-all flex items-center justify-center gap-2 order-2 md:order-1">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Cancel
                        </button>
                        
                        <button type="submit" style="background: linear-gradient(135deg, #2563eb, #1e40af); color: white;" class="w-full md:w-auto px-12 py-4 font-black rounded-2xl hover:opacity-90 shadow-lg shadow-blue-500/30 active:scale-95 transition-all uppercase tracking-widest text-xs flex items-center justify-center gap-3 order-1 md:order-2">
                            <span>{{ $viewMode === 'create' ? 'Launch Activity' : 'Save Changes' }}</span>
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    @elseif($viewMode === 'registrants')
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center gap-6 mb-10">
                <button wire:click="backToList" class="p-5 bg-white hover:bg-gray-50 text-gray-700 rounded-3xl shadow-sm border border-gray-100 transition">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight leading-none mb-2">Registrants List</h2>
                    <p class="text-gray-500 font-bold italic">“ {{ $title }} ”</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10 text-center">
                <div class="glass-card-lite rounded-[32px] p-6">
                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Confirmed Students</div>
                    <div class="text-4xl font-black text-blue-600">{{ count($registrants) }}</div>
                </div>
                <div class="glass-card-lite rounded-[32px] p-6 border-blue-500/20">
                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Active Activity</div>
                    <div class="flex justify-center"><svg class="size-8 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></div>
                </div>
                <div class="glass-card-lite rounded-[32px] p-6">
                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Status</div>
                    <div class="font-black text-xs px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full inline-block">ONGOING</div>
                </div>
            </div>
            
            <div class="glass-card-lite rounded-[40px] overflow-hidden border border-gray-100 shadow-sm">
                @if(count($registrants) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-0">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 border-b border-gray-100">Student Identity</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 text-center border-b border-gray-100">Queue</th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 text-right border-b border-gray-100">Management</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($registrants as $index => $user)
                                    <tr class="group hover:bg-blue-50/30 transition-all duration-300">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-5">
                                                <div class="size-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-blue-100 ring-4 ring-white">
                                                    {{ substr($user['name'], 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-black text-gray-900 text-lg group-hover:text-blue-600 transition-colors">{{ $user['name'] }}</div>
                                                    <div class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">{{ $user['email'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <span class="font-black text-blue-100 text-4xl tracking-tighter italic opacity-50 group-hover:opacity-100 group-hover:text-blue-200 transition-all">#{{ sprintf('%02d', $index + 1) }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <button type="button"
                                                x-data
                                                x-on:click="
                                                    Swal.fire({
                                                        title: 'ยืนยันการลบ?',
                                                        text: `คุณต้องการเอานักศึกษา '{{ $user['name'] }}' ออกจากเวิร์กชอปนี้ใช่หรือไม่?`,
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#4caf50',
                                                        cancelButtonColor: '#9e9e9e',
                                                        confirmButtonText: 'Yes',
                                                        cancelButtonText: 'Cancel'
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            $wire.deleteRegistrant({{ $user['id'] }});
                                                        }
                                                    })
                                                "
                                                class="opacity-0 group-hover:opacity-100 px-6 py-2.5 bg-red-50 hover:bg-red-600 text-red-500 hover:text-white rounded-xl text-[10px] font-black tracking-[0.1em] uppercase transition-all border border-red-100/50 shadow-sm">
                                                Remove Student
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-20 px-8">
                        <div class="mb-4 inline-flex p-6 bg-red-50 rounded-full">
                            <svg class="size-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">ไม่มีผู้ลงทะเบียนกิจกรรม</h3>
                        <p class="text-gray-400 mt-2 font-medium italic">Wait for students to join this amazing workshop!</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>