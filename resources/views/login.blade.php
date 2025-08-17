<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sentiment Analytics</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/phosphor-icons@1.4.2/src/css/icons.min.css" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/sentiment.js'])

</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">

        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900">
                Selamat Datang Kembali!
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Silakan masuk untuk melanjutkan.
            </p>
        </div>

        <form class="space-y-6" action="#" method="POST">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Alamat Email
                </label>
                <input id="email" name="email" type="email" autocomplete="email" required
                    class="w-full px-3 py-2 mt-1 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password
                </label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    class="w-full px-3 py-2 mt-1 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="remember-me" class="block ml-2 text-sm text-gray-900">
                        Ingat Saya
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-blue-600 hover:underline">
                        Lupa password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Masuk
                </button>
            </div>
        </form>

        <hr class="border-gray-200">

        <p class="text-sm text-center text-gray-600">
            Belum punya akun?
            <a href="#" class="font-medium text-blue-600 hover:underline">
                Daftar di sini
            </a>
        </p>

    </div>

</body>

</html>
