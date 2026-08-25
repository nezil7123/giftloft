@php
    use App\Support\PageSeo;

    $seo = PageSeo::for($page['component'] ?? null, $page['props'] ?? []);

    $seoTitle = PageSeo::fullTitle($seo['title']);
    $seoImage = PageSeo::absoluteImage($seo['image']);
    $seoAlt = $seo['imageAlt'] ?: $seoTitle;
    $seoUrl = rtrim(config('app.url'), '/').request()->getPathInfo();
    $isDefaultImage = str_ends_with($seoImage, PageSeo::DEFAULT_IMAGE);
    $seoImageType = match (strtolower(pathinfo(parse_url($seoImage, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION))) {
        'jpg', 'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        default => 'image/png',
    };
@endphp
<meta name="description" content="{{ $seo['description'] }}">
<link rel="canonical" href="{{ $seoUrl }}">
@if ($seo['noindex'])
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
@endif

{{-- Open Graph — Facebook, WhatsApp, LinkedIn, Slack, iMessage --}}
<meta property="og:site_name" content="ComeYay">
<meta property="og:locale" content="en_US">
<meta property="og:type" content="{{ $seo['type'] }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:secure_url" content="{{ $seoImage }}">
<meta property="og:image:type" content="{{ $seoImageType }}">
@if ($isDefaultImage)
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif
<meta property="og:image:alt" content="{{ $seoAlt }}">

{{-- X / Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seo['description'] }}">
<meta name="twitter:image" content="{{ $seoImage }}">
<meta name="twitter:image:alt" content="{{ $seoAlt }}">
