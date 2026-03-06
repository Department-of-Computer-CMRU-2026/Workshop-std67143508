<?php

use Livewire\Component;
use App\Models\Workshop;

new class extends Component
{
    public function unregister($workshopId)
    {
        $user = auth()->user();
        $user->registrations()->where('workshop_id', $workshopId)->delete();
        session()->flash('success', "Successfully unregistered from the workshop.");
    }

    public function with()
    {
        return [
            'registrations' => auth()->user()->registrations()->with('workshop')->latest()->get(),
        ];
    }
}; ?>

<div class="p-6 space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">My Registrations</h1>
            <p class="text-gray-500 mt-1 font-medium text-sm text-balance leading-relaxed">View and manage the workshops you have joined.</p>
        </div>
        
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Explore More
        </a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-500">
            <svg class="size-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($registrations as $registration)
            @php $workshop = $registration->workshop; @endphp
            <div class="glass-card-lite rounded-3xl p-6 border border-gray-100 flex flex-col relative group overflow-hidden bg-white/50 backdrop-blur-sm shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-400">
                <div class="mb-4">
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-wider rounded-full border border-blue-100">
                        Joined
                    </span>
                </div>

                <h3 class="text-xl font-black text-gray-900 mb-4 leading-tight line-clamp-2">{{ $workshop->title }}</h3>

                <div class="space-y-3 mb-6 flex-1 text-sm">
                    <div class="flex items-center gap-3 text-gray-600">
                        <div class="size-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <span class="font-bold truncate text-gray-700">{{ $workshop->speaker }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                        <div class="size-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        </div>
                        <span class="font-bold truncate text-gray-700">{{ $workshop->location }}</span>
                    </div>
                </div>

                <button wire:click="unregister({{ $workshop->id }})" wire:confirm="Are you sure you want to cancel your registration for this workshop?" class="w-full py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-600 hover:text-white transition-all text-sm uppercase tracking-widest border border-red-100">
                    Cancel Registration
                </button>
            </div>
        @empty
            <div class="col-span-full py-16 text-center border-2 border-dashed border-gray-100 rounded-[40px] bg-gray-50/50 group">
                <div class="size-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm group-hover:scale-110 transition-transform text-gray-300">
                    <svg class="size-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h3 class="text-xl font-black text-gray-500 mb-1 leading-tight">No registrations found</h3>
                <p class="text-gray-400 text-sm font-medium mb-6 leading-relaxed">You haven't registered for any activities yet.</p>
                <a href="{{ route('dashboard') }}" wire:navigate class="px-6 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                    Browse Workshops
                </a>
            </div>
        @endforelse
    </div>
</div>
