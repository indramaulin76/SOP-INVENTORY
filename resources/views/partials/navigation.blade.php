<nav class="bg-moca shadow">
    <div class="container mx-auto px-6 py-3">
        <div class="flex justify-between items-center">
            <div class="text-xl font-semibold text-white">
                <a href="/">{{ config('app.name', 'Laravel') }}</a>
            </div>

            <!-- Hamburger -->
            <div class="flex md:hidden">
                <button id="hamburger" type="button" class="text-white hover:text-gray-200 focus:outline-none focus:text-gray-200">
                    <svg viewBox="0 0 24 24" class="h-6 w-6 fill-current">
                        <path fill-rule="evenodd" d="M4 5h16a1 1 0 0 1 0 2H4a1 1 0 1 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2z"></path>
                    </svg>
                </button>
            </div>

            <!-- Links -->
            <div class="hidden md:flex items-center space-x-1">
                @auth
                    <a href="{{ url('/dashboard') }}" class="py-2 px-2 text-white">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="py-2 px-2 text-white">Log in</a>
                @endauth
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden">
            @auth
                <a href="{{ url('/dashboard') }}" class="block py-2 px-4 text-sm text-white">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block py-2 px-4 text-sm text-white">Log in</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    document.getElementById('hamburger').onclick = function () {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    };
</script>
