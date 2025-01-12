<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recherche CA-SFM</title>
        <script src="casfm.js?v=1"></script>
        <script src="sections.js?v=1"></script>
        <script src="https://cdn.jsdelivr.net/npm/minisearch@7.1.1/dist/umd/index.min.js"></script>
        <script src="//unpkg.com/alpinejs" defer></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link rel="manifest" href="manifest.json">
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('sw.js')
                        .then(registration => {
                            console.log('Service Worker registered');
                        })
                        .catch(err => {
                            console.log('Service Worker registration failed:', err);
                        });
                });
            }
        </script>
        <script>
        let pages = [];
        for (let i = 1; i <= 177; i++) {
            pages.push({
              src : 'pdf_pages/page_' + i + '.webp?v=1',
              width : (i >= 42 && i <= 132) || (i >= 137 && i <= 147) || (i >= 165 && i <= 177) ? 2339 : 1654,
              height : (i >= 42 && i <= 132) || (i >= 137 && i <= 147) || (i >= 165 && i <= 177) ? 1654 : 2339,
              id : i
            });
        }
        </script>
    </head>
    <body class="bg-slate-100">
        <div x-init class="relative">
            <button @click="window.history.back()" class="fixed top-2 left-2 z-20 p-2 bg-gray-300 text-black border-gray-800 dark:bg-gray-800 dark:text-white border-2 dark:border-gray-400 rounded-md focus:outline-none" style="transform: scale(1); zoom: 1;">
                <svg class="menu-icon h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>
        <div x-init class=" dosis min-h-screen" x-data="{pages : pages}">
            <template x-for="(page, index) in pages">
                <div class="relative flex items-center justify-center" :id="page.id"
                    x-init="if ($el.id == window.location.hash.substr(1)) $el.scrollIntoView()">
                    <div class="my-1">
                    <img :src="page.src" :width="page.width" :height="page.height" class="lg:max-w-7xl flashing-bg"
                        loading="lazy" />
                    </div>
                </div>
            </template>
        </div>

        <style>
            @keyframes flash {
                0% { background-color: transparent; }
                50% { background-color: rgba(0, 0, 0, 0.5); }
                100% { background-color: transparent; }
            }

            .flashing-bg {
                animation: flash 3s infinite;
            }
        </style>
    </body>
</html>
