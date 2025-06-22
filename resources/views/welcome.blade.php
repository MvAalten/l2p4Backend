@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Welkom bij Woordspel!</h1>
            <p class="text-lg text-gray-600">Daag je vrienden uit in dit leuke woordspel!</p>

            @auth
                <div class="mt-6 space-x-4">
                    <a href="{{ route('games.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Nieuw Spel Starten
                    </a>
                    <a href="{{ route('games.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                        Mijn Spellen
                    </a>
                </div>
            @else
                <div class="mt-6 space-x-4">
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Registreren
                    </a>
                    <a href="{{ route('login') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                        Inloggen
                    </a>
                </div>
            @endauth
        </div>

        <!-- Leaderboards -->
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Top Spelers Vandaag</h2>
                @forelse($leaderboardToday as $index => $player)
                    <div class="flex items-center justify-between py-2 {{ $index === 0 ? 'text-yellow-600 font-bold' : '' }}">
                        <span>{{ $index + 1 }}. {{ $player->username }}</span>
                        <span>{{ $player->won_games_count }} wins</span>
                    </div>
                @empty
                    <p class="text-gray-500">Nog geen spelers vandaag</p>
                @endforelse
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Top Spelers Deze Week</h2>
                @forelse($leaderboardWeek as $index => $player)
                    <div class="flex items-center justify-between py-2 {{ $index === 0 ? 'text-yellow-600 font-bold' : '' }}">
                        <span>{{ $index + 1 }}. {{ $player->username }}</span>
                        <span>{{ $player->won_games_count }} wins</span>
                    </div>
                @empty
                    <p class="text-gray-500">Nog geen spelers deze week</p>
                @endforelse
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Alle Tijd Toppers</h2>
                @forelse($leaderboardAllTime as $index => $player)
                    <div class="flex items-center justify-between py-2 {{ $index === 0 ? 'text-yellow-600 font-bold' : '' }}">
                        <span>{{ $index + 1 }}. {{ $player->username }}</span>
                        <span>{{ $player->won_games_count }} wins</span>
                    </div>
                @empty
                    <p class="text-gray-500">Nog geen spelers</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
