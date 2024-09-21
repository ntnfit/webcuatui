@php
    seo()
        ->title('Liên hệ')
        ->description('Thông tin liên hệ hợp tác')
        ->image(asset('images/og.jpg'));
@endphp

<x-layouts.appclient>
    <section>
        <div class="mt-6">
            <div class="grid sm:grid-cols-2 items-start gap-14 p-8 mx-auto max-w-4xl bg-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.3)] rounded-md font-[sans-serif]">
                <div>
                    <h1 class="text-gray-800 text-3xl">Liên hệ</h1>
                    <p class="text-sm text-gray-500 mt-4">
                        Bạn có ý tưởng lớn, bạn cần hỗ trợ về SAP ERP hoặc đang phát triển một thương hiệu và cần sự hỗ trợ?
                        Vậy thì đừng ngần ngại liên hệ với tôi.
                        Tôi rất mong được lắng nghe về dự án của bạn và sẵn lòng hỗ trợ.</p>

                    <div class="mt-12">
                        <h2 class="text-gray-800 text-base font-bold">Email</h2>
                        <ul class="mt-4">
                            <li class="flex items-center">
                                <div class="bg-butter h-10 w-10 rounded-full flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill='#0A0A0A'
                                         viewBox="0 0 479.058 479.058">
                                        <path
                                            d="M434.146 59.882H44.912C20.146 59.882 0 80.028 0 104.794v269.47c0 24.766 20.146 44.912 44.912 44.912h389.234c24.766 0 44.912-20.146 44.912-44.912v-269.47c0-24.766-20.146-44.912-44.912-44.912zm0 29.941c2.034 0 3.969.422 5.738 1.159L239.529 264.631 39.173 90.982a14.902 14.902 0 0 1 5.738-1.159zm0 299.411H44.912c-8.26 0-14.971-6.71-14.971-14.971V122.615l199.778 173.141c2.822 2.441 6.316 3.655 9.81 3.655s6.988-1.213 9.81-3.655l199.778-173.141v251.649c-.001 8.26-6.711 14.97-14.971 14.97z"
                                            data-original="#000000" />
                                    </svg>
                                </div>
                                <a href="mailto:ntnguyen0310@gmail.com" class="text-[#007bff] text-sm ml-4">
                                    <small class="block">Mail</small>
                                    <strong> ntnguyen0310@gmail.com</strong>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-12">
                        <h2 class="text-gray-800 text-base font-bold">Socials</h2>

                        <ul class="flex mt-4 space-x-4">
                            <li class="bg-[#e6e6e6cf] h-10 w-10 rounded-full flex items-center justify-center shrink-0">
                                <a href="https://www.facebook.com/harry.ntnguyen" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0,0,256,256">
                                        <g fill="#f2be65" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(5.12,5.12)"><path d="M25,3c-12.13844,0 -22,9.86156 -22,22c0,11.01913 8.12753,20.13835 18.71289,21.72852l1.14844,0.17383v-17.33594h-5.19727v-3.51953h5.19727v-4.67383c0,-2.87808 0.69065,-4.77363 1.83398,-5.96289c1.14334,-1.18926 2.83269,-1.78906 5.18359,-1.78906c1.87981,0 2.61112,0.1139 3.30664,0.19922v2.88086h-2.44727c-1.38858,0 -2.52783,0.77473 -3.11914,1.80664c-0.59131,1.03191 -0.77539,2.264 -0.77539,3.51953v4.01758h6.12305l-0.54492,3.51953h-5.57812v17.36523l1.13477,-0.1543c10.73582,-1.45602 19.02148,-10.64855 19.02148,-21.77539c0,-12.13844 -9.86156,-22 -22,-22zM25,5c11.05756,0 20,8.94244 20,20c0,9.72979 -6.9642,17.7318 -16.15625,19.5332v-12.96875h5.29297l1.16211,-7.51953h-6.45508v-2.01758c0,-1.03747 0.18982,-1.96705 0.50977,-2.52539c0.31994,-0.55834 0.62835,-0.80078 1.38477,-0.80078h4.44727v-6.69141l-0.86719,-0.11719c-0.59979,-0.08116 -1.96916,-0.27148 -4.43945,-0.27148c-2.7031,0 -5.02334,0.73635 -6.625,2.40234c-1.60166,1.66599 -2.39258,4.14669 -2.39258,7.34961v2.67383h-5.19727v7.51953h5.19727v12.9043c-9.04433,-1.91589 -15.86133,-9.84626 -15.86133,-19.4707c0,-11.05756 8.94244,-20 20,-20z"></path></g></g>
                                    </svg>
                                </a>
                            </li>
                            <li class="bg-[#e6e6e6cf] h-10 w-10 rounded-full flex items-center justify-center shrink-0">
                                <a href="https://www.linkedin.com/in/nguyen0310/" target="_blank"   >
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="25" height="25" viewBox="0,0,256,256">
                                        <g fill="#f2be65" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(5.12,5.12)"><path d="M41,4h-32c-2.76,0 -5,2.24 -5,5v32c0,2.76 2.24,5 5,5h32c2.76,0 5,-2.24 5,-5v-32c0,-2.76 -2.24,-5 -5,-5zM17,20v19h-6v-19zM11,14.47c0,-1.4 1.2,-2.47 3,-2.47c1.8,0 2.93,1.07 3,2.47c0,1.4 -1.12,2.53 -3,2.53c-1.8,0 -3,-1.13 -3,-2.53zM39,39h-6c0,0 0,-9.26 0,-10c0,-2 -1,-4 -3.5,-4.04h-0.08c-2.42,0 -3.42,2.06 -3.42,4.04c0,0.91 0,10 0,10h-6v-19h6v2.56c0,0 1.93,-2.56 5.81,-2.56c3.97,0 7.19,2.73 7.19,8.26z"></path></g></g>
                                    </svg>
                                </a>
                            </li>
                            <li class="bg-[#e6e6e6cf] h-10 w-10 rounded-full flex items-center justify-center shrink-0">
                                <a href="https://zalo.me/0981710031">
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="25" height="25" viewBox="0,0,250,250">
                                        <g fill="#f2be65" fill-rule="nonzero" stroke="none" stroke-width="1"
                                           stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10"
                                           stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(5.12,5.12)"><path d="M9,4c-2.74952,0 -5,2.25048 -5,5v32c0,2.74952 2.25048,5 5,5h32c2.74952,0 5,-2.25048 5,-5v-32c0,-2.74952 -2.25048,-5 -5,-5zM9,6h6.58008c-3.57109,3.71569 -5.58008,8.51808 -5.58008,13.5c0,5.16 2.11016,10.09984 5.91016,13.83984c0.12,0.21 0.21977,1.23969 -0.24023,2.42969c-0.29,0.75 -0.87023,1.72961 -1.99023,2.09961c-0.43,0.14 -0.70969,0.56172 -0.67969,1.01172c0.03,0.45 0.36078,0.82992 0.80078,0.91992c2.87,0.57 4.72852,-0.2907 6.22852,-0.9707c1.35,-0.62 2.24133,-1.04047 3.61133,-0.48047c2.8,1.09 5.77938,1.65039 8.85938,1.65039c4.09369,0 8.03146,-0.99927 11.5,-2.88672v3.88672c0,1.66848 -1.33152,3 -3,3h-32c-1.66848,0 -3,-1.33152 -3,-3v-32c0,-1.66848 1.33152,-3 3,-3zM33,15c0.55,0 1,0.45 1,1v9c0,0.55 -0.45,1 -1,1c-0.55,0 -1,-0.45 -1,-1v-9c0,-0.55 0.45,-1 1,-1zM18,16h5c0.36,0 0.70086,0.19953 0.88086,0.51953c0.17,0.31 0.15875,0.69977 -0.03125,1.00977l-4.04883,6.4707h3.19922c0.55,0 1,0.45 1,1c0,0.55 -0.45,1 -1,1h-5c-0.36,0 -0.70086,-0.19953 -0.88086,-0.51953c-0.17,-0.31 -0.15875,-0.69977 0.03125,-1.00977l4.04883,-6.4707h-3.19922c-0.55,0 -1,-0.45 -1,-1c0,-0.55 0.45,-1 1,-1zM27.5,19c0.61,0 1.17945,0.16922 1.68945,0.44922c0.18,-0.26 0.46055,-0.44922 0.81055,-0.44922c0.55,0 1,0.45 1,1v5c0,0.55 -0.45,1 -1,1c-0.35,0 -0.63055,-0.18922 -0.81055,-0.44922c-0.51,0.28 -1.07945,0.44922 -1.68945,0.44922c-1.93,0 -3.5,-1.57 -3.5,-3.5c0,-1.93 1.57,-3.5 3.5,-3.5zM38.5,19c1.93,0 3.5,1.57 3.5,3.5c0,1.93 -1.57,3.5 -3.5,3.5c-1.93,0 -3.5,-1.57 -3.5,-3.5c0,-1.93 1.57,-3.5 3.5,-3.5zM27.5,21c-0.10375,0 -0.20498,0.01131 -0.30273,0.03125c-0.19551,0.03988 -0.37754,0.11691 -0.53711,0.22461c-0.15957,0.1077 -0.2966,0.24473 -0.4043,0.4043c-0.10769,0.15957 -0.18473,0.3416 -0.22461,0.53711c-0.01994,0.09775 -0.03125,0.19898 -0.03125,0.30273c0,0.10375 0.01131,0.20498 0.03125,0.30273c0.01994,0.09775 0.04805,0.19149 0.08594,0.28125c0.03789,0.08977 0.08482,0.17607 0.13867,0.25586c0.05385,0.07979 0.11578,0.15289 0.18359,0.2207c0.06781,0.06781 0.14092,0.12975 0.2207,0.18359c0.15957,0.10769 0.3416,0.18473 0.53711,0.22461c0.09775,0.01994 0.19898,0.03125 0.30273,0.03125c0.10375,0 0.20498,-0.01131 0.30273,-0.03125c0.68428,-0.13959 1.19727,-0.7425 1.19727,-1.46875c0,-0.83 -0.67,-1.5 -1.5,-1.5zM38.5,21c-0.10375,0 -0.20498,0.01131 -0.30273,0.03125c-0.09775,0.01994 -0.19149,0.04805 -0.28125,0.08594c-0.08977,0.03789 -0.17607,0.08482 -0.25586,0.13867c-0.07979,0.05385 -0.15289,0.11578 -0.2207,0.18359c-0.13562,0.13563 -0.24648,0.29703 -0.32227,0.47656c-0.03789,0.08976 -0.066,0.1835 -0.08594,0.28125c-0.01994,0.09775 -0.03125,0.19898 -0.03125,0.30273c0,0.10375 0.01131,0.20498 0.03125,0.30273c0.01994,0.09775 0.04805,0.19149 0.08594,0.28125c0.03789,0.08977 0.08482,0.17607 0.13867,0.25586c0.05385,0.07979 0.11578,0.15289 0.18359,0.2207c0.06781,0.06781 0.14092,0.12975 0.2207,0.18359c0.07979,0.05385 0.16609,0.10078 0.25586,0.13867c0.08976,0.03789 0.1835,0.066 0.28125,0.08594c0.09775,0.01994 0.19898,0.03125 0.30273,0.03125c0.10375,0 0.20498,-0.01131 0.30273,-0.03125c0.68428,-0.13959 1.19727,-0.7425 1.19727,-1.46875c0,-0.83 -0.67,-1.5 -1.5,-1.5z"></path></g></g>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <form class="ml-auo space-y-4">
                    <input type='text' placeholder='Tên'
                           class="w-full text-gray-800 rounded-md py-2.5 px-4 border text-sm outline-blue-500" required />
                    <input type='email' placeholder='Email'
                           class="w-full text-gray-800 rounded-md py-2.5 px-4 border text-sm outline-blue-500"  required/>
                    <input type='text' placeholder='Tiêu đề'
                           class="w-full text-gray-800 rounded-md py-2.5 px-4 border text-sm outline-blue-500" required />
                    <textarea placeholder='Nội dung' rows="6"
                              class="w-full text-gray-800 rounded-md px-4 border text-sm pt-2.5 outline-blue-500" required></textarea>
                    <button type='submit'
                            class="text-white bg-salmon hover:bg-salmon-600 rounded-md text-sm px-4 py-3 w-full !mt-6">Gửi</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.appclient>
