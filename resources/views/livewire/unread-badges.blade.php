<span>
@if($type === 'msg-desktop')
    @if($count > 0)
    <span class="nav-badge" style="font-size:9px;min-width:16px;height:16px;">
        {{ $count > 99 ? '99+' : $count }}
    </span>
    @endif

@elseif($type === 'msg-mobile')
    @if($count > 0)
    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-black rounded-full min-w-[16px] h-4 flex items-center justify-center px-1">
        {{ $count > 99 ? '99+' : $count }}
    </span>
    @endif

@elseif($type === 'avatar-dot')
    @if($count > 0)
    <span class="absolute -top-1 -right-1 bg-red-600 text-xs px-1.5 rounded-full">{{ $count }}</span>
    @endif
@endif
</span>