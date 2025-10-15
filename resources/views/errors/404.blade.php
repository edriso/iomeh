@php
    // Force render the Inertia 404 page when Laravel's default error handling takes over
    $inertiaResponse = \Inertia\Inertia::render('errors/404');
    echo $inertiaResponse->toResponse(request())->getContent();
@endphp