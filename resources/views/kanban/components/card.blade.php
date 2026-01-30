<div class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-4 cursor-move border border-gray-200 group"
     draggable="true"
     ondragstart="handleDragStart(event, {{ $card->id }})"
     onclick="document.getElementById('cardModal{{ $card->id }}').showModal()">
    
    <!-- Priority Badge -->
    <div class="flex items-start justify-between mb-3">
        @if($card->priority === 'high')
            <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-700">Tinggi</span>
        @elseif($card->priority === 'medium')
            <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-yellow-100 text-yellow-700">Sedang</span>
        @else
            <span class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold bg-green-100 text-green-700">Rendah</span>
        @endif
        <span class="opacity-0 group-hover:opacity-100 transition text-gray-400">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
            </svg>
        </span>
    </div>

    <!-- Card Title -->
    <h4 class="font-semibold text-gray-900 text-sm line-clamp-2 mb-2">{{ $card->title }}</h4>

    <!-- Card Description -->
    @if($card->description)
        <p class="text-xs text-gray-600 line-clamp-2 mb-3">{{ $card->description }}</p>
    @endif

    <!-- Due Date -->
    @if($card->due_date)
        <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-3">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>{{ \Carbon\Carbon::parse($card->due_date)->format('d M') }}</span>
        </div>
    @endif

    <!-- Assigned Users -->
    @if($card->assignedUsers->count() > 0)
        <div class="flex items-center gap-1 mt-3">
            @foreach($card->assignedUsers->take(3) as $user)
                <div class="w-6 h-6 rounded-full bg-indigo-500 text-white text-xs flex items-center justify-center font-semibold hover:ring-2 hover:ring-indigo-300"
                     title="{{ $user->name }}">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endforeach
            @if($card->assignedUsers->count() > 3)
                <div class="w-6 h-6 rounded-full bg-gray-300 text-gray-700 text-xs flex items-center justify-center font-semibold">
                    +{{ $card->assignedUsers->count() - 3 }}
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Include Card Modals -->
@include('kanban.components.card.detail', ['card' => $card])
@include('kanban.components.card.edit', ['card' => $card])
@include('kanban.components.card.assign', ['card' => $card])
