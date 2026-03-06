<x-layouts::app :title="__('Home')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @if(auth()->user()->isAdmin())
            <livewire:admin-dashboard />
        @else
            <livewire:student-dashboard />
        @endif
    </div>
</x-layouts::app>
