@props(['clientList'])

<div class="card bg-base-100 shadow-xl">
    <div class="card-body">
        @if($clientList->isEmpty())
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <p class="text-gray-600">Aucun client trouvé</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            @foreach($clientList->headers as $header)
                                <th>{{ $header->label }}</th>
                            @endforeach
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientList->items as $item)
                            <tr>
                                <td>{{ $item->email() }}</td>
                                <td>{{ $item->nom() }}</td>
                                <td>{{ $item->prenom() }}</td>
                                <td>
                                    <span class="badge {{ $item->statusClass() }}">
                                        {{ $item->status() }}
                                    </span>
                                </td>
                                <td>{{ $item->createdAt() }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        @foreach($item->actions()->actions as $action)
                                            <a href="{{ $action->url }}" class="btn btn-sm btn-outline">
                                                {{ $action->label }}
                                            </a>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="flex justify-between items-center mt-4">
                <p class="text-sm text-gray-600">
                    {{ $clientList->count() }} client(s) trouvé(s)
                </p>
            </div>
        @endif
    </div>
</div>