<?php

use Livewire\Component;
use App\Models\Workshop;

new class extends Component
{
    public function with()
    {
        return [
            'workshops' => Workshop::withCount('registrations')->get(),
            'registeredWorkshopIds' => auth()->user()->registrations()->pluck('workshop_id')->toArray(),
        ];
    }
};
?>

<div>
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
                        <button disabled class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 px-4 py-2 rounded cursor-not-allowed">
                            Registered
                        </button>
                    @elseif($remaining > 0)
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow transition">
                            Register
                        </button>
                    @else
                        <button disabled class="bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-400 px-4 py-2 rounded cursor-not-allowed">
                            Closed
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>