<div class="w-full flex justify-center items-center bg-gray-200 p-2 rounded-lg shadow-md">
    <ul class="flex space-x-2 rtl:space-x-reverse justify-center items-center">
        <li class="{{ request()->is('overview') ? 'bg-white' : 'bg-transparent' }} p-3 rounded-md">
            <a href="{{ url('/overview') }}"
                class="flex items-center {{ request()->is('overview') ? 'text-blue-500' : 'text-gray-700' }} font-normal rounded-md">
                <i class="ph-lg ph-chart-pie-slice"></i>
                <span class="text-sm ml-1">Overview</span>
            </a>
        </li>
        <li class="{{ request()->is('sources') ? 'bg-white' : 'bg-transparent' }} p-3 rounded-md">
            <a href="{{ url('/sources') }}"
                class="flex items-center {{ request()->is('sources') ? 'text-blue-500' : 'text-gray-700' }} font-normal rounded-md">
                <i class="ph-lg ph-database"></i>
                <span class="text-sm ml-1">Sources</span>
            </a>
        </li>
        <li class="{{ request()->is('trends') ? 'bg-white' : 'bg-transparent' }} p-3 rounded-md">
            <a href="{{ url('/trends') }}"
                class="flex items-center {{ request()->is('trends') ? 'text-blue-500' : 'text-gray-700' }} font-normal rounded-md">
                <i class="ph-lg ph-trend-up"></i>
                <span class="text-sm ml-1">Trends</span>
            </a>
        </li>
        <li class="{{ request()->is('mentions') ? 'bg-white' : 'bg-transparent' }} p-3 rounded-md">
            <a href="{{ url('/mentions') }}"
                class="flex items-center {{ request()->is('mentions') ? 'text-blue-500' : 'text-gray-700' }} font-normal rounded-md">
                <i class="ph-lg ph-chat-circle-text"></i>
                <span class="text-sm ml-1">Mentions</span>
            </a>
        </li>
    </ul>
</div>
