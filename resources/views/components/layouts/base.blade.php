@props([
    'darkMode' => false,
    'docSearch' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />

    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <x-seo::meta />

    {{-- Favicon --}}
    <link rel="stylesheet" href="https://unpkg.com/@highlightjs/cdn-assets@11.9.0/styles/default.min.css">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png?v=w1dBNxT7Wg" />
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png?v=w1dBNxT7Wg" />
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png?v=w1dBNxT7Wg" />
    <link rel="manifest" href="/favicon/site.webmanifest?v=w1dBNxT7Wg" />
    <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg?v=w1dBNxT7Wg" color="#fdae4b" />
    <link rel="shortcut icon" href="/favicon/favicon.ico?v=w1dBNxT7Wg" />
    <meta name="msapplication-TileColor" content="#ffc40d" />
    <meta name="msapplication-config" content="/favicon/browserconfig.xml?v=w1dBNxT7Wg" />
    <meta name="theme-color" content="#ffffff" />


    <!-- Fonts -->
    @googlefonts

    <!-- Styles -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @livewireStyles
    @filamentStyles
    @vite('resources/css/app.css')

    <!-- Scripts -->
    @livewireScripts
    @vite('resources/js/app.js')
    @stack('scripts')
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
    <script>
        hljs.highlightAll();
        const copyButtonLabel = "Copy Code";

        // use a class selector if available
        let blocks = document.querySelectorAll("pre");

        blocks.forEach((block) => {
            // only add button if browser supports Clipboard API
            if (navigator.clipboard) {
                let button = document.createElement("button");
                button.className = "copyBtn";

                // Create SVG element
                let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                svg.setAttribute("width", "24");
                svg.setAttribute("height", "24");
                svg.setAttribute("viewBox", "0 0 24 24");
                svg.setAttribute("fill", "none");
                svg.setAttribute("stroke", "currentColor");
                svg.setAttribute("stroke-width", "2");
                svg.setAttribute("stroke-linecap", "round");
                svg.setAttribute("stroke-linejoin", "round");
                svg.classList.add("copySvg");

                // Add a copy icon (you can replace this path with another icon)
                let path = document.createElementNS("http://www.w3.org/2000/svg", "path");
                path.setAttribute("d",
                    "M8 2H16C16.55 2 17 2.45 17 3V15C17 15.55 16.55 16 16 16H8C7.45 16 7 15.55 7 15V3C7 2.45 7.45 2 8 2ZM5 5H3C2.45 5 2 5.45 2 6V20C2 20.55 2.45 21 3 21H13C13.55 21 14 20.55 14 20V18"
                );
                svg.appendChild(path);

                // Append the SVG into the button
                button.appendChild(svg);
                block.insertBefore(button, block.firstChild);

                // Add click event to copy the code
                button.addEventListener("click", async () => {
                    await copyCode(block, button);
                });
            }
        });

        async function copyCode(block, button) {
            let code = block.querySelector("code");
            let text = code.innerText;

            await navigator.clipboard.writeText(text);

            // visual feedback that task is completed
            button.querySelector("svg").style.fill = "green"; // Change color of SVG

            setTimeout(() => {
                button.querySelector("svg").style.fill = ""; // Reset to original color
            }, 700);
        }
    </script>
</head>

<body
    class="relative min-h-screen overflow-x-clip bg-cream font-vietnam text-midnight antialiased selection:bg-stone-500/10">
    <div id="docsearch" class="hidden"></div>

    {{ $slot }}
</body>


</html>
