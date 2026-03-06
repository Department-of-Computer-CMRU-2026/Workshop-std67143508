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

<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-1000">
    <style>
        .glass-card-lite {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card-lite:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(30, 58, 138, 0.15);
            border-color: rgba(59, 130, 246, 0.4);
        }
        .gradient-text-blue {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-card-premium {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            border-radius: 30px;
            padding: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(37, 99, 235, 0.4);
        }
        .stat-card-premium::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
    </style>

    @if (session()->has('success'))
        <div class="p-5 rounded-2xl bg-green-50 text-green-700 border border-green-100 flex items-center gap-3 animate-in slide-in-from-top-4 duration-500">
            <svg class="size-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    
    @error('registration')
        <div class="p-5 rounded-2xl bg-red-50 text-red-700 border border-red-100 flex items-center gap-3 animate-in slide-in-from-top-4 duration-500">
            <svg class="size-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span class="font-bold">{{ $message }}</span>
        </div>
    @enderror

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="flex-1">
            <h1 class="text-4xl font-black tracking-tight text-gray-900 leading-none">
                <span class="gradient-text-blue">Upcoming Activities</span>
            </h1>
            <p class="text-gray-500 mt-3 font-medium">ค้นหาและลงทะเบียนกิจกรรมเวิร์กชอปที่คุณสนใจ</p>
        </div>
        
        <div class="stat-card-premium min-w-[280px]">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <div class="text-blue-100 text-xs font-black uppercase tracking-widest mb-1 opacity-80">My Registration</div>
                    <div class="text-4xl font-black">{{ $totalRegistered }} <span class="text-xl opacity-60">/ 3</span></div>
                </div>
                <div class="p-3 bg-white/20 rounded-2xl">
                    <svg class="size-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="mt-4 w-full bg-white/20 rounded-full h-2 relative overflow-hidden z-10">
                <div class="h-full bg-white transition-all duration-700" style="width: {{ ($totalRegistered / 3) * 100 }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pt-4">
        @foreach($workshops as $workshop)
            @php
                $remaining = $workshop->total_seats - $workshop->registrations_count;
                $isRegistered = in_array($workshop->id, $registeredWorkshopIds);
            @endphp
            <div class="glass-card-lite rounded-[40px] p-8 flex flex-col relative overflow-hidden group">
                <!-- Status Badge -->
                <div class="mb-5 flex justify-between items-center">
                    <span class="px-4 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-full shadow-sm 
                        {{ $remaining > 0 ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                        {{ $remaining > 0 ? $remaining . ' Slots Available' : 'Full Capacity' }}
                    </span>
                    @if($isRegistered)
                        <span class="flex items-center gap-1.5 text-blue-600">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="text-[10px] font-black uppercase">Registered</span>
                        </span>
                    @endif
                </div>

                <h3 class="text-2xl font-black text-gray-900 mb-2 leading-tight group-hover:text-blue-600 transition-colors">{{ $workshop->title }}</h3>
                
                <div class="space-y-4 my-6 flex-1">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-gray-50 rounded-xl text-gray-400">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-gray-300 uppercase leading-none mb-1">Speaker</div>
                            <div class="text-sm font-bold text-gray-700">{{ $workshop->speaker }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-gray-50 rounded-xl text-gray-400">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-gray-300 uppercase leading-none mb-1">Location</div>
                            <div class="text-sm font-bold text-gray-700">{{ $workshop->location }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    @if($isRegistered)
                        <button 
                            type="button"
                            x-data
                            x-on:click="
                                Swal.fire({
                                    title: 'ยืนยันการยกเลิก?',
                                    text: 'คุณต้องการยกเลิกการลงทะเบียนกิจกรรมนี้ใช่หรือไม่?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#ef4444',
                                    cancelButtonColor: '#9e9e9e',
                                    confirmButtonText: 'Yes',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $wire.unregister({{ $workshop->id }})
                                    }
                                })
                            "
                            class="w-full py-4 bg-white border-2 border-red-50 text-red-500 font-black rounded-2xl hover:bg-red-600 hover:text-white hover:border-red-600 transition-all shadow-sm">
                            UNREGISTER
                        </button>
                    @elseif($remaining > 0 && $totalRegistered < 3)
                        <button 
                            type="button"
                            x-data
                            x-on:click="
                                Swal.fire({
                                    title: 'ต้องการ Join Activity จริงๆ หรือไม่?',
                                    text: 'คุณต้องการลงทะเบียนเข้าร่วมกิจกรรม {{ $workshop->title }} ใช่หรือไม่?',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#4caf50',
                                    cancelButtonColor: '#9e9e9e',
                                    confirmButtonText: 'Yes',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $wire.register({{ $workshop->id }})
                                    }
                                })
                            "
                            class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 uppercase tracking-widest">
                            Join Activity
                        </button>
                    @elseif($totalRegistered >= 3)
                        <div class="w-full py-4 bg-gray-50 text-gray-400 font-black rounded-2xl text-center border border-gray-100 cursor-not-allowed text-xs">
                            REGISTRATION LIMIT REACHED
                        </div>
                    @else
                        <div class="w-full py-4 bg-gray-50 text-gray-400 font-black rounded-2xl text-center border border-gray-100 cursor-not-allowed text-xs uppercase">
                            Activity Full
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>