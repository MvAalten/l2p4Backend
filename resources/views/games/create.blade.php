@extends('layouts.app')

@section('title', 'Nieuw Spel')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-6">Nieuw Spel Starten</h1>

            <form method="POST" action="{{ route('games.store') }}">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Kies je tegenstander
                    </label>

                    <div class="space-y-3">
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="opponent_type" value="random" class="mr-3" required>
                            <div>
                                <span class="font-medium">Willekeurige tegenstander</span>
                                <p class="text-sm text-gray-600">Speel tegen een willekeurige online speler</p>
                            </div>
                        </label>

                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="opponent_type" value="friend" class="mr-3" required>
                            <div>
                                <span class="font-medium">Vriend uitnodigen</span>
                                <p class="text-sm text-gray-600">Nodig een van je vrienden uit</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="friend-select" class="mb-6 hidden">
                    <label for="friend_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Selecteer een vriend
                    </label>
                    <select name="friend_id" id="friend_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Kies een vriend...</option>
                        @foreach($friends as $friend)
                            <option value="{{ $friend->id }}">{{ $friend->username }}</option>
                        @endforeach
                    </select>
                    @if($friends->isEmpty())
                        <p class="text-sm text-gray-500 mt-1">
                            Je hebt nog geen vrienden.
                            <a href="{{ route('friends.index') }}" class="text-blue-600 hover:text-blue-800">
                                Voeg vrienden toe
                            </a>
                        </p>
                    @endif
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('games.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                        Annuleren
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Spel Starten
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const opponentRadios = document.querySelectorAll('input[name="opponent_type"]');
            const friendSelect = document.getElementById('friend-select');

            opponentRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'friend') {
                        friendSelect.classList.remove('hidden');
                    } else {
                        friendSelect.classList.add('hidden');
                    }
                });
            });
        });
    </script>
@endsection
