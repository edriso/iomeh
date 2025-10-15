@php
    // Force render the Inertia 500 page when Laravel's default error handling takes over
    $inertiaResponse = \Inertia\Inertia::render('errors/500');
    echo $inertiaResponse->toResponse(request())->getContent();
@endphp