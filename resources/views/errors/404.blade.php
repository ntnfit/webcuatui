@extends('layouts.app')

@section('title', '404 - Không tìm thấy trang')

@section('content')
    <div class="container-page flex min-h-[60vh] items-center justify-center py-16">
        <div class="animate-fade-in-up max-w-lg text-center">
            <p class="font-mono text-7xl font-bold text-subtle sm:text-9xl">404</p>
            <h1 class="mt-4 text-2xl font-bold sm:text-3xl">Trang không tồn tại</h1>
            <p class="mt-3 text-muted">
                {{ $error ?? 'Có vẻ như trang bạn đang tìm không tồn tại hoặc đã được di chuyển.' }}
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-primary">Về trang chủ</a>
                <a href="{{ route('marketplace.index') }}" class="btn btn-secondary">Xem marketplace</a>
            </div>
        </div>
    </div>
@endsection
