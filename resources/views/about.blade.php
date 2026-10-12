@extends('layouts.app')

@section('title', 'About ToiLamERP — ERP & AI Solutions')
@section('description', 'ToiLamERP is an independent technology initiative based in Vietnam, building ERP integration, automation and AI-driven solutions for SMEs.')

@section('content')
<section class="border-b border-line-soft">
    <div class="container-page max-w-3xl py-14 sm:py-20">
        <p class="eyebrow mb-5">about</p>
        <h1 class="text-3xl font-bold leading-tight tracking-tight sm:text-4xl">About ToiLamERP</h1>
        <p class="mt-5 text-base leading-relaxed text-muted">
            ToiLamERP is an independent technology initiative based in Vietnam, developing practical enterprise
            software products for SMEs and growing businesses. Our mission is to make ERP systems more connected,
            accessible, and intelligent.
        </p>

        <h2 class="mt-12 text-xl font-semibold">Our Solutions</h2>
        <ul class="mt-4 space-y-4 text-sm leading-relaxed text-muted">
            <li><strong class="text-fg">SAP Business One Add-ons</strong> — extend ERP capabilities with custom business applications.</li>
            <li><strong class="text-fg">ERP Integration Platform</strong> — connect ERP systems with banking, CRM, e-invoicing, and external applications.</li>
            <li><strong class="text-fg">AI-Powered Automation</strong> — intelligent tools and AI agents that streamline enterprise operations.</li>
            <li><strong class="text-fg">Enterprise APIs</strong> — secure, scalable middleware and API integration solutions.</li>
        </ul>

        <h2 class="mt-12 text-xl font-semibold">Contact</h2>
        <p class="mt-4 text-sm text-muted">
            Website: <a href="https://toilamerp.com" class="hover:text-link">https://toilamerp.com</a><br>
            Email: <a href="mailto:harry@toilamerp.com" class="hover:text-link">harry@toilamerp.com</a>
        </p>
    </div>
</section>
@endsection
