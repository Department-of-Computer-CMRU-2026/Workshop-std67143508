<?php

use Livewire\Component;
use App\Models\Workshop;

new class extends Component
{
    public function with()
    {
        return [
            'workshops' => Workshop::withCount('registrations')->latest()->get(),
            'registeredWorkshopIds' => auth()->user()->registrations()->pluck('workshop_id')->toArray(),
            'totalRegistered' => auth()->user()->registrations()->count(),
        ];
    }
    
    public function register($workshopId)
    {
        $user = auth()->user();
        
        // Check 1: Maximum 3 workshops
        if ($user->registrations()->count() >= 3) {
            $this->addError('registration', "You can only register for a maximum of 3 workshops.");
            return;
        }
        
        $workshop = Workshop::withCount('registrations')->findOrFail($workshopId);
        
        // Check 2: Seat availability
        if ($workshop->registrations_count >= $workshop->total_seats) {
            $this->addError('registration', "This workshop is already full.");
            return;
        }
        
        // Check 3: Already registered (Handled by unique constraint, but good to check here)
        if ($user->registrations()->where('workshop_id', $workshopId)->exists()) {
            return;
        }
        
        $user->registrations()->create([
            'workshop_id' => $workshopId
        ]);
        
        session()->flash('success', "Successfully registered for {$workshop->title}");
    }
    
    public function unregister($workshopId)
    {
        $user = auth()->user();
        $user->registrations()->where('workshop_id', $workshopId)->delete();
        session()->flash('success', "Successfully unregistered from the workshop.");
    }
};
?>

<div>
    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 text-green-800 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif
    
    @error('registration')
        <div class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-800 dark:text-red-300">
            {{ $message }}
        </div>
    @enderror

    <div class="mb-6 flex justify-between items-center text-gray-700 dark:text-gray-300 bg-white dark:bg-neutral-800 p-4 rounded-lg border border-neutral-200 dark:border-neutral-700">
        <h2 class="text-xl font-bold">Upcoming Workshops</h2>
        <span class="px-4 py-2 font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full">
            Your Registrations: {{ $totalRegistered }} / 3
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($workshops as $workshop)
            @php
                $remaining = $workshop->total_seats - $workshop->registrations_count;
                $isRegistered = in_array($workshop->id, $registeredWorkshopIds);
            @endphp
            <div class="bg-white dark:bg-neutral-800 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 shadow p-6 border-l-4 {{ $remaining > 0 ? 'border-l-indigo-500' : 'border-l-gray-400' }}">
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-gray-100">{{ $workshop->title }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-1"><strong>Speaker:</strong> {{ $workshop->speaker }}</p>
                <p class="text-gray-600 dark:text-gray-400 mb-4"><strong>Location:</strong> {{ $workshop->location }}</p>
                
                <div class="flex justify-between items-center mt-4">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $remaining > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ max(0, $remaining) }} Seats Left
                    </span>
                    
                    @if($isRegistered)
                        <button wire:click="unregister({{ $workshop->id }})" class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 hover:bg-red-200 px-4 py-2 rounded transition shadow-sm font-medium">
                            Cancel
                        </button>
                    @elseif($remaining > 0 && $totalRegistered < 3)
                        <button wire:click="register({{ $workshop->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded shadow transition font-medium">
                            Register
                        </button>
                    @elseif($totalRegistered >= 3)
                        <button disabled class="bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-400 px-4 py-2 rounded cursor-not-allowed text-sm">
                            Limit Reached (3/3)
                        </button>
                    @else
                        <button disabled class="bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-400 px-4 py-2 rounded cursor-not-allowed font-medium">
                            Closed
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>