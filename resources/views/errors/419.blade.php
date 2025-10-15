@php
    // Force render the Inertia 419 page when Laravel's default error handling takes over
    $inertiaResponse = \Inertia\Inertia::render('errors/419');
    echo $inertiaResponse->toResponse(request())->getContent();
@endphp