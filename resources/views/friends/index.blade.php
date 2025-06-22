@extends('layouts.app')

@section('title', 'Vrienden')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Vrienden</h1>
        </div>

        <!-- Friend Search -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Vrienden Zoeken</h2>
            <form method="GET" action="{{ route('friends.search') }}" class="flex gap-2">
                <input
                    type="text"
                    name="q"
                    placeholder="Zoek op gebruikersnaam of e-mail..."
                    value="{{ request('q') }}"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Zoeken
                </button>
            </form>

            @if(isset($searchResults))
                <div class="mt-4">
                    <h3 class="font-medium mb-2">Zoekresultaten:</h3>
                    @forelse($searchResults as $user)
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <div class="flex items-center">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->username }}" class="w-8 h-8 rounded-full mr-3">
                                @else
                                    <div class="w-8 h-8 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                        {{ substr($user->username, 0, 1) }}
                                    </div>
                                @endif
                                <span>{{ $user->username }}</span>
                            </div>
                            <form method="POST" action="{{ route('friends.request') }}">
                                @csrf
                                <input type="hidden" name="friend_id" value="{{ $user->id }}">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                                    Uitnodigen
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-500">Geen gebruikers gevonden</p>
                    @endforelse
                </div>
            @endif
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Friend Requests -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-orange-600">Vriendschapsverzoeken</h2>
                @forelse($pendingRequests as $request)
                    <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                        <div class="flex items-center">
                            @if($request->requester->avatar)
                                <img src="{{ asset('storage/' . $request->requester->avatar) }}" alt="{{ $request->requester->username }}" class="w-10 h-10 rounded-full mr-3">
                            @else
                                <div class="w-10 h-10 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                    {{ substr($request->requester->username, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <span class="font-medium">{{ $request->requester->username }}</span>
                                <p class="text-sm text-gray-600">{{ $request->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <form method="POST" action="{{ route('friends.accept', $request->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                    Accepteren
                                </button>
                            </form>
                            <form method="POST" action="{{ route('friends.decline', $request->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                    Weigeren
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Geen vriendschapsverzoeken</p>
                @endforelse
            </div>

            <!-- Friends List -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-green-600">Mijn Vrienden</h2>
                @forelse($friends as $friend)
                    <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                        <div class="flex items-center">
                            @if($friend->avatar)
                                <img src="{{ asset('storage/' . $friend->avatar) }}" alt="{{ $friend->username }}" class="w-10 h-10 rounded-full mr-3">
                            @else
                                <div class="w-10 h-10 bg-gray-300 rounded-full mr-3 flex items-center justify-center">
                                    {{ substr($friend->username, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <span class="font-medium">{{ $friend->username }}</span>
                                <p class="text-sm text-gray-600">
                                    <span class="inline-block w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                    Online
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('profile.show', $friend) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                Profiel
                            </a>
                            <a href="{{ route('games.create', ['friend' => $friend->id]) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                Uitdagen
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Nog geen vrienden. Zoek hierboven naar gebruikers om uit te nodigen!</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
