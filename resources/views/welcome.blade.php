<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Prrahi</title>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .animated-bg {
      background: linear-gradient(-45deg, #6b73ff, #000dff, #1e3a8a, #6366f1);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
    }
  </style>
</head>
<body class="animated-bg min-h-screen grid place-items-center">

  <!-- Centered Content -->
  <div class="text-white text-center w-full max-w-2xl mx-auto px-4 space-y-10">

    <!-- Logo -->
    <img src="{{ asset('images/logo/auth-logo.svg') }}" alt="Logo"
         class="w-48 h-32 sm:w-32 sm:h-28 md:w-64 md:h-32 mx-auto mt-4 mb-0 sm:mt-0" />

    <!-- Welcome Message -->
    <h1 class="text-2xl sm:text-4xl md:text-6xl font-bold drop-shadow-lg">
      Welcome to Prrahi
    </h1>

    <!-- Login Buttons -->
    <div class="flex flex-col gap-4 sm:flex-row sm:justify-center sm:items-center sm:gap-6">
      <a href="/admin/login"
         class="bg-white text-blue-700 hover:bg-blue-100 font-semibold py-3 px-6 rounded-xl shadow-md transition duration-300 w-full sm:w-52 mx-auto text-center flex items-center justify-center leading-tight sm:min-h-14">
        Administrator
      </a>
      <a href="/sales/login"
         class="bg-white text-green-700 hover:bg-green-100 font-semibold py-3 px-6 rounded-xl shadow-md transition duration-300 w-full sm:w-52 mx-auto text-center flex items-center justify-center leading-tight sm:min-h-14">
        Sales
      </a>
      <a href="/distributor/login"
         class="bg-white text-purple-700 hover:bg-purple-100 font-semibold py-3 px-6 rounded-xl shadow-md transition duration-300 w-full sm:w-52 mx-auto text-center flex items-center justify-center leading-tight sm:min-h-14">
        Distributor
      </a>
    </div>

  </div>

</body>
</html>
