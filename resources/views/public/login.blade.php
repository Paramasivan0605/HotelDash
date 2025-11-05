<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Food Order - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-700 via-red-800 to-maroon-900 p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Modern Header Bar -->
        <div class="bg-gradient-to-r from-red-800 to-red-900 text-center text-white py-6 px-4">
            <h2 class="text-3xl font-extrabold tracking-wide">Hotel Food Order</h2>
            <p class="text-sm opacity-75 mt-1">Instant food delivery to your room</p>
        </div>

        <div class="p-6 text-black">
            @if(session('error'))
                <div class="mb-4 bg-red-100 p-3 text-sm text-red-700 rounded-lg shadow-md">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-black mb-1">
                        Full Name
                    </label>
                    <input type="text" id="name" name="name"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none text-black"
                        placeholder="Enter your full name" value="{{ old('name') }}" required>
                </div>

                <!-- Mobile Number -->
                <div>
                    <label for="mobile" class="block text-sm font-medium text-black mb-1">
                        Mobile Number
                    </label>
                    <input type="tel" id="mobile" name="mobile"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none text-black"
                        placeholder="Mobile Number" value="{{ old('mobile') }}" required>
                </div>

                <!-- Location Dropdown -->
                <div>
                    <label for="location" class="block text-sm font-medium text-black mb-1">
                        Select Hotel Branch
                    </label>
                   <select id="location" name="location"
    class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-red-500 focus:outline-none text-black"
    required>
    <option value="">Choose a location</option>
    @foreach ($locations as $location)
        <option value="{{ $location->location_id }}">{{ $location->location_name }}</option>
    @endforeach
</select>

                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3 text-white font-bold rounded-lg bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 shadow-lg transform hover:scale-[1.02] transition">
                    Login / Sign Up
                </button>
            </form>

            <div class="text-center mt-4">
                <small class="text-black opacity-70">
                    By continuing, you agree to our 
                    <span class="text-red-600 underline cursor-pointer">Terms of Service</span>
                </small>
            </div>
        </div>
    </div>

    <script>
        // Format mobile input to only allow 10 digits
        document.getElementById('mobile').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            e.target.value = value;
        });
    </script>

</body>
</html>
