<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{ asset("css/output.css") }}" rel="stylesheet">
  <link href="{{ asset("css/main.css") }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />

  @stack("additional-style")
</head>

<body>
    <main class="bg-[#FAFAFA] max-w-[640px] mx-auto min-h-screen relative flex flex-col has-[#CTA-nav]:pb-[120px] has-[#Bottom-nav]:pb-[120px]">
        @yield('content')
        {{-- @include('partials.menu') --}}
      </main>
      @stack("custom-script")
</body>
</html>
