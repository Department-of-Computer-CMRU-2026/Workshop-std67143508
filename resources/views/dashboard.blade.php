<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @if(auth()->user()->email === 'admin@example.com')
            <livewire:admin-dashboard />
        @else
            <livewire:student-dashboard />
        @endif
    </div>
</x-layouts::app>
