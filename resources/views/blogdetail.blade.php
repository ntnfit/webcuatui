<x-layouts.appclient>
    @push('css')
        <link rel="stylesheet" href="https://unpkg.com/@highlightjs/cdn-assets@11.9.0/styles/default.min.css">
    @endpush
    <section x-cloak x-data="{
        back_button_is_hovering: false,
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
        class="mx-auto w-full max-w-8xl px-5 sm:px-10">
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
                <div class="mx-auto mt-5 w-full max-w-[82.5rem] border-t border-merino">
                </div>
                <div class="flex flex-col items-center space-y-4 pt-4">
                    <!-- Share Article -->
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Chia sẻ:</span>
                        <!-- Icons -->
                        <div class="flex space-x-2">
                            <!-- Facebook Icon -->
                            <a href="https://www.facebook.com/sharer/sharer.php?{{ url()->current()}}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20 1C21.6569 1 23 2.34315 23 4V20C23 21.6569 21.6569 23 20 23H4C2.34315 23 1 21.6569 1 20V4C1 2.34315 2.34315 1 4 1H20ZM20 3C20.5523 3 21 3.44772 21 4V20C21 20.5523 20.5523 21 20 21H15V13.9999H17.0762C17.5066 13.9999 17.8887 13.7245 18.0249 13.3161L18.4679 11.9871C18.6298 11.5014 18.2683 10.9999 17.7564 10.9999H15V8.99992C15 8.49992 15.5 7.99992 16 7.99992H18C18.5523 7.99992 19 7.5522 19 6.99992V6.31393C19 5.99091 18.7937 5.7013 18.4813 5.61887C17.1705 5.27295 16 5.27295 16 5.27295C13.5 5.27295 12 6.99992 12 8.49992V10.9999H10C9.44772 10.9999 9 11.4476 9 11.9999V12.9999C9 13.5522 9.44771 13.9999 10 13.9999H12V21H4C3.44772 21 3 20.5523 3 20V4C3 3.44772 3.44772 3 4 3H20Z"></path>
                                </svg>
                            </a>
                            <!-- X (formerly Twitter) Icon -->
                            <a href="http://twitter.com/share?text={{$article->slug}}&amp;url={{ url()->current()}}" target="_blank" class="text-black hover:text-gray-700">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 512">
                                    <path d="M256 0c141.385 0 256 114.615 256 256S397.385 512 256 512 0 397.385 0 256 114.615 0 256 0z" />
                                    <path fill="#fff" fill-rule="nonzero" d="M318.64 157.549h33.401l-72.973 83.407 85.85 113.495h-67.222l-52.647-68.836-60.242 68.836h-33.423l78.052-89.212-82.354-107.69h68.924l47.59 62.917 55.044-62.917zm-11.724 176.908h18.51L205.95 176.493h-19.86l120.826 157.964z" />
                                </svg>
                            </a>
                            <!-- Email Icon -->
                            <a href="mailto:?subject={{$article->slug}}&amp;body={{ url()->current()}}" target="_blank" class="text-pink-500 hover:text-pink-700">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 19H6.2C5.0799 19 4.51984 19 4.09202 18.782C3.71569 18.5903 3.40973 18.2843 3.21799 17.908C3 17.4802 3 16.9201 3 15.8V8.2C3 7.0799 3 6.51984 3.21799 6.09202C3.40973 5.71569 3.71569 5.40973 4.09202 5.21799C4.51984 5 5.0799 5 6.2 5H17.8C18.9201 5 19.4802 5 19.908 5.21799C20.2843 5.40973 20.5903 5.71569 20.782 6.09202C21 6.51984 21 7.0799 21 8.2V10M20.6067 8.26229L15.5499 11.6335C14.2669 12.4888 13.6254 12.9165 12.932 13.0827C12.3192 13.2295 11.6804 13.2295 11.0677 13.0827C10.3743 12.9165 9.73279 12.4888 8.44975 11.6335L3.14746 8.09863M14 21L16.025 20.595C16.2015 20.5597 16.2898 20.542 16.3721 20.5097C16.4452 20.4811 16.5147 20.4439 16.579 20.399C16.6516 20.3484 16.7152 20.2848 16.8426 20.1574L21 16C21.5523 15.4477 21.5523 14.5523 21 14C20.4477 13.4477 19.5523 13.4477 19 14L14.8426 18.1574C14.7152 18.2848 14.6516 18.3484 14.601 18.421C14.5561 18.4853 14.5189 18.5548 14.4903 18.6279C14.458 18.7102 14.4403 18.7985 14.405 18.975L14 21Z" stroke="#1E88E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Copy Link Section -->
                    <div class="relative flex w-full max-w-lg">
                        <!-- Link Input -->
                        <input type="text" id="linkInput" value="{{ url()->current() }}"
                               class="flex-grow rounded-l-md border border-gray-300 px-4 py-4 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 pr-16" readonly />
                        <!-- Copy Button -->
                        <button id="copyButton" onclick="copyToClipboard()"
                                class="absolute right-1 top-1 bottom-1 bg-purple-500 px-4 py-2 text-white hover:bg-purple-700
        focus:outline-none focus:ring-2 focus:ring-purple-500 rounded-md">
                            sao chép
                        </button>
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
                            class="rounded-full border border-slate-300 px-3 py-1 text-sm font-medium text-black text-slate-600 hover:bg-slate-100">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </section>
        <script>
            function copyToClipboard() {
                // Lấy giá trị của input
                var copyText = document.getElementById("linkInput");

                // Chọn toàn bộ nội dung của input
                copyText.select();
                copyText.setSelectionRange(0, 99999); // Đảm bảo cho các thiết bị di động

                // Thực hiện copy đến clipboard
                navigator.clipboard.writeText(copyText.value).then(function() {
                    // Thay đổi văn bản nút
                    var copyButton = document.getElementById("copyButton");
                    copyButton.textContent = "Đã sao chép";

                    // Đặt lại văn bản nút sau 2 giây
                    setTimeout(function() {
                        copyButton.textContent = "sao chép";
                    }, 2000);
                }).catch(function(error) {
                    console.error("Không thể sao chép: ", error);
                });
            }
        </script>
</x-layouts.appclient>
