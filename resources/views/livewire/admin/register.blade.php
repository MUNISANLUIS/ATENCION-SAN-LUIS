<div class="max-w-md mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-700">Registro</h2>

    <form wire:submit.prevent="register">
        {{-- Email --}}
        <div class="mb-4">
            <label for="email" class="block text-gray-600 font-medium mb-2">Correo electrónico</label>
            <input
                type="text"
                id="email"
                wire:model.defer="email"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                placeholder="tu@correo.com"
            >
            @error('email')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div class="mb-4">
            <label for="password" class="block text-gray-600 font-medium mb-2">Contraseña</label>
            <input
                type="password"
                id="password"
                wire:model.defer="password"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
                placeholder="********"
            >
            @error('password')
                <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
        </div>

        {{-- Botón --}}
        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md transition">
            Crear cuenta
        </button>
    </form>

    @if (session()->has('success'))
        <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-md">
            {{ session('success') }}
        </div>
    @endif
</div>
