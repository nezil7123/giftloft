<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#070b16">

        {{-- Title and all social/meta tags are rendered server-side: the crawlers
             that build link previews do not run JavaScript, so Inertia's <Head>
             cannot be the source of truth. See App\Support\PageSeo. --}}
        <title inertia>{{ \App\Support\PageSeo::fullTitle(\App\Support\PageSeo::for($page['component'] ?? null, $page['props'] ?? [])['title']) }}</title>

        @include('partials.seo')

        {{-- Icons: SVG for modern browsers, ICO fallback, PNGs for OS/app surfaces. --}}
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="alternate icon" href="/favicon.ico" sizes="16x16 32x32 48x48">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="manifest" href="/site.webmanifest">
        <meta name="apple-mobile-web-app-title" content="ComeYay">
        <meta name="application-name" content="ComeYay">

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

            $webSiteLd = json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'ComeYay',
                'url' => $siteUrl,
                'inLanguage' => 'en',
                'publisher' => ['@type' => 'Organization', 'name' => 'ComeYay'],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        @endphp
        <script type="application/ld+json">{!! $organizationLd !!}</script>
        <script type="application/ld+json">{!! $webSiteLd !!}</script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
