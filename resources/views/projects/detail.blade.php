<x-partials.layout>
    {{-- hero-section page --}}
    <section
        class="bg-no-repeat bg-center bg-cover bg-[#FFF6F0] h-[350px] lg:h-[513px] flex flex-wrap items-center relative before:absolute before:inset-0 before:content-[''] before:bg-[#000000] before:opacity-[70%]"
        style="background-image: url('{{ asset($data->bannger_image ?? "assets/images/breadcrumb/bg-1.png") }}');">
        <div class="container">
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <div class="max-w-[600px]  mx-auto text-center text-white relative z-[1]">
                        <div class="mb-5"><span class="text-base block">{{ $data->project_name }}</span></div>
                        <h1
                            class="font-lora text-[36px] sm:text-[50px] md:text-[68px] lg:text-[50px] leading-tight xl:text-2xl font-medium">
                            {{ $data->city_name }} by {{ $data->developer_name }}
                        </h1>

                        <p class="text-base mt-5 max-w-[500px] mx-auto text-center">
                            {!! $data->description !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Hero section end --}}

    {{-- Popular Properties start --}}
    <section class="popular-properties py-[80px] lg:py-[120px]">
        <div class="container">
            <div class="grid grid-cols-12 mb-[-30px] gap-[30px] xl:gap-[50px]">
                <div class="col-span-12 md:col-span-6 lg:col-span-8 mb-[30px]">
                    <div class="grid grid-cols-12 mb-[30px] gap-[30px] items-center">
                        <div class="col-span-4 lg:col-span-6">
                            <ul class="grid-tab-menu flex flex-wrap">
                                <li data-grid="grid" class="mr-[10px] leading-none active">
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
                                <li data-grid="list" class="leading-none">
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
                        </div>
                        <div class="col-span-8 lg:col-span-6 text-right selectricc-border-none">
                            <span class="text-primary">Sort by:</span>
                            <select name="sort_direction" id="select"
                                class="bg-white text-[#9C9C9C] text[14px] capitalize cursor-pointer nice-select sorting-select">
                                <option value="" disabled selected>Default Order</option>
                                <option value="asc">Ascending</option>
                                <option value="desc">Descending</option>
                            </select>
                        </div>
                    </div>

                    {{-- Properties Grid Container --}}
                    <div id="grid" class="grid grid-tab-content active">
                        <div class="col-span-12">
                            <div class="grid sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 gap-[30px]">
                                @forelse($properties as $property)
                                    <div class="swiper-slide">
                                        <div
                                            class="overflow-hidden rounded-md drop-shadow-[0px_0px_5px_rgba(0,0,0,0.1)] bg-[#FFFDFC] text-center transition-all duration-300 hover:-translate-y-[10px]">
                                            <div class="relative">
                                                <a href="{{ route('project.properties.show', [$project->slug, $property->slug]) }}"
                                                    class="block">
                                                    <img src="{{ asset($property->image ?? 'assets/images/properties/properties4.jpg') }}"
                                                        class="w-full h-[260px] object-cover" loading="lazy"
                                                        alt="{{ $property->name }}">
                                                </a>
                                                <span
                                                    class="absolute bottom-5 left-5 bg-[#FFFDFC] p-[5px] rounded-[2px] text-primary leading-none text-[14px] font-normal capitalize">
                                                    {{ $property->propertyType->name }}
                                                </span>
                                            </div>

                                            <div class="py-[20px] px-[20px] text-left">
                                                <h3>
                                                    <a href="{{ route('project.properties.show', [$project->slug, $property->slug]) }}"
                                                        class="font-lora leading-tight text-[22px] xl:text-[26px] text-primary hover:text-secondary transition-all font-medium">
                                                        {{ $property->name }}
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
                                                        {{ $property->address ?? $property->city->name }}
                                                    </span>
                                                </h4>
                                                <p class="text-gray-600 text-sm mt-3 leading-relaxed">
                                                    {{ Str::limit($property->description, 110) }}
                                                </p>
                                                <ul
                                                    class="flex flex-wrap items-center justify-between text-[12px] mt-[10px] mb-[15px] pb-[10px] border-b border-[#E0E0E0]">
                                                    <li
                                                        class="flex flex-wrap items-center pr-[15px] border-r border-[#E0DEDE]">
                                                        <span>Size: <b>{{ $property->size ?? 0 }} Sq.ft</b></span>
                                                    </li>
                                                    <li
                                                        class="flex flex-wrap items-center pr-[15px] border-r border-[#E0DEDE]">
                                                        <span>Beds: <b>{{ $property->bedrooms ?? 0 }}</b></span>
                                                    </li>
                                                    <li class="flex flex-wrap items-center">
                                                        <span>Baths: <b>{{ $property->bathrooms ?? 0 }}</b></span>
                                                    </li>
                                                </ul>

                                                <ul>
                                                    <li class="flex flex-wrap items-center justify-between">
                                                        <span
                                                            class="font-lora text-base text-primary leading-none font-medium">
                                                            Price: {{ $property->formatted_price }}
                                                        </span>
                                                        <span class="flex flex-wrap items-center">
                                                            <a href="{{ route('project.properties.show', [$project->slug, $property->slug]) }}"
                                                                class="inline-flex items-center gap-1 text-xs text-secondary hover:text-primary font-medium transition-colors">
                                                                View Unit
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
                                    </div>
                                @empty
                                    <div
                                        class="col-span-2 text-center py-12 bg-white rounded border border-gray-100 shadow-sm">
                                        <p class="text-gray-500 font-light">No properties found matching your search.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Pagination --}}
                    <div class="grid grid-cols-12 mt-[50px] gap-[30px]">
                        <div class="col-span-12">
                            @if ($properties->hasPages())
                                <ul class="pagination flex flex-wrap items-center justify-center">
                                    {{-- Previous Page Link --}}
                                    @if ($properties->onFirstPage())
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
                                                href="{{ $properties->previousPageUrl() }}">
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
                                    @for ($i = 1; $i <= $properties->lastPage(); $i++)
                                        @if ($i == $properties->currentPage())
                                            <li class="mx-2">
                                                <span
                                                    class="flex flex-wrap items-center justify-center w-[26px] h-[26px] bg-secondary rounded-full text-white leading-none text-[12px] font-medium">{{ $i }}</span>
                                            </li>
                                        @else
                                            <li class="mx-2">
                                                <a class="flex flex-wrap items-center justify-center w-[26px] h-[26px] leading-none hover:text-secondary text-[12px]"
                                                    href="{{ $properties->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endif
                                    @endfor

                                    {{-- Next Page Link --}}
                                    @if ($properties->hasMorePages())
                                        <li class="mx-2">
                                            <a class="flex flex-wrap items-center justify-center w-[26px] h-[26px] bg-primary rounded-full text-orange leading-none transition-all hover:bg-secondary text-white text-[12px]"
                                                href="{{ $properties->nextPageUrl() }}">
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

                {{-- Sidebar Column --}}
                <div class="col-span-12 md:col-span-6 lg:col-span-4 mb-[30px]">
                    <aside class="mb-[-60px] asidebar">

                        {{-- Search Filter Box --}}
                        <div class="mb-[60px]">
                            <h3 class="text-primary leading-none text-[24px] font-lora underline mb-[40px] font-medium">
                                Projects Search <span class="text-secondary">.</span>
                            </h3>

                            <form action="{{ route('project.properties.index', [$project->slug]) }}" method="get"
                                class="relative">

                                {{-- Search Text --}}
                                <div class="relative mb-[25px] bg-white">
                                    <input
                                        class="font-light w-full leading-[1.75] placeholder:opacity-100 placeholder:text-body border border-primary border-opacity-60 rounded-[8px] pl-[40px] pr-[20px] py-[8px] focus:border-secondary focus:border-opacity-60 focus:outline-none focus:drop-shadow-[0px_6px_15px_rgba(0,0,0,0.1)] bg-white"
                                        type="text" placeholder="search" name="q" value="{{ request()->q ?? '' }}">
                                    <svg class="absolute top-1/2 -translate-y-1/2 z-[1] left-[20px] pointer-events-none"
                                        width="14" height="14" viewBox="0 0 14 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5.39648 6.41666H8.60482" stroke="#016450" stroke-width="1.5"
                                            stroke-linecap="round" />
                                        <path d="M7 8.02083V4.8125" stroke="#016450" stroke-width="1.5"
                                            stroke-linecap="round" />
                                        <path
                                            d="M2.11231 4.9525C3.26148 -0.0991679 10.7456 -0.0933345 11.889 4.95833C12.5598 7.92167 10.7165 10.43 9.10064 11.9817C7.92814 13.1133 6.07314 13.1133 4.89481 11.9817C3.28481 10.43 1.44148 7.91583 2.11231 4.9525Z"
                                            stroke="#0B2C3D" stroke-width="1.5" />
                                    </svg>
                                </div>

                                {{-- Property Type Dropdown --}}
                                <div class="relative mb-[25px] bg-white selectricc-border-none">
                                    <svg class="absolute top-1/2 -translate-y-1/2 z-[1] left-[20px] pointer-events-none"
                                        width="13" height="13" viewBox="0 0 13 13" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_928_754)">
                                            <path
                                                d="M4.64311 0H0V4.64311H4.64311V0ZM3.71437 3.71437H0.928741V0.928741H3.71437V3.71437Z"
                                                fill="#0B2C3D" />
                                            <path
                                                d="M8.35742 0V4.64311H13.0005V0H8.35742ZM12.0718 3.71437H9.28616V0.928741H12.0718V3.71437Z"
                                                fill="#0B2C3D" />
                                            <path
                                                d="M0 13H4.64311V8.35689H0V13ZM0.928741 9.28563H3.71437V12.0713H0.928741V9.28563Z"
                                                fill="#0B2C3D" />
                                            <path
                                                d="M8.35742 13H13.0005V8.35689H8.35742V13ZM9.28616 9.28563H12.0718V12.0713H9.28616V9.28563Z"
                                                fill="#0B2C3D" />
                                            <path
                                                d="M6.96437 0H6.03563V6.03563H0V6.96437H6.03563V13H6.96437V6.96437H13V6.03563H6.96437V0Z"
                                                fill="#0B2C3D" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_928_754">
                                                <rect width="13" height="13" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <select name="property_type_id"
                                        class="font-light w-full border border-primary border-opacity-60 rounded-[8px] pl-[40px] pr-[20px] py-[10px] focus:outline-none bg-white cursor-pointer appearance-none text-[14px]">
                                        <option value="">Property Type</option>
                                        @foreach ($propertyTypes as $type)
                                            <option value="{{ $type->id }}" {{ request()->property_type_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- City Dropdown --}}
                                <div class="relative mb-[25px] bg-white selectricc-border-none">
                                    <svg class="absolute top-1/2 -translate-y-1/2 z-[1] left-[20px] pointer-events-none"
                                        width="14" height="14" viewBox="0 0 14 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.16602 12.8333H12.8327" stroke="#0B2C3D" stroke-width="1.5"
                                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M1.7207 12.8333L1.74987 5.81583C1.74987 5.45999 1.91904 5.12169 2.19904 4.90002L6.28237 1.72085C6.70237 1.39418 7.29154 1.39418 7.71737 1.72085L11.8007 4.89418C12.0865 5.11585 12.2499 5.45416 12.2499 5.81583V12.8333"
                                            stroke="#0B2C3D" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M9.04232 6.41666H4.95898C4.47482 6.41666 4.08398 6.8075 4.08398 7.29166V12.8333H9.91732V7.29166C9.91732 6.8075 9.52648 6.41666 9.04232 6.41666Z"
                                            stroke="#0B2C3D" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M5.83398 9.47916V10.3542" stroke="#0B2C3D" stroke-width="1.5"
                                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M6.125 4.375H7.875" stroke="#0B2C3D" stroke-width="1.5"
                                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <select name="city_id"
                                        class="font-light w-full border border-primary border-opacity-60 rounded-[8px] pl-[40px] pr-[20px] py-[10px] focus:outline-none bg-white cursor-pointer appearance-none text-[14px]">
                                        <option value="">City</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" {{ request()->city_id == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Projects Dropdown --}}
                                <div class="relative mb-[25px] bg-white selectricc-border-none">
                                    <svg class="absolute top-1/2 -translate-y-1/2 z-[1] left-[20px] pointer-events-none"
                                        width="14" height="14" viewBox="0 0 14 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.16602 12.8333H12.8327" stroke="#0B2C3D" stroke-width="1.5"
                                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M1.7207 12.8333L1.74987 5.81583C1.74987 5.45999 1.91904 5.12169 2.19904 4.90002L6.28237 1.72085C6.70237 1.39418 7.29154 1.39418 7.71737 1.72085L11.8007 4.89418C12.0865 5.11585 12.2499 5.45416 12.2499 5.81583V12.8333"
                                            stroke="#0B2C3D" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M9.04232 6.41666H4.95898C4.47482 6.41666 4.08398 6.8075 4.08398 7.29166V12.8333H9.91732V7.29166C9.91732 6.8075 9.52648 6.41666 9.04232 6.41666Z"
                                            stroke="#0B2C3D" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M5.83398 9.47916V10.3542" stroke="#0B2C3D" stroke-width="1.5"
                                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M6.125 4.375H7.875" stroke="#0B2C3D" stroke-width="1.5"
                                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <select name="project_id"
                                        class="font-light w-full border border-primary border-opacity-60 rounded-[8px] pl-[40px] pr-[20px] py-[10px] focus:outline-none bg-white cursor-pointer appearance-none text-[14px]">
                                        <option value="">Projects</option>
                                        @foreach ($projects as $proj)
                                            <option value="{{ $proj->id }}" {{ $project->id == $proj->id || request()->project_id == $proj->id ? 'selected' : '' }}>
                                                {{ $proj->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit"
                                    class="block w-full z-[1] text-center before:rounded-md before:block before:absolute before:left-auto before:right-0 before:inset-y-0 before:z-[-1] before:bg-secondary before:w-0 hover:before:w-full hover:before:left-0 hover:before:right-auto before:transition-all leading-none px-[30px] py-[12px] capitalize font-medium text-white text-[14px] xl:text-[16px] relative after:block after:absolute after:inset-0 after:z-[-2] after:bg-primary after:rounded-md after:transition-all">Search</button>

                            </form>
                        </div>

                        {{-- Featured Projects Carousel --}}
                        <div class="mb-[60px]">
                            <h3 class="text-primary leading-none text-[24px] font-lora underline mb-[40px] font-medium">
                                Featured Projects<span class="text-secondary">.</span>
                            </h3>
                            <div class="sidebar-carousel relative">
                                <div class="swiper p-1">
                                    <div class="swiper-wrapper">
                                        @foreach($featured_projects as $project)
                                            <div class="swiper-slide">
                                                <div
                                                    class="overflow-hidden rounded-md drop-shadow-[0px_2px_3px_rgba(0,0,0,0.1)] bg-[#FFFDFC] text-center mb-[40px]">
                                                    <div class="relative">
                                                        <a href="{{ route('project.properties.index', $project->slug) }}"
                                                            class="block">
                                                            <img src="{{ asset($project->image ?? 'assets/images/properties/propertie-slider-1.png') }}"
                                                                class="w-full h-[220px] object-cover" loading="lazy"
                                                                alt="{{ $project->name }}">
                                                        </a>
                                                    </div>

                                                    <div class="pt-[15px] pb-[20px] px-[20px] text-left">
                                                        <h3>
                                                            <a href="{{ route('project.properties.index', $project->slug) }}"
                                                                class="font-lora leading-tight text-[18px] text-primary hover:text-secondary font-medium">
                                                                {{ $project->name }}
                                                            </a>
                                                        </h3>
                                                        <h4 class="leading-none mt-1">
                                                            <span
                                                                class="font-light text-[14px] leading-[1.75] text-gray-500 underline">
                                                                {{ $project->location ?? $project->city->name }}
                                                            </span>
                                                        </h4>
                                                        <ul class="mt-[10px]">
                                                            <li class="flex flex-wrap items-center justify-between">
                                                                <span
                                                                    class="font-lora text-[14px] text-secondary leading-none">
                                                                    Starting: {{ $project->formatted_starting_price }}
                                                                </span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center justify-center mt-[25px]">
                                    <div
                                        class="swiper-button-prev w-[26px] h-[26px] rounded-full bg-primary text-white hover:bg-secondary static mx-[5px] mt-[0px]">
                                    </div>
                                    <div
                                        class="swiper-button-next w-[26px] h-[26px] rounded-full bg-primary text-white hover:bg-secondary static mx-[5px] mt-[0px]">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Our Agents --}}
                        <div class="mb-[60px]">
                            <h3 class="text-primary leading-none text-[24px] font-lora underline mb-[30px] font-medium">
                                Our Agents<span class="text-secondary">.</span>
                            </h3>

                            @php
                                // Get agents assigned directly to this project
                                $project_agents = \App\Models\Agent::where('project_id', $project->id)
                                    ->where('status', true)->get();

                                // If no assigned agents, get 2 random agents
                                if ($project_agents->isEmpty()) {
                                    $project_agents = \App\Models\Agent::where('status', true)
                                        ->inRandomOrder()->take(2)->get();
                                }
                            @endphp

                            <div class="grid sm:grid-cols-2 lg:grid-cols-2 gap-x-[20px] mb-[-20px]">
                                @forelse($project_agents->take(2) as $agent)
                                <div class="text-center group mb-[30px]">
                                    <div class="relative z-[1] rounded-[6px_6px_0px_0px]">
                                        <a href="javascript:void(0)"
                                            class="block relative before:absolute before:content-[''] before:inset-x-0 before:bottom-0 before:bg-[#016450] before:w-full before:h-[calc(100%_-_30px)] before:z-[-1] before:rounded-[6px_6px_0px_0px]">
                                            <img src="{{ asset($agent->image ?? 'assets/images/team/person3.png') }}"
                                                class="w-full object-contain block mx-auto" loading="lazy" width="130"
                                                height="154" alt="{{ $agent->name }}">
                                        </a>
                                    </div>
                                    <div
                                        class="bg-[#FFFDFC] drop-shadow-[0px_2px_15px_rgba(0,0,0,0.1)] rounded-[0px_0px_6px_6px] px-[10px] pt-[5px] pb-[15px] border-b-[6px] border-primary transition-all duration-700 group-hover:border-secondary">
                                        <h3><a href="javascript:void(0)"
                                                class="font-lora text-[14px] text-primary hover:text-secondary">{{ $agent->name }}</a></h3>
                                        <p class="font-light text-[12px] leading-none capitalize mt-[5px]">{{ $agent->designation ?? 'Real Estate Agent' }}</p>
                                        @if($agent->whatsapp)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $agent->whatsapp) }}" target="_blank" class="inline-flex items-center gap-1 mt-2 text-[11px] text-secondary hover:text-primary">
                                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L32 503l138.4-36.2c32.5 17.7 68.9 27 106.3 27 122.4 0 222-99.6 222-222 0-59.3-23.2-115-65.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-8.2-4.8-82 21.5 21.8-80-5.3-8.5c-18.4-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6z"/></svg>
                                                WhatsApp
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="col-span-2 text-center py-6">
                                    <p class="text-gray-400 text-sm">No agents available.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Tags
                        <!-- <!-- <div class="mb-[60px]"> -->
                        <h3 class="text-primary leading-none text-[24px] font-lora underline mb-[40px] font-medium">
                            Tags<span class="text-secondary">.</span>
                        </h3>
                        <ul class="flex flex-wrap my-[-7px] mx-[-5px] font-light text-[12px]">
                            <li class="my-[7px] mx-[5px]"><a href="#"
                                    class="leading-none border border-[#E0E0E0] py-[8px] px-[10px] block rounded-[4px] hover:text-secondary">Real
                                    Estate</a>
                            </li>
                            <li class="my-[7px] mx-[5px]"><a href="#"
                                    class="leading-none border border-[#E0E0E0] py-[8px] px-[10px] block rounded-[4px] hover:text-secondary">Appartment</a>
                            </li>
                            <li class="my-[7px] mx-[5px]"><a href="#"
                                    class="leading-none border border-[#E0E0E0] py-[8px] px-[10px] block rounded-[4px] hover:text-secondary">Sale
                                    Property</a>
                            </li>
                            <li class="my-[7px] mx-[5px]"><a href="#"
                                    class="leading-none border border-[#E0E0E0] py-[8px] px-[10px] block rounded-[4px] hover:text-secondary">Duplex</a>
                            </li>
                            <li class="my-[7px] mx-[5px]"><a href="#"
                                    class="leading-none border border-[#E0E0E0] py-[8px] px-[10px] block rounded-[4px] hover:text-secondary">Buy
                                    Property</a>
                            </li>
                            <li class="my-[7px] mx-[5px]"><a href="#"
                                    class="leading-none border border-[#E0E0E0] py-[8px] px-[10px] block rounded-[4px] hover:text-secondary">Houses</a>
                            </li>
                        </ul>
                </div> -->--}}
                </aside>
            </div>
        </div>
        </div>
    </section>
    <!-- Popular Properties end -->

    <x-partials.news-letter-section />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && window.jQuery.fn.selectric) {
                window.jQuery('.nice-select').selectric({
                    onChange: function (element) {
                        window.jQuery(element).change();
                    }
                });
            }
        });
    </script>
</x-partials.layout>