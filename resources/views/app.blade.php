<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#070b16">

        {{-- Per-page title/description/OG tags come from the <Seo> component. --}}
        <title inertia>{{ config('app.name', 'ComeYay') }}</title>

        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="alternate icon" href="/favicon.ico" sizes="any">
        <link rel="apple-touch-icon" href="/brand/comeyay-logo.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|playfair-display:400,500,600,700,400i,500i,600i&display=swap" rel="stylesheet" />

        {{-- Site-wide structured data so search engines learn the brand. --}}
        @php
            $siteUrl = rtrim(config('app.url'), '/');
            $organizationLd = json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'ComeYay',
                'url' => $siteUrl,
                'logo' => $siteUrl.'/brand/comeyay-logo.png',
                'slogan' => 'Invite. Plan. Celebrate.',
                'description' => 'ComeYay lets you create beautiful event websites and digital invitations for weddings, birthdays, baby showers and every celebration in between.',
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        @endphp
        <script type="application/ld+json">{!! $organizationLd !!}</script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
