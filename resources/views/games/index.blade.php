@extends('layouts.app')

@section('title', 'Mijn Spellen')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Mijn Spellen</h1>
            <a href="{{ route('games.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Nieuw Spel Starten
            </a>
        </div>

        <div class="grid gap-6">
            <!-- Active Games -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-green-600">Actieve Spellen</h2>
                @forelse($activeGames as $game)
                    <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:mb-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold">
                                    {{ $game->player1->username }} vs {{ $game->player2->username }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Gestart: {{ $game->started_at ? $game->started_at->format('d-m-Y H:i') : $game->created_at->format('d-m-Y H:i') }}
                                </p>
                                @php
                                    $isUserTurn = $game->isUserTurn(Auth::id());
                                @endphp
                                <p class="text-sm {{ $isUserTurn ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                                    {{ $isUserTurn ? '🎯 Jouw beurt!' : '⏳ Wachten op tegenstander' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Jouw beurten: {{ $game->getUserTurnCount(Auth::id()) }}/6
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                    Actief
                                </span>
                                <a href="{{ route('games.show', $game) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                    Spelen
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Geen actieve spellen</p>
                @endforelse
            </div>

            <!-- Pending Games -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-yellow-600">Uitnodigingen</h2>
                @forelse($pendingGames as $game)
                    <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:mb-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold">
                                    {{ $game->player1->username }} vs {{ $game->player2->username }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Uitgenodigd: {{ $game->created_at->format('d-m-Y H:i') }}
                                </p>
                                @if($game->player1_id === Auth::id())
                                    <p class="text-sm text-blue-600">Wachten op {{ $game->player2->username }}</p>
                                @else
                                    <p class="text-sm text-orange-600">Uitnodiging van {{ $game->player1->username }}</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">
                                    Wachtend
                                </span>
                                <a href="{{ route('games.show', $game) }}"
                                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                    Bekijken
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Geen openstaande uitnodigingen</p>
                @endforelse
            </div>

            <!-- Finished Games -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-600">Afgeronde Spellen</h2>
                @forelse($finishedGames as $game)
                    <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:mb-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold">
                                    {{ $game->player1->username }} vs {{ $game->player2->username }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Afgerond: {{ $game->ended_at->format('d-m-Y H:i') }}
                                </p>
                                @if($game->winner_id)
                                    <p class="text-sm {{ $game->winner_id === Auth::id() ? 'text-green-600' : 'text-red-600' }}">
                                        Winnaar: {{ $game->winner->username }}
                                        {{ $game->winner_id === Auth::id() ? '🏆' : '' }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-600">Geen winnaar</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">
                                    Afgerond
                                </span>
                                <a href="{{ route('games.show', $game) }}"
                                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                    Bekijken
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Nog geen afgeronde spellen</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
