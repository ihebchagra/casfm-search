<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recherche CASFM</title>
        <script src="casfm.js?v=1"></script>
        <script src="sections.js?v=1"></script>
        <script src="https://cdn.jsdelivr.net/npm/minisearch@7.1.1/dist/umd/index.min.js"></script>
        <script src="//unpkg.com/alpinejs" defer></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            function highlightKeywords(keywords, text, snippetLength = 300) {
                let lowerText = text.toLowerCase();
                let startIndex = 0;
                let endIndex = text.length;
                let matchFound = false;
                for (let i = 0; i < keywords.length; i++) {
                    const keyword = keywords[i].toLowerCase();
                    if (keyword.length <= 2) {
                        continue;
                    }
                    const index = lowerText.indexOf(keyword, startIndex);
                    if (index !== -1) {
                        if (!matchFound) {
                            startIndex = Math.max(0, index - 20);
                            matchFound = true;
                        }
                        endIndex = Math.min(text.length, index + keyword.length + 20);
                        startIndex = Math.max(0, endIndex - snippetLength);
                    }
                }
                if (!matchFound) {
                    return text.slice(0, snippetLength) + (text.length > snippetLength ? '...' : '');
                }
                let snippet = text.slice(startIndex, endIndex);
                if (startIndex > 0) snippet = '...' + snippet;
                if (endIndex < text.length) snippet += '...';
                keywords.forEach((keyword) => {
                        if (keyword.length <= 2) {
                            return;
                        }
                        const regex = new RegExp(keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
                        snippet = snippet.replace(regex, ' <strong>$&</strong>');
                        });
                    return snippet;
                }
                const minisearch = new MiniSearch({
                    fields: ['chapter', 'page', 'content', 'id', 'subchapter'],
                    storeFields: ['chapter', 'page', 'content', 'id', 'subchapter'],
                    searchOptions: {
                        boost: {
                            subchapter: 2
                        },
                        fuzzy: 0.3,
                        prefix: true
                    }
                });
                minisearch.addAll(casfm_pages);
        </script>
    </head>
    <body class="bg-slate-50">
        <div x-data="{
                    searchTerm: '',
                    pages: casfm_pages,
                    chapters: casfm_chapters,
                    results :[],
                    search() {
                    this.results = minisearch.search(this.searchTerm);
                    }
				}" class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl font-serif text-indigo-900 mb-8">Recherche CASFM</h1>
                <p class="text-gray-700 mb-8 font-serif">Cherchez simplement dans les recommandations CASFM comme vous le feriez sur Google. Tapez un mot ou une phrase pour trouver rapidement les informations microbiologiques dont vous avez besoin.</p>
                <div class="mb-6">
                    <input type="text" x-model="searchTerm" @input="search" placeholder="Rechercher dans le document..." class="w-full px-4 py-2 rounded border border-indigo-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-serif">
                </div>
                <div class="space-y-4">
                    <template x-if="results.length === 0">
                        <div class="space-y-4">
                            <template x-for="chapter in chapters" :key="chapter.title">
                                <div class="bg-white p-6 rounded-lg shadow-sm border border-indigo-100">
                                    <a :href="'viewer.php#' + chapter.page">
                                        <h2 x-text="chapter.title" class="text-xl font-serif text-indigo-900 mb-2"></h2>
                                        <p class="text-gray-700">Page <span x-text="chapter.page"></span>
                                        </p>
                                    </a>
                                    <template x-if="chapter.subchapters">
                                        <div class="ml-4 mt-2 space-y-2">
                                            <template x-for="subchapter in chapter.subchapters" :key="subchapter.title">
                                                <div class="border-l-2 border-indigo-200 pl-4">
                                                    <a :href="'viewer.php#' + subchapter.page">
                                                        <h3 x-text="subchapter.title" class="text-lg font-serif text-indigo-800"></h3>
                                                        <p class="text-gray-700">Page <span x-text="subchapter.page"></span>
                                                        </p>
                                                    </a>
                                                    <template x-if="subchapter.subsections">
                                                        <div class="ml-4 mt-2 space-y-2">
                                                            <template x-for="subsection in subchapter.subsections" :key="subsection.title">
                                                                <div class="border-l-2 border-indigo-100 pl-4">
                                                                    <a :href="'viewer.php#' + subsection.page">
                                                                        <h4 x-text="subsection.title" class="text-md font-serif text-indigo-700"></h4>
                                                                        <p class="text-gray-700">Page <span x-text="subsection.page"></span>
                                                                        </p>
                                                                    </a>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="results.length > 0">
                        <template x-for="result in results" :key="result.id">
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-indigo-100">
                                <a :href="'viewer.php#' + result.page">
                                    <h2 x-text="result.chapter" class="text-xl font-serif text-indigo-900 mb-2"></h2>
                                    <div class="border-l-2 border-indigo-200 pl-4 mt-2">
                                        <h3 x-text="result.subchapter" class="text-lg font-serif text-indigo-800"></h3>
                                        <div class="border-l-2 border-indigo-100 pl-4 mt-2">
                                            <h4 x-text="result.subsection" class="text-md font-serif text-indigo-700"></h4>
                                            <p class="text-gray-700 mt-2 font-serif" x-html="highlightKeywords(searchTerm.split(' '), result.content)"></p>
                                        </div>
                                    </div>
                                    <p class="text-gray-700 text-sm pt-1 italic">Page <span x-text="result.page"></span>
                                </a>
                            </div>
                        </template>
                    </template>
                </div>
            </div>
        </div>
        <footer class="bg-indigo-50 mt-16 py-8">
            <div class="container mx-auto px-4 max-w-4xl">
                <div class="text-center text-sm text-gray-600 space-y-2">
                    <p>&copy; 2024 Iheb Chagra. Tous droits réservés.</p>
                        <p>Licence <a href="https://www.gnu.org/licenses/gpl-3.0.fr.html" class="text-indigo-600 hover:text-indigo-800">GNU General Public License v3.0</a></p>
                            <p>Ce site est Open-Source sur <a href="https://github.com/ihebchagra/recherche-casfm" class="text-indigo-600 hover:text-indigo-800">Github</a></p>
                            <p>Contact: <a href="mailto:ihebchagra@gmail.com" class="text-indigo-600 hover:text-indigo-800">ihebchagra@gmail.com</a></p>
                            <p>Médecin Résident en Microbiologie, Hôpitaux de Tunisie</p>
                </div>
            </div>
        </footer>
    </body>
</html>