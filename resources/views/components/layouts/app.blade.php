<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? config('app.name') }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased">
  <x-main full-width>
    <x-slot:sidebar drawer="main-drawer" collapsible
      class="bg-sky-800 pt-3 text-white">

      <!-- Hidden when collapsed -->
      <div
        class="hidden-when-collapsed ml-5 text-4xl font-black text-yellow-500">
        mary</div>

      <!-- Display when collapsed -->
      <div
        class="display-when-collapsed ml-5 text-4xl font-black text-orange-500">
        m</div>

      <!-- Custom `active menu item background color` -->
      <x-menu activate-by-route active-bg-color="bg-base-300/10">

        <!-- User -->
        @if ($user = auth()->user())
          <x-list-item :item="$user" sub-value="username"
            no-separator no-hover
            class="!-mx-2 mb-5 mt-2 border-y border-y-sky-900">
            <x-slot:actions>
              <div class="tooltip tooltip-left" data-tip="logoff">
                <x-button icon="o-power"
                  class="btn-circle btn-ghost btn-xs"
                  onclick="window.location.href='{{ route('logout') }}'" />
              </div>
            </x-slot:actions>
          </x-list-item>
        @endif

        <x-menu-item title="Home" icon="o-home" link="/" />
        <x-menu-item title="Yeah" icon="o-sparkles"
          link="####" />

        <x-menu-sub title="Settings" icon="o-cog-6-tooth">
          <x-menu-item title="Wifi" icon="o-wifi" link="####" />
          <x-menu-item title="Archives" icon="o-archive-box"
            link="####" />
        </x-menu-sub>
      </x-menu>
    </x-slot:sidebar>

    <!-- The `$slot` goes here -->
    <x-slot:content>
      {{ $slot }}
    </x-slot:content>
  </x-main>
</body>

</html>