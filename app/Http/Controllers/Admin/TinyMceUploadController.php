<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TinyMceUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Signed route ensures disk/dir params were not tampered with client-side
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,gif,webp,svg', 'max:10240'],
        ]);

        $disk = $request->query('disk', config('filament-tinyeditor.upload_disk', 'public'));
        $dir  = $request->query('dir',  config('filament-tinyeditor.upload_directory', 'uploads'));

        // Restrict to safe disk names — prevent arbitrary disk injection
        abort_if(! in_array($disk, ['public', 'local', 's3']), 403);

        $path = $request->file('file')->store($dir, $disk);

        return response()->json([
            'location' => Storage::disk($disk)->url($path),
        ]);
    }
}
