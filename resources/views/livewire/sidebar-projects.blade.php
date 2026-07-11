<div class="flex flex-col gap-0.5 mt-0.5 relative pl-4">
    <div class="absolute top-0 bottom-0 left-5 border-l border-white/10"></div>
    @if(auth()->user()->currentWorkspace)
        <a href="/dashboard/new" wire:navigate class="group flex items-center gap-2.5 px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 {{ request()->is('dashboard/new') ? 'bg-white/10 text-white font-medium' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
            <span class="text-[14px] font-bold text-primary group-hover:scale-110 transition-transform shrink-0 w-4 text-center">+</span>
            <span class="text-[13px] font-semibold text-primary">Create New Project</span>
        </a>
        @foreach(auth()->user()->currentWorkspace->projects()->with('techStacks')->get() as $p)
            <a href="/dashboard/projects/{{ $p->slug }}" wire:navigate class="group flex items-center gap-2.5 px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 {{ $currentSlug === $p->slug ? 'bg-white/10 text-white font-medium' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                @if($p->logo_url)
                    <img src="{{ $p->logo_url }}" class="w-4 h-4 shrink-0 object-contain filter group-hover:brightness-125 transition-all" alt="{{ $p->title }}">
                @else
                    <img src="{{ asset('assets/icon-images/cube-icon.png') }}" class="w-4 h-4 shrink-0" alt="Cube Roadmap"> 
                @endif
                <span class="text-[13px] truncate">{{ $p->title }}</span>
            </a>
        @endforeach
    @endif
</div>
