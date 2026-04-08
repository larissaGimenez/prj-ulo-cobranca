@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">{{ __('Nome Completo') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required autofocus autocomplete="name" />
                    @error('name')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="email"
                        class="block font-medium text-sm text-gray-700">{{ __('E-mail Institucional') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required autocomplete="username" />
                    @error('email')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="password" class="block font-medium text-sm text-gray-700">{{ __('Senha') }}</label>
                    <input id="password" type="password" name="password"
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required autocomplete="new-password" />
                    @error('password')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label for="password_confirmation"
                        class="block font-medium text-sm text-gray-700">{{ __('Confirmar Senha') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required autocomplete="new-password" />
                    @error('password_confirmation')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('login') }}">
                        {{ __('Já possui cadastro?') }}
                    </a>

                    <button type="submit"
                        class="ms-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Cadastrar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection