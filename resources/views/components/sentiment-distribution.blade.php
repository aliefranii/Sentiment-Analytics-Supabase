<div class="flex flex-col lg:flex-row gap-4">
    <!-- Doughnut Chart -->
    <div class="w-full bg-white rounded-lg shadow-md py-4 md:py-6">
        <div class="flex justify-between items-start w-full px-4 mb-2">
            <div>
                <p class="text-sm font-bold text-gray-900">Sentiment Distribution</p>
                <span class="text-sm text-gray-500">Overall sentiment breakdown across all sources</span>
            </div>

            <!-- Dropdown Unik untuk Doughnut -->
            <div class="relative bg-gray-200 rounded-md p-2">
                <button id="sentimentDropdownBtn" data-dropdown-toggle="sentimentDropdownMenu" type="button"
                    class="text-sm font-medium text-gray-500 hover:text-gray-900 inline-flex items-center">
                    This Month
                    <svg class="w-2.5 h-2.5 ms-1.5" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <div id="sentimentDropdownMenu"
                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 mt-2">
                    <ul class="py-2 text-sm text-gray-700">
                        <li><a href="#" data-range="today"
                                class="range-option-doughnut block px-4 py-2 hover:bg-blue-500 hover:text-white">Today</a>
                        </li>
                        <li><a href="#" data-range="this_week"
                                class="range-option-doughnut block px-4 py-2 hover:bg-blue-500 hover:text-white">This
                                Week</a>
                        </li>
                        <li><a href="#" data-range="this_month"
                                class="range-option-doughnut block px-4 py-2 hover:bg-blue-500 hover:text-white">This
                                Month</a>
                        </li>
                        <li><a href="#" data-range="this_year"
                                class="range-option-doughnut block px-4 py-2 hover:bg-blue-500 hover:text-white">This
                                Year</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <div class="flex flex-col items-center justify-center">
            <div class="w-[300px] h-[300px] relative">
                <canvas id="sentimentChart" class="w-full h-full"></canvas>
            </div>

            <div id="custom-legend" class="flex flex-wrap justify-center gap-6 mt-4 text-xs text-gray-700 font-medium">
            </div>
        </div>
    </div>

    <!-- Bar Chart -->
    <div class="w-full bg-white rounded-lg shadow-md py-4 md:py-6">
        <div class="flex justify-between items-start w-full px-4 mb-2">
            <div>
                <p class="text-sm font-bold text-gray-900">Sentiment By Source</p>
                <span class="text-sm text-gray-500">Sentiment analysis across different platforms</span>
            </div>

            <!-- Dropdown Unik untuk Bar -->
            <div class="relative bg-gray-200 rounded-md p-2">
                <button id="barDropdownBtn" data-dropdown-toggle="barDropdownMenu" type="button"
                    class="text-sm font-medium text-gray-500 hover:text-gray-900 inline-flex items-center">
                    This Month
                    <svg class="w-2.5 h-2.5 ms-1.5" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <div id="barDropdownMenu"
                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 mt-2">
                    <ul class="py-2 text-sm text-gray-700">
                        <li><a href="#" data-range="today"
                                class="range-option-bar block px-4 py-2 hover:bg-blue-500 hover:text-white">Today</a>
                        </li>
                        <li><a href="#" data-range="this_week"
                                class="range-option-bar block px-4 py-2 hover:bg-blue-500 hover:text-white">This
                                Week</a>
                        </li>
                        <li><a href="#" data-range="this_month"
                                class="range-option-bar block px-4 py-2 hover:bg-blue-500 hover:text-white">This
                                Month</a>
                        </li>
                        <li><a href="#" data-range="this_year"
                                class="range-option-bar block px-4 py-2 hover:bg-blue-500 hover:text-white">This
                                Year</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <!-- Tambahkan scroll horizontal untuk mobile -->
        <div class="overflow-x-auto px-4">
            <div class="min-w-[600px] h-[300px]">
                <canvas id="sentimentBarChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
</div>
