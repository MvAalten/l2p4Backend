@extends('layouts.app')

@section('title', 'Instellingen')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Instellingen</h1>

        <div class="space-y-6">
            <!-- Profile Settings -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Profiel</h2>
                <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gebruikersnaam</label>
                        <input type="text" name="username" value="{{ Auth::user()->username }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                        <input type="email" name="email" value="{{ Auth::user()->email }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Avatar</label>
                        <input type="file" name="avatar" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Profiel Bijwerken
                    </button>
                </form>
            </div>

            <!-- Game Settings -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Spel Instellingen</h2>
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="notify_by_email" value="1"
                                   {{ $settings->notify_by_email ? 'checked' : '' }}
                                   class="mr-2">
                            E-mail notificaties ontvangen
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_profile_public" value="1"
                                   {{ $settings->is_profile_public ? 'checked' : '' }}
                                   class="mr-2">
                            Profiel openbaar maken
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Taal</label>
                        <select name="language" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="nl" {{ $settings->language === 'nl' ? 'selected' : '' }}>Nederlands</option>
                            <option value="en" {{ $settings->language === 'en' ? 'selected' : '' }}>English</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Moeilijkheidsgraad</label>
                        <select name="preference" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="easy" {{ $settings->preference === 'easy' ? 'selected' : '' }}>Makkelijk</option>
                            <option value="medium" {{ $settings->preference === 'medium' ? 'selected' : '' }}>Gemiddeld</option>
                            <option value="hard" {{ $settings->preference === 'hard' ? 'selected' : '' }}>Moeilijk</option>
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Instellingen Opslaan
                    </button>
                </form>
            </div>

            <!-- Password Change -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">Wachtwoord Wijzigen</h2>
                <form method="POST" action="{{ route('settings.password') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Huidig Wachtwoord</label>
                        <input type="password" name="current_password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nieuw Wachtwoord</label>
                        <input type="password" name="new_password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bevestig Nieuw Wachtwoord</label>
                        <input type="password" name="new_password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                        Wachtwoord Wijzigen
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
