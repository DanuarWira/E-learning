<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-neutral-50 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-lg shadow-md">
        <div class="text-center">
            <a href="#" class="text-3xl font-bold text-indigo-600">Engage<span class="text-neutral-800">English</span></a>
            <h2 class="mt-4 text-2xl font-bold text-neutral-900">
                Sign in to your account
            </h2>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            @error('email')
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ $message }}</span>
            </div>
            @enderror
            <input type="hidden" name="remember" value="true">
            <div class="rounded-md shadow-sm -space-y-px flex flex-col gap-4">
                <div>
                    <label for="email-address" class="sr-only">Email address</label>
                    <input id="email-address" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="appearance-none rounded-md relative block w-full px-3 py-3 border border-neutral-300 placeholder-neutral-500 text-neutral-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-md relative block w-full px-3 py-3 border border-neutral-300 placeholder-neutral-500 text-neutral-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-neutral-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-neutral-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <button type="submit" class="relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign in
                </button>
                <p class="text-center text-sm text-neutral-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Register
                    </a>
                </p>
            </div>
        </form>
    </div>
</body>

</html>