<?php

// API routes disabled - System now uses pure Laravel MVC with Blade templates
// All functionality migrated to Web Controllers in routes/web.php

use Illuminate\Support\Facades\Route;

// Health check endpoint for monitoring (optional)
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'message' => 'Lodge POS is running']);
});
