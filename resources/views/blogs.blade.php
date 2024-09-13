@php
    // Fake data for articles
    // Fake data for categories
    // $categories = $categories;

    // Fake data for types
@endphp

{{-- <x-layouts.appclient>
    <div class="mx-auto mt-5 w-full max-w-[82.5rem] border-t border-merino">

    </div>
    <x-articles.list :articles="$articles" :categories="$categories" :types="$types" />
</x-layouts.appclient> --}}
<x-layouts.appclient>
    <x-articles.hero :$articlesCount />
    <div class="mx-auto mt-5 w-full max-w-[82.5rem] border-t border-merino">

    </div>
    <x-articles.list :articles="$articles" :categories="$categories" :types="$types" />
</x-layouts.appclient>
