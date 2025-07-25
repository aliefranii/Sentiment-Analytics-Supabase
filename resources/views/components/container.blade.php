<div class="w-full h-[450px] bg-white rounded-lg shadow-md py-4 md:py-6 flex flex-col">
    <!-- Header -->
    <div class="px-4 mb-2 flex justify-between items-start">
        <div>
            <p class="text-sm font-bold text-gray-900">Feed Summarize AI</p>
            <span class="text-sm text-gray-500">Newest feeds summarize by Jubir AI.</span>
        </div>

        <!-- Dropdown -->
        <div class="relative bg-gray-200 rounded-md p-2 w-27">
            <button id="sourceDropdownBtn" data-dropdown-toggle="sourceDropdownMenu" type="button"
                class="text-sm font-medium text-gray-500 hover:text-gray-900 inline-flex items-center">
                This Month
                <svg class="w-2.5 h-2.5 ms-1.5" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 4 4 4-4" />
                </svg>
            </button>
            <div id="sourceDropdownMenu"
                class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-full mt-2 absolute right-0">
                <ul class="py-2 text-sm text-gray-700">
                    <li><a href="#" data-range="today"
                            class="range-option-bar block px-4 py-2 hover:bg-blue-500 hover:text-white">Today</a></li>
                    <li><a href="#" data-range="this_week"
                            class="range-option-bar block px-4 py-2 hover:bg-blue-500 hover:text-white">This Week</a>
                    </li>
                    <li><a href="#" data-range="this_month"
                            class="range-option-bar block px-4 py-2 hover:bg-blue-500 hover:text-white">This Month</a>
                    </li>
                    <li><a href="#" data-range="this_year"
                            class="range-option-bar block px-4 py-2 hover:bg-blue-500 hover:text-white">This Year</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-200 my-2"></div>

    <!-- Scrollable card area -->
    <div class="flex-1 overflow-y-auto px-4 py-2 space-y-2">
        @foreach ($feeds as $feed)
            <div class="relative bg-white border border-gray-300 rounded-md overflow-hidden p-4">
                <div class="flex flex-row space-x-4 w-full">
                    <div class="flex justify-center items-center">
                        <div
                            class="bg-white w-[64px] h-[64px] p-2 rounded-full border border-gray-300 flex justify-center items-center">
                            <img src="{{ asset('images/KabarWajo-Logo.png') }}" alt="Logo">
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1">
                        <div class="flex flex-row space-x-2">
                            <span class="inline-block bg-green-50 text-[14px] font-semibold py-1 px-2 rounded-2xl">
                                {{ $feed->source }}
                            </span>
                            <span
                                class="inline-block bg-green-200 text-green-800 text-[14px] font-semibold py-1 px-2 rounded-2xl">
                                {{ ucfirst($feed->sentimen) }}
                            </span>
                        </div>
                        <div class="w-full">
                            <!-- Tampilkan deskripsi yang terpotong -->
                            <span class="desc-text block text-sm text-gray-700" data-feed-id="{{ $feed->id }}">
                                {{ Str::limit($feed->desc, 130, '...') }}
                            </span>
                        </div>
                        <!-- Link untuk melihat deskripsi lengkap -->
                        <a href="javascript:void(0);" onclick="toggleFullDesc(event)"
                            class="text-[14px] font-semibold text-blue-500 hover:text-blue-700">Selengkapnya....</a>
                        <!-- Deskripsi penuh -->
                        <span class="full-desc text-sm text-gray-600 hidden" data-feed-id="{{ $feed->id }}">
                            {{ $feed->desc }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function toggleFullDesc(event) {
        var feedId = event.target.closest('[data-feed-id]').getAttribute('data-feed-id');
        console.log('Feed ID:', feedId); // Debugging log untuk memastikan feedId yang benar

        var descText = document.querySelector('.desc-text[data-feed-id="' + feedId + '"]');
        var fullDesc = document.querySelector('.full-desc[data-feed-id="' + feedId + '"]');

        console.log('Desc Text:', descText); // Debugging log untuk memastikan elemen ditemukan
        console.log('Full Desc:', fullDesc);

        if (!descText || !fullDesc) {
            console.error('Element not found for feedId: ' + feedId);
            return;
        }

        var link = event.target;

        if (fullDesc.classList.contains('hidden')) {
            fullDesc.classList.remove('hidden');
            descText.classList.add('hidden');
            link.textContent = "Tutup";
        } else {
            fullDesc.classList.add('hidden');
            descText.classList.remove('hidden');
            link.textContent = "Selengkapnya....";
        }
    }
</script>
