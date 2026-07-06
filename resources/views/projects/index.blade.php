<x-partials.layout>
    <!-- Hero section start -->
    <section
        class="bg-no-repeat bg-center bg-cover bg-[#E9F1FF] h-[350px] lg:h-[513px] flex flex-wrap items-center relative before:absolute before:inset-0 before:content-[''] before:bg-[#000000] before:opacity-[70%]"
        style="background-image: url('{{ asset('assets/images/breadcrumb/bg-1.png') }}');">
        <div class="container">
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <div class="max-w-[600px]  mx-auto text-center text-white relative z-[1]">
                        <div class="mb-5">
                            <span class="text-base block">
                                @if(isset($city))
                                    {{ $city->name }}
                                @endif
                            </span>
                        </div>
                        <h1
                            class="font-lora text-[36px] sm:text-[50px] md:text-[68px] lg:text-[50px] leading-tight xl:text-2xl font-medium">
                            Projects
                        </h1>
                        <p class="text-base mt-5 max-w-[500px] mx-auto text-center">
                            Huge number of propreties availabe here for buy and sell also you can find here co-living
                            property as you like
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero section end -->

    <!-- Popular Properties start -->
    <section class="popular-properties py-[80px] lg:py-[120px]">
        <div class="container">
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <div
                        class="flex lg:justify-between lg:flex-row flex-col-reverse justify-start items-start lg:items-center mb-10">
                        <div class="flex flex-row items-center">
                            <ul class="grid-tab-menu flex flex-wrap">
                                <li data-grid="grid" class="mr-[10px] leading-none active flex">
                                    <button class="leading-none capitalize transition-all text-[16px] ease-out"
                                        aria-label="Grid View">
                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_901_7333)">
                                                <path
                                                    d="M4.37474 0H0.874735C0.391831 0 0 0.391831 0 0.874735V4.37474C0 4.85764 0.391831 5.24947 0.874735 5.24947H4.37474C4.85764 5.24947 5.24947 4.85764 5.24947 4.37474V0.874735C5.25053 0.391831 4.85764 0 4.37474 0Z"
                                                    fill="currentcolor" />
                                                <path
                                                    d="M4.37474 7.87474H0.874735C0.391831 7.87474 0 8.26657 0 8.75053V12.2505C0 12.7334 0.391831 13.1253 0.874735 13.1253H4.37474C4.85764 13.1253 5.24947 12.7334 5.24947 12.2505V8.75053C5.25053 8.26657 4.85764 7.87474 4.37474 7.87474Z"
                                                    fill="currentcolor" />
                                                <path
                                                    d="M4.37474 15.7505H0.874735C0.391831 15.7505 0 16.1424 0 16.6253V20.1253C0 20.6082 0.391831 21 0.874735 21H4.37474C4.85764 21 5.24947 20.6082 5.24947 20.1253V16.6253C5.25053 16.1424 4.85764 15.7505 4.37474 15.7505Z"
                                                    fill="currentcolor" />
                                                <path
                                                    d="M12.2497 0H8.74973C8.26683 0 7.875 0.391831 7.875 0.874735V4.37474C7.875 4.85764 8.26683 5.24947 8.74973 5.24947H12.2497C12.7326 5.24947 13.1245 4.85764 13.1245 4.37474V0.874735C13.1245 0.391831 12.7326 0 12.2497 0Z"
                                                    fill="currentcolor" />
                                                <path
                                                    d="M12.2497 7.87474H8.74973C8.26683 7.87474 7.875 8.26657 7.875 8.74948V12.2495C7.875 12.7324 8.26683 13.1242 8.74973 13.1242H12.2497C12.7326 13.1242 13.1245 12.7324 13.1245 12.2495V8.75054C13.1245 8.26657 12.7326 7.87474 12.2497 7.87474Z"
                                                    fill="currentcolor" />
                                                <path
                                                    d="M12.2497 15.7505H8.74973C8.26683 15.7505 7.875 16.1424 7.875 16.6253V20.1253C7.875 20.6082 8.26683 21 8.74973 21H12.2497C12.7326 21 13.1245 20.6082 13.1245 20.1253V16.6253C13.1245 16.1424 12.7326 15.7505 12.2497 15.7505Z"
                                                    fill="currentcolor" />
                                                <path
                                                    d="M20.1247 0H16.6247C16.1418 0 15.75 0.391831 15.75 0.874735V4.37474C15.75 4.85764 16.1418 5.24947 16.6247 5.24947H20.1247C20.6076 5.24947 20.9995 4.85764 20.9995 4.37474V0.874735C20.9995 0.391831 20.6076 0 20.1247 0Z"
                                                    fill="currentcolor" />
                                                <path
                                                    d="M20.1247 7.87474H16.6247C16.1418 7.87474 15.75 8.26657 15.75 8.74948V12.2495C15.75 12.7324 16.1418 13.1242 16.6247 13.1242H20.1247C20.6076 13.1242 20.9995 12.7324 20.9995 12.2495V8.75054C20.9995 8.26657 20.6076 7.87474 20.1247 7.87474Z"
                                                    fill="currentcolor" />
                                                <path
                                                    d="M20.1247 15.7505H16.6247C16.1418 15.7505 15.75 16.1424 15.75 16.6253V20.1253C15.75 20.6082 16.1418 21 16.6247 21H20.1247C20.6076 21 20.9995 20.6082 20.9995 20.1253V16.6253C20.9995 16.1424 20.6076 15.7505 20.1247 15.7505Z"
                                                    fill="currentcolor" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_901_7333">
                                                    <rect width="21" height="21" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </button>
                                </li>
                                <li data-grid="list" class="leading-none flex">
                                    <button
                                        class="leading-none capitalize text-primary hover:text-secondary transition-all text-[16px] ease-out"
                                        aria-label="List View">
                                        <svg width="25" height="19" viewBox="0 0 25 19" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M23.7525 18.6641H7.03597C6.34482 18.6641 5.78906 18.1017 5.78906 17.4052C5.78906 16.71 6.34611 16.1462 7.03597 16.1462H23.7525C24.4411 16.1462 24.9994 16.71 24.9994 17.4052C24.9994 18.103 24.4411 18.6641 23.7525 18.6641Z"
                                                fill="currentcolor" />
                                            <path
                                                d="M23.7525 10.7602H7.03597C6.34482 10.7602 5.78906 10.1965 5.78906 9.5013C5.78906 8.80608 6.34611 8.24236 7.03597 8.24236H23.7525C24.4411 8.24236 24.9994 8.80608 24.9994 9.5013C24.9994 10.1965 24.4411 10.7602 23.7525 10.7602Z"
                                                fill="currentcolor" />
                                            <path
                                                d="M23.7525 2.85378H7.03597C6.34482 2.85378 5.78906 2.29005 5.78906 1.59483C5.78906 0.899617 6.34611 0.335892 7.03597 0.335892H23.7525C24.4411 0.335892 24.9994 0.899617 24.9994 1.59483C24.9994 2.29005 24.4411 2.85378 23.7525 2.85378Z"
                                                fill="currentcolor" />
                                            <path
                                                d="M3.35001 1.69248C3.35001 2.62594 2.60084 3.38235 1.67629 3.38235C0.749175 3.38235 0 2.62594 0 1.69248C0 0.759011 0.749175 0 1.67629 0C2.60084 0 3.35001 0.759011 3.35001 1.69248Z"
                                                fill="currentcolor" />
                                            <path
                                                d="M3.35001 9.5013C3.35001 10.4348 2.60084 11.1912 1.67629 11.1912C0.750464 11.1912 0 10.4348 0 9.5013C0 8.56783 0.749175 7.80882 1.67629 7.80882C2.60084 7.80752 3.35001 8.56653 3.35001 9.5013Z"
                                                fill="currentcolor" />
                                            <path
                                                d="M3.35001 17.3088C3.35001 18.2423 2.60084 18.9987 1.67629 18.9987C0.750464 18.9987 0 18.2423 0 17.3088C0 16.3754 0.749175 15.6163 1.67629 15.6163C2.60084 15.6163 3.35001 16.3754 3.35001 17.3088Z"
                                                fill="currentcolor" />
                                        </svg>
                                    </button>
                                </li>
                            </ul>
                            <div class="ml-3 selectricc-border-none">
                                <span class="text-primary">Sort by:</span>
                                <select
                                    class="bg-white text-[#9C9C9C] text[14px] capitalize cursor-pointer nice-select">
                                    <option value="0" selected>Default Order</option>
                                    <option value="1">A to Z</option>
                                    <option value="2">Z to A</option>
                                    <option value="3">All</option>
                                </select>
                            </div>
                        </div>
                        <ul class="all-properties flex flex-wrap lg:pt-[10px]">
                            <li data-tab="all-properties"
                                class="mr-[30px] md:mr-[45px] mb-4 lg:mb-0 leading-none active">
                                <button
                                    class="leading-none capitalize text-primary hover:text-secondary transition-all text-[16px] ease-out">
                                    All Projects
                                </button>
                            </li>
                            @foreach($types as $type)
                                <li data-tab="{{ $type->slug }}" class="mr-[30px] md:mr-[45px] mb-4 lg:mb-0 leading-none">
                                    <button
                                        class="leading-none capitalize text-primary hover:text-secondary transition-all text-[16px] ease-out">
                                        {{ $type->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Grid View Tab Content --}}
                    <div class="grid grid-tab-content active">
                        <div class="col-span-12">

                            {{-- All Projects --}}
                            <div class="all-properties properties-tab-content active">
                                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-[30px]">
                                    @forelse($projects as $project)
                                        <div
                                            class="overflow-hidden rounded-md drop-shadow-[0px_0px_5px_rgba(0,0,0,0.1)] bg-[#FFFDFC] text-center transition-all duration-300 hover:-translate-y-[10px]">
                                            <div class="relative">
                                                <a href="{{ route('project.properties.index', $project->slug) }}"
                                                    class="block">
                                                    <img src="{{ asset($project->image ?? 'assets/images/properties/properties1.png') }}"
                                                        class="w-full h-[260px] object-cover" loading="lazy"
                                                        alt="{{ $project->name }}">
                                                </a>
                                                @if($project->completion_year)
                                                    <span
                                                        class="absolute bottom-5 left-5 bg-[#FFFDFC] p-[5px] rounded-[2px] text-primary leading-none text-[14px] font-normal capitalize">
                                                        Est. {{ $project->completion_year }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="py-[20px] px-[20px] text-left">
                                                <h3>
                                                    <a href="{{ route('project.properties.index', $project->slug) }}"
                                                        class="font-lora leading-tight text-[22px] xl:text-[26px] text-primary hover:text-secondary transition-all font-medium">
                                                        {{ $project->name }}
                                                    </a>
                                                </h3>
                                                <h4>
                                                    <span
                                                        class="font-light text-[14px] leading-[1.75] text-gray-500 flex items-center gap-1">
                                                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"
                                                            class="inline text-secondary">
                                                            <path
                                                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                                        </svg>
                                                        {{ $project->location ?? $project->city->name }}
                                                    </span>
                                                </h4>
                                                <p class="text-gray-600 text-sm mt-3 leading-relaxed">
                                                    {{ Str::limit($project->description, 110) }}
                                                </p>
                                                <ul
                                                    class="flex flex-wrap items-center justify-between text-[12px] mt-[15px] mb-[15px] pb-[10px] border-b border-[#E0E0E0]">
                                                    <li
                                                        class="flex flex-wrap items-center pr-[15px] border-r border-[#E0DEDE]">
                                                        <span class="font-medium text-gray-500">Developer:</span>
                                                        <span
                                                            class="ml-1 text-primary font-semibold">{{ $project->developer ?? 'N/A' }}</span>
                                                    </li>
                                                    <li class="flex flex-wrap items-center pl-[15px]">
                                                        <span class="font-medium text-gray-500">City:</span>
                                                        <span
                                                            class="ml-1 text-primary font-semibold">{{ $project->city->name }}</span>
                                                    </li>
                                                </ul>

                                                <ul>
                                                    <li class="flex flex-wrap items-center justify-between">
                                                        <span
                                                            class="font-lora text-base text-primary leading-none font-medium">
                                                            Starting: {{ $project->formatted_starting_price }}
                                                        </span>
                                                        <span class="flex flex-wrap items-center">
                                                            <a href="{{ route('project.properties.index', $project->slug) }}"
                                                                class="inline-flex items-center gap-1 text-xs text-secondary hover:text-primary font-medium transition-colors">
                                                                View Project
                                                                <svg width="14" height="10" viewBox="0 0 26 11"
                                                                    fill="currentColor">
                                                                    <path
                                                                        d="M20.0877 0.69303L24.2075 5.00849H0V5.99151H24.2075L20.0877 10.307L20.7493 11L26 5.5L20.7493 0L20.0877 0.69303Z" />
                                                                </svg>
                                                            </a>
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-span-3 text-center py-12">
                                            <p class="text-gray-500">No projects available.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Dynamic Category Grids --}}
                            @foreach($types as $type)
                                <div class="{{ $type->slug }} properties-tab-content">
                                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-[30px]">
                                        @php
                                            $filtered = $projects->filter(function ($p) use ($type) {
                                                return $p->properties->where('property_type_id', $type->id)->count() > 0;
                                            });
                                        @endphp
                                        @forelse($filtered as $project)
                                            <div
                                                class="overflow-hidden rounded-md drop-shadow-[0px_0px_5px_rgba(0,0,0,0.1)] bg-[#FFFDFC] text-center transition-all duration-300 hover:-translate-y-[10px]">
                                                <div class="relative">
                                                    <a href="{{ route('project.properties.index', $project->slug) }}"
                                                        class="block">
                                                        <img src="{{ asset($project->image ?? 'assets/images/properties/properties1.png') }}"
                                                            class="w-full h-[260px] object-cover" loading="lazy"
                                                            alt="{{ $project->name }}">
                                                    </a>
                                                    @if($project->completion_year)
                                                        <span
                                                            class="absolute bottom-5 left-5 bg-[#FFFDFC] p-[5px] rounded-[2px] text-primary leading-none text-[14px] font-normal capitalize">
                                                            Est. {{ $project->completion_year }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="py-[20px] px-[20px] text-left">
                                                    <h3>
                                                        <a href="{{ route('project.properties.index', $project->slug) }}"
                                                            class="font-lora leading-tight text-[22px] xl:text-[26px] text-primary hover:text-secondary transition-all font-medium">
                                                            {{ $project->name }}
                                                        </a>
                                                    </h3>
                                                    <h4>
                                                        <span
                                                            class="font-light text-[14px] leading-[1.75] text-gray-500 flex items-center gap-1">
                                                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"
                                                                class="inline text-secondary">
                                                                <path
                                                                    d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                                            </svg>
                                                            {{ $project->location ?? $project->city->name }}
                                                        </span>
                                                    </h4>
                                                    <p class="text-gray-600 text-sm mt-3 leading-relaxed">
                                                        {{ Str::limit($project->description, 110) }}
                                                    </p>
                                                    <ul
                                                        class="flex flex-wrap items-center justify-between text-[12px] mt-[15px] mb-[15px] pb-[10px] border-b border-[#E0E0E0]">
                                                        <li
                                                            class="flex flex-wrap items-center pr-[15px] border-r border-[#E0DEDE]">
                                                            <span class="font-medium text-gray-500">Developer:</span>
                                                            <span
                                                                class="ml-1 text-primary font-semibold">{{ $project->developer ?? 'N/A' }}</span>
                                                        </li>
                                                        <li class="flex flex-wrap items-center pl-[15px]">
                                                            <span class="font-medium text-gray-500">City:</span>
                                                            <span
                                                                class="ml-1 text-primary font-semibold">{{ $project->city->name }}</span>
                                                        </li>
                                                    </ul>

                                                    <ul>
                                                        <li class="flex flex-wrap items-center justify-between">
                                                            <span
                                                                class="font-lora text-base text-primary leading-none font-medium">
                                                                Starting: {{ $project->formatted_starting_price }}
                                                            </span>
                                                            <span class="flex flex-wrap items-center">
                                                                <a href="{{ route('project.properties.index', $project->slug) }}"
                                                                    class="inline-flex items-center gap-1 text-xs text-secondary hover:text-primary font-medium transition-colors">
                                                                    View Project
                                                                    <svg width="14" height="10" viewBox="0 0 26 11"
                                                                        fill="currentColor">
                                                                        <path
                                                                            d="M20.0877 0.69303L24.2075 5.00849H0V5.99151H24.2075L20.0877 10.307L20.7493 11L26 5.5L20.7493 0L20.0877 0.69303Z" />
                                                                    </svg>
                                                                </a>
                                                            </span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-span-3 text-center py-12">
                                                <p class="text-gray-500">No projects found in this category.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    {{-- List View Tab Content --}}
                    <div class="list grid-tab-content">
                        <div class="col-span-12">

                            {{-- All Projects (List) --}}
                            <div class="all-properties properties-tab-content active">
                                <div class="grid grid-cols-1 gap-[30px]">
                                    @forelse($projects as $project)
                                        <div
                                            class="overflow-hidden rounded-md text-center transition-all duration-300 drop-shadow-[0px_2px_5px_rgba(0,0,0,0.1)] bg-[#FFFDFC] hover:-translate-y-[10px] flex flex-wrap flex-col md:flex-row items-center">
                                            <div class="relative mb-[15px] lg:mb-[0px] block w-full lg:w-[300px] shrink-0">
                                                <a href="{{ route('project.properties.index', $project->slug) }}"
                                                    class="block h-[250px]">
                                                    <img src="{{ asset($project->image ?? 'assets/images/properties/properties4.jpg') }}"
                                                        class="w-full h-full rounded-tl-[6px] lg:rounded-bl-[6px] object-cover"
                                                        loading="lazy" alt="{{ $project->name }}">
                                                </a>
                                                @if($project->completion_year)
                                                    <span
                                                        class="absolute bottom-5 left-5 bg-[#FFFDFC] p-[5px] rounded-[2px] text-primary leading-none text-[14px] font-normal">
                                                        Est. {{ $project->completion_year }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="flex flex-col relative w-full lg:w-[calc(100%-300px)] py-5 px-6">
                                                <div class="text-left w-full">
                                                    <h3>
                                                        <a href="{{ route('project.properties.index', $project->slug) }}"
                                                            class="font-lora leading-tight text-[22px] xl:text-[26px] text-primary hover:text-secondary transition-all font-medium">
                                                            {{ $project->name }}
                                                        </a>
                                                    </h3>
                                                    <h4>
                                                        <span
                                                            class="font-light text-tiny text-gray-500 flex items-center gap-1">
                                                            <svg width="12" height="12" fill="currentColor"
                                                                viewBox="0 0 24 24" class="inline text-secondary">
                                                                <path
                                                                    d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                                            </svg>
                                                            {{ $project->location ?? $project->city->name }}
                                                        </span>
                                                    </h4>
                                                    <p class="text-gray-600 text-sm mt-3 leading-relaxed">
                                                        {{ Str::limit($project->description, 180) }}
                                                    </p>
                                                    <ul
                                                        class="flex flex-wrap items-center justify-between text-[12px] mt-[15px] mb-[10px] pt-[10px] border-t border-[#E0E0E0]">
                                                        <li
                                                            class="flex flex-wrap items-center pr-5 border-r border-[#E0DEDE]">
                                                            <span class="font-medium text-gray-500">Developer:</span>
                                                            <span
                                                                class="ml-1 text-primary font-semibold">{{ $project->developer ?? 'N/A' }}</span>
                                                        </li>
                                                        <li class="flex flex-wrap items-center pl-5">
                                                            <span class="font-medium text-gray-500">City:</span>
                                                            <span
                                                                class="ml-1 text-primary font-semibold">{{ $project->city->name }}</span>
                                                        </li>
                                                    </ul>
                                                    <div class="flex items-center justify-between mt-3">
                                                        <span
                                                            class="font-lora text-base text-primary leading-none font-medium">
                                                            Starting: {{ $project->formatted_starting_price }}
                                                        </span>
                                                        <a href="{{ route('project.properties.index', $project->slug) }}"
                                                            class="inline-flex items-center gap-1 text-xs text-secondary hover:text-primary font-medium transition-colors">
                                                            View Project
                                                            <svg width="14" height="10" viewBox="0 0 26 11"
                                                                fill="currentColor">
                                                                <path
                                                                    d="M20.0877 0.69303L24.2075 5.00849H0V5.99151H24.2075L20.0877 10.307L20.7493 11L26 5.5L20.7493 0L20.0877 0.69303Z" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-span-1 text-center py-12">
                                            <p class="text-gray-500">No projects available.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Dynamic Category Lists --}}
                            @foreach($types as $type)
                                <div class="{{ $type->slug }} properties-tab-content">
                                    <div class="grid grid-cols-1 gap-[30px]">
                                        @php
                                            $filtered = $projects->filter(function ($p) use ($type) {
                                                return $p->properties->where('property_type_id', $type->id)->count() > 0;
                                            });
                                        @endphp
                                        @forelse($filtered as $project)
                                            <div
                                                class="overflow-hidden rounded-md text-center transition-all duration-300 drop-shadow-[0px_2px_5px_rgba(0,0,0,0.1)] bg-[#FFFDFC] hover:-translate-y-[10px] flex flex-wrap flex-col md:flex-row items-center">
                                                <div class="relative mb-[15px] lg:mb-[0px] block w-full lg:w-[300px] shrink-0">
                                                    <a href="{{ route('project.properties.index', $project->slug) }}"
                                                        class="block h-[250px]">
                                                        <img src="{{ asset($project->image ?? 'assets/images/properties/properties4.jpg') }}"
                                                            class="w-full h-full rounded-tl-[6px] lg:rounded-bl-[6px] object-cover"
                                                            loading="lazy" alt="{{ $project->name }}">
                                                    </a>
                                                    @if($project->completion_year)
                                                        <span
                                                            class="absolute bottom-5 left-5 bg-[#FFFDFC] p-[5px] rounded-[2px] text-primary leading-none text-[14px] font-normal">
                                                            Est. {{ $project->completion_year }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="flex flex-col relative w-full lg:w-[calc(100%-300px)] py-5 px-6">
                                                    <div class="text-left w-full">
                                                        <h3>
                                                            <a href="{{ route('project.properties.index', $project->slug) }}"
                                                                class="font-lora leading-tight text-[22px] xl:text-[26px] text-primary hover:text-secondary transition-all font-medium">
                                                                {{ $project->name }}
                                                            </a>
                                                        </h3>
                                                        <h4>
                                                            <span
                                                                class="font-light text-tiny text-gray-500 flex items-center gap-1">
                                                                <svg width="12" height="12" fill="currentColor"
                                                                    viewBox="0 0 24 24" class="inline text-secondary">
                                                                    <path
                                                                        d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                                                </svg>
                                                                {{ $project->location ?? $project->city->name }}
                                                            </span>
                                                        </h4>
                                                        <p class="text-gray-600 text-sm mt-3 leading-relaxed">
                                                            {{ Str::limit($project->description, 180) }}
                                                        </p>
                                                        <ul
                                                            class="flex flex-wrap items-center justify-between text-[12px] mt-[15px] mb-[10px] pt-[10px] border-t border-[#E0E0E0]">
                                                            <li
                                                                class="flex flex-wrap items-center pr-5 border-r border-[#E0DEDE]">
                                                                <span class="font-medium text-gray-500">Developer:</span>
                                                                <span
                                                                    class="ml-1 text-primary font-semibold">{{ $project->developer ?? 'N/A' }}</span>
                                                            </li>
                                                            <li class="flex flex-wrap items-center pl-5">
                                                                <span class="font-medium text-gray-500">City:</span>
                                                                <span
                                                                    class="ml-1 text-primary font-semibold">{{ $project->city->name }}</span>
                                                            </li>
                                                        </ul>
                                                        <div class="flex items-center justify-between mt-3">
                                                            <span
                                                                class="font-lora text-base text-primary leading-none font-medium">
                                                                Starting: {{ $project->formatted_starting_price }}
                                                            </span>
                                                            <a href="{{ route('project.properties.index', $project->slug) }}"
                                                                class="inline-flex items-center gap-1 text-xs text-secondary hover:text-primary font-medium transition-colors">
                                                                View Project
                                                                <svg width="14" height="10" viewBox="0 0 26 11"
                                                                    fill="currentColor">
                                                                    <path
                                                                        d="M20.0877 0.69303L24.2075 5.00849H0V5.99151H24.2075L20.0877 10.307L20.7493 11L26 5.5L20.7493 0L20.0877 0.69303Z" />
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-span-1 text-center py-12">
                                                <p class="text-gray-500">No projects found in this category.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    {{-- Dynamic Pagination --}}
                    <div class="grid grid-cols-12 mt-[50px] gap-[30px]">
                        <div class="col-span-12">
                            @if ($projects->hasPages())
                                <ul class="pagination flex flex-wrap items-center justify-center">
                                    {{-- Previous Page Link --}}
                                    @if ($projects->onFirstPage())
                                        <li class="mx-2 opacity-50">
                                            <span
                                                class="flex flex-wrap items-center justify-center w-[26px] h-[26px] bg-primary rounded-full text-orange leading-none text-[12px] cursor-not-allowed">
                                                <svg width="6" height="11" viewBox="0 0 6 11" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M5.8853 10.0592C5.7326 10.212 5.48474 10.212 5.33204 10.0592L0.636322 5.36134C0.48362 5.20856 0.48362 4.96059 0.636322 4.80782L5.33204 0.109909C5.48749 -0.0403413 5.73535 -0.0359829 5.8853 0.119544C6.03181 0.271171 6.03181 0.511801 5.8853 0.663428L1.46633 5.08446L5.8853 9.50573C6.03823 9.65873 6.03823 9.90648 5.8853 10.0592Z"
                                                        fill="white" />
                                                </svg>
                                            </span>
                                        </li>
                                    @else
                                        <li class="mx-2">
                                            <a class="flex flex-wrap items-center justify-center w-[26px] h-[26px] bg-primary rounded-full text-orange leading-none transition-all hover:bg-secondary text-white text-[12px]"
                                                href="{{ $projects->previousPageUrl() }}">
                                                <svg width="6" height="11" viewBox="0 0 6 11" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M5.8853 10.0592C5.7326 10.212 5.48474 10.212 5.33204 10.0592L0.636322 5.36134C0.48362 5.20856 0.48362 4.96059 0.636322 4.80782L5.33204 0.109909C5.48749 -0.0403413 5.73535 -0.0359829 5.8853 0.119544C6.03181 0.271171 6.03181 0.511801 5.8853 0.663428L1.46633 5.08446L5.8853 9.50573C6.03823 9.65873 6.03823 9.90648 5.8853 10.0592Z"
                                                        fill="white" />
                                                </svg>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Page Numbers --}}
                                    @for ($i = 1; $i <= $projects->lastPage(); $i++)
                                        @if ($i == $projects->currentPage())
                                            <li class="mx-2">
                                                <span
                                                    class="flex flex-wrap items-center justify-center w-[26px] h-[26px] bg-secondary rounded-full text-white leading-none text-[12px] font-medium">{{ $i }}</span>
                                            </li>
                                        @else
                                            <li class="mx-2">
                                                <a class="flex flex-wrap items-center justify-center w-[26px] h-[26px] leading-none hover:text-secondary text-[12px]"
                                                    href="{{ $projects->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endif
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($projects->hasMorePages())
                                        <li class="mx-2">
                                            <a class="flex flex-wrap items-center justify-center w-[26px] h-[26px] bg-primary rounded-full text-orange leading-none transition-all hover:bg-secondary text-white text-[12px]"
                                                href="{{ $projects->nextPageUrl() }}">
                                                <svg width="6" height="11" viewBox="0 0 6 11" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M0.114699 10.0592C0.267401 10.212 0.515257 10.212 0.667959 10.0592L5.36368 5.36134C5.51638 5.20856 5.51638 4.96059 5.36368 4.80782L0.667959 0.109909C0.512505 -0.0403413 0.26465 -0.0359829 0.114699 0.119544C-0.031813 0.271171 -0.031813 0.511801 0.114699 0.663428L4.53367 5.08446L0.114699 9.50573C-0.038233 9.65873 -0.038233 9.90648 0.114699 10.0592Z"
                                                        fill="white" />
                                                </svg>
                                            </a>
                                        </li>
                                    @else
                                        <li class="mx-2 opacity-50">
                                            <span
                                                class="flex flex-wrap items-center justify-center w-[26px] h-[26px] bg-primary rounded-full text-orange leading-none text-[12px] cursor-not-allowed">
                                                <svg width="6" height="11" viewBox="0 0 6 11" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M0.114699 10.0592C0.267401 10.212 0.515257 10.212 0.667959 10.0592L5.36368 5.36134C5.51638 5.20856 5.51638 4.96059 5.36368 4.80782L0.667959 0.109909C0.512505 -0.0403413 0.26465 -0.0359829 0.114699 0.119544C-0.031813 0.271171 -0.031813 0.511801 0.114699 0.663428L4.53367 5.08446L0.114699 9.50573C-0.038233 9.65873 -0.038233 9.90648 0.114699 10.0592Z"
                                                        fill="white" />
                                                </svg>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Popular Properties end -->
    <x-partials.news-letter-section />
</x-partials.layout>