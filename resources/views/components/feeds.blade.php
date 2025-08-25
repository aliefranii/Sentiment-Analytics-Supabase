<div class="w-full h-[450px] bg-white rounded-lg shadow-md py-4 md:py-6 flex flex-col">
    <!-- Header -->
    <div class="px-4 mb-2 flex justify-between items-start">
        <div>
            <p class="text-sm font-bold text-gray-900">Feeds Summarize and Analytics Jubir.AI</p>
            <span class="text-sm text-gray-500">Newest feeds summarize and Analytics by Jubir.AI</span>
        </div>
    </div>

    <div class="border-t border-gray-200 my-2"></div>

    <!-- Scrollable card area -->
    <div class="flex-1 overflow-y-auto px-4 py-2 space-y-2">
        @foreach ($feeds as $feed)
            {{-- Container utama diberi data-feed-id --}}
            <div class="relative bg-white border border-gray-300 rounded-md overflow-hidden p-4"
                data-feed-id="{{ $feed->id }}">
                <div class="flex flex-row space-x-4 w-full">
                    <div class="flex justify-center items-center">
                        <div
                            class="bg-white w-[64px] h-[64px] p-2 rounded-full border border-gray-300 flex justify-center items-center">
                            @php
                                $logoPath = match (strtolower($feed->source)) {
                                    'tribun-wajo' => 'images/Tribun-Logo.png',
                                    'kabar-wajo' => 'images/KabarWajo-Logo.png',
                                    'rakyatsulsel-wajo' => 'images/RakyatSulsel-Logo.png',
                                    'jurnal8-wajo' => 'images/Jurnal8Wajo-Logo.png',
                                    default => 'images/Wajo-logo.png', // Fallback jika sumber tidak dikenali
                                };
                            @endphp
                            <img src="{{ asset($logoPath) }}" alt="Logo {{ $feed->source }}">
                        </div>
                    </div>

                    <div class="flex flex-col space-y-1">
                        <div class="flex flex-row space-x-2">
                            <span class="inline-block bg-green-50 text-[14px] font-semibold py-1 px-2 rounded-2xl">
                                {{ $feed->source }}
                            </span>
                            @php
                                $sentimentColor = match (strtolower($feed->sentimen)) {
                                    'positif' => 'bg-green-200 text-green-800',
                                    'negatif' => 'bg-red-200 text-red-800',
                                    'netral' => 'bg-gray-200 text-gray-800',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span
                                class="inline-block {{ $sentimentColor }} text-[14px] font-semibold py-1 px-2 rounded-2xl">
                                {{ ucfirst($feed->sentimen) }}
                            </span>
                        </div>

                        <div class="w-full">
                            <span class="desc-text block text-sm text-gray-700">
                                {!! parseManualMarkdown(Str::limit($feed->desc, 130, '...')) !!}
                            </span>

                            <span class="full-desc text-sm text-gray-600 hidden">
                                {!! parseManualMarkdown($feed->desc) !!}
                            </span>
                        </div>

                        {{-- Toggle link --}}
                        <a href="javascript:void(0);" onclick="toggleFullDesc(event)"
                            class="text-[14px] font-semibold text-blue-500 hover:text-blue-700">
                            Selengkapnya....
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Script -->
<script>
    function toggleFullDesc(event) {
        const container = event.target.closest('[data-feed-id]');
        if (!container) return;

        const descText = container.querySelector('.desc-text');
        const fullDesc = container.querySelector('.full-desc');
        const link = event.target;

        if (!descText || !fullDesc) return;

        if (fullDesc.classList.contains('hidden')) {
            fullDesc.classList.remove('hidden');
            descText.classList.add('hidden');
            link.textContent = 'Tutup';
        } else {
            fullDesc.classList.add('hidden');
            descText.classList.remove('hidden');
            link.textContent = 'Selengkapnya....';
        }
    }
</script>
