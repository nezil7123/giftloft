<?php

/*
|--------------------------------------------------------------------------
| Shared-hosting front controller shim
|--------------------------------------------------------------------------
|
| Lets the app load from the project root (domain.com) instead of
| domain.com/public, without moving any folders. The .htaccess in this
| directory rewrites all requests into public/; this file is the fallback
| for the "/" request on hosts where a directory index still fires.
|
| __DIR__ inside public/index.php resolves to the public/ folder, so its
| ../vendor and ../bootstrap paths keep working unchanged.
|
*/

require __DIR__.'/public/index.php';
