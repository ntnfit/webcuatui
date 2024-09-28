<x-layouts.appclient>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="mx-auto w-full max-w-screen-lg px-10 pt-40 lg:px-5 rounded-lg">
        <h2 class="text-center text-gray-700 font-bold text-xl mb-6">Đăng nhập</h2>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('social.redirect', ['provider' => 'google']) }}"
                class="bg-white border border-gray-300 text-gray-700 rounded-lg p-2 flex items-center justify-center hover:bg-gray-100">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google"
                    class="w-5 h-5 mr-2">
                Google
            </a>
            <a href="{{ route('social.redirect', ['provider' => 'github']) }}"
                class="bg-white border border-gray-300 text-gray-700 rounded-lg p-2 flex items-center justify-center hover:bg-gray-100">
                <img src="https://upload.wikimedia.org/wikipedia/commons/9/91/Octicons-mark-github.svg" alt="Github"
                    class="w-5 h-5 mr-2">
                Github
            </a>
            <a href="{{ route('social.redirect', ['provider' => 'linkedin-openid']) }}"
                class="bg-white border border-gray-300 text-gray-700 rounded-lg p-2 flex items-center justify-center hover:bg-gray-100">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/ca/LinkedIn_logo_initials.png" alt="Linkedin"
                    class="w-5 h-5 mr-2">
                Linkedin
            </a>
            <a href="#"
                class="bg-white border border-gray-300 text-gray-700 rounded-lg p-2 flex items-center justify-center hover:bg-gray-100">
                <img src="https://j2team.dev/images/brand/ph.ico" alt="Pornhub" class="w-5 h-5 mr-2">
                Pornhub
            </a>
        </div>
    </div>
</x-layouts.appclient>
