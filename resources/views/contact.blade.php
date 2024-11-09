@php
    seo()
        ->title('Liên hệ')
        ->description('Thông tin liên hệ hợp tác')
        ->image(asset('images/og.jpg'));
@endphp

<x-layouts.appclient>
    @if (session('success'))
        <div id="success-message" class="max-w-4xl mx-auto px-4 py-4 mt-4 bg-green-100 text-green-800 border border-green-400 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    <section class="py-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Contact Info Section -->
                    <div class="p-8 bg-gradient-to-br from-salmon/10 to-butter/10">
                        <h1 class="text-3xl font-bold text-gray-800">Liên hệ</h1>
                        <p class="mt-4 text-gray-600 leading-relaxed">
                            Bạn có ý tưởng lớn, bạn cần hỗ trợ về SAP ERP hoặc đang phát triển một thương hiệu và cần sự hỗ trợ?
                            Vậy thì đừng ngần ngại liên hệ với tôi.
                            Tôi rất mong được lắng nghe về dự án của bạn và sẵn lòng hỗ trợ.
                        </p>

                        <!-- Email Section -->
                        <div class="mt-8">
                            <h2 class="text-lg font-semibold text-gray-800">Email</h2>
                            <div class="mt-4">
                                <a href="mailto:ntnguyen0310@gmail.com"
                                   class="flex items-center group hover:text-salmon transition-colors">
                                    <div class="bg-butter h-12 w-12 rounded-full flex items-center justify-center shrink-0 group-hover:bg-salmon/20 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill='currentColor' viewBox="0 0 479.058 479.058">
                                            <path d="M434.146 59.882H44.912C20.146 59.882 0 80.028 0 104.794v269.47c0 24.766 20.146 44.912 44.912 44.912h389.234c24.766 0 44.912-20.146 44.912-44.912v-269.47c0-24.766-20.146-44.912-44.912-44.912zm0 29.941c2.034 0 3.969.422 5.738 1.159L239.529 264.631 39.173 90.982a14.902 14.902 0 0 1 5.738-1.159zm0 299.411H44.912c-8.26 0-14.971-6.71-14.971-14.971V122.615l199.778 173.141c2.822 2.441 6.316 3.655 9.81 3.655s6.988-1.213 9.81-3.655l199.778-173.141v251.649c-.001 8.26-6.711 14.97-14.971 14.97z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <span class="text-sm text-gray-500">Mail</span>
                                        <p class="font-medium">ntnguyen0310@gmail.com</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="mt-8">
                            <h2 class="text-lg font-semibold text-gray-800">Mạng xã hội</h2>
                            <div class="flex mt-4 space-x-4">
                                <a href="https://www.facebook.com/harry.ntnguyen"
                                   target="_blank"
                                   class="bg-gray-100 h-12 w-12 rounded-full flex items-center justify-center shrink-0 hover:bg-salmon/20 transition-colors">
                                    <svg class="w-6 h-6 text-salmon" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5L14.171.5C10.244.5,9.5,3.438,9.5,5.32v2.14h-3v4h3v12h5v-12h3.851l.466-4Z"/>
                                    </svg>
                                </a>
                                <a href="https://www.linkedin.com/in/nguyen0310/"
                                   target="_blank"
                                   class="bg-gray-100 h-12 w-12 rounded-full flex items-center justify-center shrink-0 hover:bg-salmon/20 transition-colors">
                                    <svg class="w-6 h-6 text-salmon" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447,20.452H16.893V14.883c0-1.328-.027-3.037-1.852-3.037-1.853,0-2.136,1.445-2.136,2.939v5.667H9.351V9h3.414v1.561h.046a3.745,3.745,0,0,1,3.37-1.85c3.6,0,4.267,2.37,4.267,5.455v6.286ZM5.337,7.433A2.062,2.062,0,0,1,3.275,5.371,2.062,2.062,0,0,1,5.337,3.309,2.062,2.062,0,0,1,7.4,5.371,2.062,2.062,0,0,1,5.337,7.433Zm1.82,13.019H3.509V9H7.157Z"/>
                                    </svg>
                                </a>
                                <a href="https://zalo.me/0981710031"
                                   target="_blank"
                                   class="bg-gray-100 h-12 w-12 rounded-full flex items-center justify-center shrink-0 hover:bg-salmon/20 transition-colors">
                                    <svg class="w-6 h-6 text-salmon" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.49 10.272v-.45h1.347v6.322h-.77a.576.576 0 0 1-.577-.573v.001a3.273 3.273 0 0 1-1.851.575 3.228 3.228 0 0 1-3.229-3.227 3.228 3.228 0 0 1 3.229-3.224 3.274 3.274 0 0 1 1.851.576zm-1.851.326a2.31 2.31 0 0 0-2.31 2.321 2.31 2.31 0 0 0 2.31 2.321 2.31 2.31 0 0 0 2.31-2.321 2.31 2.31 0 0 0-2.31-2.321zm7.95-.326v-.45h1.347v6.322h-.77a.576.576 0 0 1-.577-.573v.001a3.273 3.273 0 0 1-1.851.575 3.228 3.228 0 0 1-3.229-3.227 3.228 3.228 0 0 1 3.229-3.224 3.274 3.274 0 0 1 1.851.576zm-1.851.326a2.31 2.31 0 0 0-2.31 2.321 2.31 2.31 0 0 0 2.31 2.321 2.31 2.31 0 0 0 2.31-2.321 2.31 2.31 0 0 0-2.31-2.321zm-11.54 2.321c0 .576-.229 1.094-.603 1.47l-.005.004a2.065 2.065 0 0 1-1.472.602 2.065 2.065 0 0 1-1.473-.602l-.004-.004a2.065 2.065 0 0 1-.603-1.47c0-.576.229-1.094.603-1.47l.005-.004A2.065 2.065 0 0 1 3.52 10.85c.576 0 1.094.229 1.47.603l.004.004c.374.376.603.894.603 1.47zm-.577-4.741H3.52a3.931 3.931 0 0 0-3.932 3.932v7.496A3.932 3.932 0 0 0 3.52 24h16.96a3.932 3.932 0 0 0 3.932-3.932v-7.496a3.932 3.932 0 0 0-3.932-3.932H15.35c-.177-.307-.567-.652-1.104-.652H9.754c-.536 0-.927.345-1.104.652z"/>
                                        <path d="M3.52 14.084a1.16 1.16 0 0 0 1.16-1.16c0-.641-.519-1.16-1.16-1.16a1.16 1.16 0 0 0-1.16 1.16c0 .641.519 1.16 1.16 1.16z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="p-8">
                        <form method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                            @csrf
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên</label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       required
                                       value="{{ old('name') }}"
                                       class="w-full px-4 py-2.5 text-gray-800 border border-gray-300 rounded-lg focus:ring-2 focus:ring-salmon focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                                />
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       required
                                       value="{{ old('email') }}"
                                       class="w-full px-4 py-2.5 text-gray-800 border border-gray-300 rounded-lg focus:ring-2 focus:ring-salmon focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                                />
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                                <input type="text"
                                       id="subject"
                                       name="subject"
                                       required
                                       value="{{ old('subject') }}"
                                       class="w-full px-4 py-2.5 text-gray-800 border border-gray-300 rounded-lg focus:ring-2 focus:ring-salmon focus:border-transparent transition-all @error('subject') border-red-500 @enderror"
                                />
                                @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Nội dung</label>
                                <textarea id="message"
                                          name="message"
                                          rows="6"
                                          required
                                          class="w-full px-4 py-2.5 text-gray-800 border border-gray-300 rounded-lg focus:ring-2 focus:ring-salmon focus:border-transparent transition-all @error('message') border-red-500 @enderror"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

{{--                            @if(session('success'))--}}
{{--                                <div class="p-4 rounded-lg bg-green-50 text-green-600">--}}
{{--                                    {{ session('success') }}--}}
{{--                                </div>--}}
{{--                            @endif--}}

                            <button type="submit"
                                    class="w-full px-6 py-3 text-white bg-salmon rounded-lg hover:bg-salmon-600 focus:ring-4 focus:ring-salmon/30 transition-all">
                                Gửi tin nhắn
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const successMessage = document.getElementById('success-message');
                if (successMessage) {
                    // Ẩn thông báo sau 10 giây (10,000 ms)
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 10000);
                }
            });
        </script>
</x-layouts.appclient>
