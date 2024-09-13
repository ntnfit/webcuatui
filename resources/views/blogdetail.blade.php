<x-layouts.appclient>
    <section x-cloak x-data="{
        back_button_is_hovering: false,
        ,
    }" x-ref="section" x-init="() => {
        if (reducedMotion) return
        gsap.fromTo(
            $refs.section, {
                autoAlpha: 0,
                y: 50,
            }, {
                autoAlpha: 1,
                y: 0,
                duration: 0.7,
                ease: 'circ.out',
            },
        )
    }"
        class="mx-auto w-full max-w-7xl px-5 xl:px-0">
        <div class="flex flex-wrap items-center justify-between gap-5 pt-20">
            {{-- Back Button --}}
            <a x-on:mouseenter="back_button_is_hovering = true" x-on:mouseleave="back_button_is_hovering = false"
                href="{{ route('blogs') }}"
                class="flex items-center gap-3 p-1 text-dolphin transition duration-300 hover:-translate-x-2 hover:text-evening">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 20 20">
                    <path fill="currentColor"
                        d="M2.64 11.917h16.591a.78.78 0 0 1 .769.792a.78.78 0 0 1-.769.791H.771c-.688 0-1.03-.857-.541-1.354L5.549 6.73a.754.754 0 0 1 1.087.006a.808.808 0 0 1-.005 1.119l-3.99 4.063Z" />
                </svg>
                <div class="text-xl font-medium">{{ __('Blog') }}</div>
            </a>
            <div class="flex flex-wrap items-center gap-3 transition duration-300 will-change-transform"
                :class="{
                    'opacity-60 translate-x-2': back_button_is_hovering,
                }">
                {{--                    <livewire:star-record :record="$article" /> --}}
            </div>
        </div>

        <div class="flex flex-col md:grid md:grid-cols-2 gap-4 items-center">
            <div class="pt-5 gap-4 h-full flex flex-col justify-between">
                <div class="flex">
                    <x-articles.type-badge :type="$type" />
                </div>
                <div class="text-3xl font-extrabold leading-normal">
                    {{ App::getLocale() === 'vi' ? $article->title : $article->title_en ?? $article->title }}

                </div>

                <p>
                    {{ App::getLocale() === 'vi' ? $article->sub_title : $article->sub_title_en ?? $article->sub_title }}
                </p>
                <div class="pt-2">
                    <div class="text-base text-dolphin">
                        {{ $article->published_at->toFormattedDateString() }}
                    </div>
                </div>
            </div>
            <div class="flex justify-center w-full sm:w-auto">
                <img class="aspect-[16/9] w-full sm:w-10/12 rounded-xl bg-cover bg-center bg-no-repeat ring-1 ring-dawn-pink"
                    src="{{ $article->featurePhoto }}" alt="{{ $article->photo_alt_text }}">
                {{-- src="https://images.ctfassets.net/h6goo9gw1hh6/5wl7KPvpM44dPJ3kwKfwTe/0eb029cd00424d1b1934d780f57bbc34/Aspect-Ration-Image-1to1.jpg?w=1600&h=1600&fl=progressive&q=70&fm=jpg" --}}
                {{-- alt="{{ $article->photo_alt_text }}"> --}}
            </div>
        </div>

        {{-- Categories --}}
        <div class="flex flex-wrap items-center gap-3.5 pt-6 mb-5">

            @foreach ($article->categories as $category)
                <div class="select-none rounded-full bg-stone-200/50 px-5 py-2.5 text-sm">
                    <div class="text-sm">
                        {{ $category->name }}
                    </div>
                </div>
            @endforeach
        </div>
        {{-- <div class="mx-auto mt-5 w-full max-w-[82.5rem] border-t border-merino">
        </div> --}}
        {{-- Main Content --}}
        <div class="flex flex-col items-start gap-20 pt-7 transition duration-300 will-change-transform lg:flex-row"
            :class="{
                'opacity-60 translate-x-2': back_button_is_hovering,
            }">
            {{-- Left Side --}}
            <div class="w-full ">
                {{-- Article Type --}}

                {{-- Content --}}
                <div class="pt-8 border-t border-merino">
                    <div
                        class="prose w-max-[80ch] w-full max-w-full sm:max-w-screen-sm md:max-w-screen-md lg:max-w-screen-lg xl:max-w-screen-xl pr-4 sm:pr-6 md:pr-8 lg:pr-12 mx-auto
                         selection:bg-stone-500/30 prose-a:break-words
                         prose-blockquote:not-italic
                         prose-code:break-words
                         prose-code:bg-black-100
                         p-code:bg-merino
                         prose-code:px-1.5 prose-code:py-0.5
                         prose-code:font-normal prose-code:before:hidden
                         prose-code:after:hidden [&_p]:before:hidden [&_p]:after:hidden  [&_code:not([class])]:bg-merino">
                        {!! App::getLocale() === 'vi' ? $article->body : $article->body_en ?? $article->body !!}

                    </div>
                </div>
            </div>

            {{-- Right Side --}}
            <div class="flex flex-col w-full items-center gap-12 lg:max-w-sm xl:max-w-md">
                {{-- Author --}}
                <div class="w-full pt-10 text-evening">
                    <div class="grid w-full place-items-center">
                        <div class="flex items-center gap-4 pt-3">
                        </div>
                        <div class="mt-4 space-y-4 rounded-2xl  p-6 text-center shadow-lg shadow-black/[0.01]">
                            <div class="prose">

                            </div>
                        </div>
                    </div>
                </div>

                @if (count($mostBlogs))
                    {{-- More From This Author --}}
                    <div class="mx-auto w-full">
                        <div class="text-lg font-extrabold">
                            {{ __('More from this author') }}
                        </div>

                        <div class="w-full space-y-5 pt-7">
                            @foreach ($mostBlogs->take(3) as $otherArticle)
                                <a href="{{ route('admin.post.show', ['blogs' => $otherArticle]) }}"
                                    class="relative block w-full rounded-2xl bg-white px-5 py-3 shadow-lg shadow-hurricane/5 transition duration-300 ease-out will-change-transform hover:translate-x-2">
                                    <div class="flex w-full items-center justify-between gap-5">
                                        {{-- Type --}}
                                        <x-articles.type-badge :type="showBadgeType($otherArticle->type)" size="sm" />

                                        {{-- Stars --}}
                                        <div class="flex items-center gap-1.5 pr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="text-peach-orange"
                                                width="20" height="20" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                    d="M9.153 5.408C10.42 3.136 11.053 2 12 2c.947 0 1.58 1.136 2.847 3.408l.328.588c.36.646.54.969.82 1.182c.28.213.63.292 1.33.45l.636.144c2.46.557 3.689.835 3.982 1.776c.292.94-.546 1.921-2.223 3.882l-.434.507c-.476.557-.715.836-.822 1.18c-.107.345-.071.717.001 1.46l.066.677c.253 2.617.38 3.925-.386 4.506c-.766.582-1.918.051-4.22-1.009l-.597-.274c-.654-.302-.981-.452-1.328-.452c-.347 0-.674.15-1.329.452l-.595.274c-2.303 1.06-3.455 1.59-4.22 1.01c-.767-.582-.64-1.89-.387-4.507l.066-.676c.072-.744.108-1.116 0-1.46c-.106-.345-.345-.624-.821-1.18l-.434-.508c-1.677-1.96-2.515-2.941-2.223-3.882c.293-.941 1.523-1.22 3.983-1.776l.636-.144c.699-.158 1.048-.237 1.329-.45c.28-.213.46-.536.82-1.182l.328-.588Z" />
                                            </svg>
                                            <div class="pt-0.5 text-sm font-medium text-dolphin">
                                                {{ number_format(100) }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Title --}}
                                    <div class="px-1 pb-1 pt-4 font-medium">
                                        <div class="line-clamp-2">
                                            {{ App::getLocale() === 'vi' ? $otherArticle->title : $otherArticle->title_en ?? $otherArticle->title }}

                                        </div>
                                        <div class="pt-1 text-xs text-dolphin/80">
                                            {{ $otherArticle->published_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-x-2.5 gap-y-3 pt-3">
                                        @foreach ($otherArticle->categories as $category)
                                            <div class="rounded-full bg-slate-100 px-5 py-2.5 text-xs">
                                                {{ $category->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="mx-auto mt-5 w-full max-w-[82.5rem] border-t border-merino">
        </div>
        @if ($article->tags->count())
            <div class="pt-10">
                <span class="mb-3 block font-semibold">Tags</span>
                <div class="space-x-2 space-y-1">
                    @foreach ($article->tags as $tag)
                        {{-- <a href="{{ route('filamentblog.tag.post', ['tag' => $tag->slug]) }}" --}}
                        <a href="#"
                            class="rounded-full border border-slate-300 px-3 py-1 text-sm font-medium font-medium text-black text-slate-600 hover:bg-slate-100">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
    @push('js')
    @endpush
</x-layouts.appclient>
