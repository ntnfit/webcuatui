@extends('layouts.main')

@section('title', '404 - Không tìm thấy trang')

@section('content')
    @include('partials.navbar')
    
    <div class="min-h-screen bg-white dark:bg-gray-900 flex items-center justify-center px-4">
        <div class="text-center max-w-lg mx-auto animate-fade-in-up">
            <h1 class="text-9xl font-bold text-gray-200 dark:text-gray-800 mb-4">404</h1>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">
                Oops! Trang không tồn tại.
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8 text-lg">
                {{ $error ?? 'Có vẻ như trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.' }}
            </p>
            <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-300 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                Quay về trang chủ
            </a>
        </div>
    </div>
@endsection
