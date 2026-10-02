@extends('layouts.app')

@section('title', 'Công cụ cho kế toán và ERP | HarryDev')
@section('description', 'Các công cụ trực tuyến cho kế toán và người dùng SAP Business One, bắt đầu với công cụ thuế trong khu vực khách hàng.')
@section('og:title', 'Công cụ cho kế toán và ERP | HarryDev')
@section('og:description', 'Các công cụ trực tuyến cho kế toán và người dùng SAP Business One.')

@section('content')
    <section class="border-b border-line-soft">
        <div class="container-page py-10 sm:py-14">
            <p class="eyebrow mb-4">tools</p>
            <h1 class="max-w-3xl text-3xl font-bold tracking-tight sm:text-4xl">Công cụ</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-muted sm:text-base">
                Các công cụ trực tuyến phục vụ công việc kế toán và vận hành ERP hằng ngày.
            </p>
        </div>
    </section>

    <section class="container-page py-10">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ url('/customer') }}" class="card group flex h-full flex-col p-5">
                <div class="mb-3 flex items-center gap-2">
                    <span class="badge badge-green">Thuế</span>
                    <span class="badge badge-muted">Cần đăng nhập</span>
                </div>
                <h2 class="mb-2 text-base font-semibold leading-snug transition-colors group-hover:text-link">Công cụ thuế</h2>
                <p class="mb-4 flex-1 text-sm leading-relaxed text-muted">
                    Làm việc với dữ liệu thuế của doanh nghiệp trong khu vực khách hàng. Đăng nhập hoặc đăng ký tài khoản để bắt đầu.
                </p>
                <span class="mt-auto border-t border-line-soft pt-3 font-mono text-[11px] text-link group-hover:underline">Mở công cụ →</span>
            </a>
        </div>

        <p class="mt-8 font-mono text-xs text-subtle">Thêm công cụ sẽ được bổ sung tại đây.</p>
    </section>
@endsection
