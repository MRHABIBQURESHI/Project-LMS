<x-partials.layout :title="'Home'">
    <!-- Hero section start -->
    <section class="bg-primary relative pt-[130px] lg:pt-[80px] xl:pt-[0px] mb-[70px] lg:mb-[0px]">
        <div class="hero-slider overflow-hidden">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <!-- swiper-slide start -->
                    <div class="swiper-slide lg:h-[700px] xl:h-[950px] xs:h-[auto] flex flex-wrap items-center">
                        <div class="container">
                            <div class="grid grid-cols-12">
                                <div class="col-span-12 lg:col-span-5 xl:col-span-6">
                                    <div class="slider-content max-w-[560px] relative z-[9]">
                                        <div class="relative mb-5 sub_title">
                                            <span class="text-base text-white block">A new way to find Properties</span>
                                        </div>
                                        <h1
                                            class="font-lora text-secondary text-[36px] sm:text-[50px] md:text-[68px] lg:text-[50px] leading-tight xl:text-2xl title font-normal">
                                            <span>Find your Most Suitable Property</span>
                                        </h1>

                                        <p class="text-base text-white mt-8 mb-12 text max-w-[570px]">
                                            Huge number of propreties availabe here for buy, and sell, also you
                                            can find here co-living property, So you have lots of opportunity
                                        </p>
                                        <div class="inline-block hero_btn">
                                            <a href="{{ route('contact') }}"
                                                class="before:rounded-md before:block before:absolute before:left-auto before:right-0 before:inset-y-0 before:-z-[1] before:bg-white before:w-0 hover:before:w-full hover:before:left-0 hover:before:right-auto hover:text-primary before:transition-all leading-none px-[20px] py-[15px] capitalize font-medium text-white text-[14px] xl:text-[16px] relative after:block after:absolute after:inset-0 after:-z-[2] after:bg-secondary after:rounded-md after:transition-all block">Contact
                                                us</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-12 lg:col-span-7 xl:col-span-6">
                                    <div
                                        class="relative mt-10 -right-6 md:mt-0 lg:absolute lg:right-0 lg:bottom-0 lg:w-3/4 xl:w-fit">
                                        <img class="hero_image w-full"
                                            src="{{ asset('assets/images/hero/home-1.png') }}" width="866" height="879"
                                            alt="hero image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- swiper-slide end-->
                    <!-- swiper-slide start -->
                    <div class="swiper-slide lg:h-[700px] xl:h-[950px] xs:h-[auto] flex flex-wrap items-center">
                        <div class="container">
                            <div class="grid grid-cols-12">
                                <div class="col-span-12 lg:col-span-5 xl:col-span-6">
                                    <div class="slider-content max-w-[560px] relative z-[9]">
                                        <div class="relative mb-5 sub_title">
                                            <span class="text-base text-white block">A new way to find Properties</span>
                                        </div>
                                        <h1
                                            class="font-lora text-secondary text-[36px] sm:text-[50px] md:text-[68px] lg:text-[50px] leading-tight xl:text-2xl title font-normal">
                                            <span>Modern, Creative & Luxury Homes</span>
                                        </h1>

                                        <p class="text-base text-white mt-8 mb-12 text max-w-[570px]">
                                            Huge number of propreties availabe here for buy, and sell, also you
                                            can find here co-living property, So you have lots of opportunity
                                        </p>
                                        <div class="inline-block hero_btn">
                                            <a href="{{ route('contact') }}"
                                                class="before:rounded-md before:block before:absolute before:left-auto before:right-0 before:inset-y-0 before:-z-[1] before:bg-white before:w-0 hover:before:w-full hover:before:left-0 hover:before:right-auto hover:text-primary before:transition-all leading-none px-[20px] py-[15px] capitalize font-medium text-white text-[14px] xl:text-[16px] relative after:block after:absolute after:inset-0 after:-z-[2] after:bg-secondary after:rounded-md after:transition-all block">Contact
                                                us</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-12 lg:col-span-5 xl:col-span-6">
                                    <div
                                        class="relative mt-10 md:mt-0 -right-6 lg:absolute lg:right-0 lg:bottom-0 lg:w-3/4 xl:w-fit">
                                        <img class="hero_image w-full"
                                            src="{{ asset('assets/images/hero/home-1.png') }}" width="866" height="879"
                                            alt="hero image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- swiper-slide end-->
                </div>
            </div>

        </div>
        <span class="shape-4 absolute -bottom-[100px] left-0 scene" data-relative-input="true">
            <img data-depth="0.1" src="{{ asset('assets/images/hero/shape4.svg') }}" alt="">
        </span>
    </section>
    <!-- Hero section end -->


    <!-- Advanced Search Start -->
    <div class="mt-[80px] lg:mt-[120px] xl:mt-[-160px] relative z-[2] px-[15px] lg:px-[0px]">
        <div class="container">
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <div class="bg-white border border-solid border-[#016450] border-opacity-25 rounded-[15px] px-[20px] sm:px-[30px] py-[35px] shadow-xl">
                        <form action="{{ route('projects.index') }}" method="GET" id="advanced-search-form">
                            <div class="flex flex-wrap items-stretch gap-0 -mb-[20px]">

                                <!-- City Dropdown -->
                                <div class="flex items-center flex-1 min-w-[180px] mb-[20px] pr-4 lg:border-r lg:border-[#E0E0E0]">
                                    <div class="mr-3 shrink-0 text-secondary">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <label for="search-city" class="font-lora block capitalize text-primary text-[15px] xl:text-[18px] leading-none mb-2 font-medium">City</label>
                                        <select name="city" id="search-city"
                                            class="appearance-none bg-transparent text-[13px] font-light cursor-pointer w-full focus:outline-none text-gray-600 border-0 p-0">
                                            <option value="">All Cities</option>
                                            @foreach($cities as $city)
                                                <option value="{{ $city->slug }}" {{ request('city') == $city->slug ? 'selected' : '' }}>{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Property Type Dropdown -->
                                <div class="flex items-center flex-1 min-w-[180px] mb-[20px] px-4 lg:border-r lg:border-[#E0E0E0]">
                                    <div class="mr-3 shrink-0 text-secondary">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <label for="search-type" class="font-lora block capitalize text-primary text-[15px] xl:text-[18px] leading-none mb-2 font-medium">Property Type</label>
                                        <select name="type" id="search-type"
                                            class="appearance-none bg-transparent text-[13px] font-light cursor-pointer w-full focus:outline-none text-gray-600 border-0 p-0">
                                            <option value="">All Types</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type->slug }}" {{ request('type') == $type->slug ? 'selected' : '' }}>{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Project Dropdown -->
                                @if(isset($all_projects) && count($all_projects) > 0)
                                <div class="flex items-center flex-1 min-w-[180px] mb-[20px] px-4 lg:border-r lg:border-[#E0E0E0]">
                                    <div class="mr-3 shrink-0 text-secondary">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <label for="search-project" class="font-lora block capitalize text-primary text-[15px] xl:text-[18px] leading-none mb-2 font-medium">Project</label>
                                        <select name="project" id="search-project"
                                            class="appearance-none bg-transparent text-[13px] font-light cursor-pointer w-full focus:outline-none text-gray-600 border-0 p-0">
                                            <option value="">All Projects</option>
                                            @foreach($all_projects as $proj)
                                                <option value="{{ $proj->slug }}" {{ request('project') == $proj->slug ? 'selected' : '' }}>{{ $proj->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

                                <!-- Search Button -->
                                <div class="mb-[20px] pl-4 flex items-center justify-end shrink-0">
                                    <button type="submit"
                                        class="bg-secondary hover:bg-primary text-white font-lora font-medium text-base px-7 py-4 rounded-lg transition-all duration-300 shadow-md flex items-center gap-2 whitespace-nowrap">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        <span>Find Projects</span>
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Advanced Search End -->


    <!-- Popular Projects start -->
    <section class="popular-properties py-[80px] lg:py-[125px]">
        <div class="container">
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <div class="flex flex-col items-center justify-center mb-[50px]">
                        <span class="text-secondary text-tiny inline-block mb-2">Best Choice</span>
                        <h2 class="font-lora text-primary text-[24px] sm:text-[30px] xl:text-xl capitalize font-medium">
                            Popular <span class="text-secondary">Projects.</span></h2>
                    </div>
                </div>
            </div>

            <div class="properties-slider">
                <div class="swiper  -mx-[15px] -my-[60px] px-[15px] py-[60px]">
                    <div class="swiper-wrapper">
                        @forelse($poular_projects as $project)
                            <!-- swiper-slide start -->
                            <div class="swiper-slide">
                                <div
                                    class="overflow-hidden rounded-md drop-shadow-[0px_0px_5px_rgba(0,0,0,0.1)] bg-[#FFFDFC] text-center transition-all duration-300 hover:-translate-y-[10px]">
                                    <div class="relative">
                                        <a href="{{ route('projects.index', [$project->slug]) }}" class="block">
                                            <img src="{{ asset($project->image ?? 'assets/images/properties/properties1.png') }}"
                                                class="w-full h-[260px] object-cover" loading="lazy"
                                                alt="{{ $project->name }}">
                                        </a>
                                        @if($project->completion_year)
                                            <span
                                                class="absolute bottom-5 left-5 bg-[#FFFDFC] p-[5px] rounded-[2px] text-primary leading-none text-[14px] font-normal capitalize">Est.
                                                {{ $project->completion_year }}</span>
                                        @endif
                                    </div>

                                    <div class="py-[20px] px-[20px] text-left">
                                        <h3><a href="{{ route('project.properties.index', [$project->slug]) }}"
                                                class="font-lora leading-tight text-[22px] xl:text-[26px] text-primary hover:text-secondary transition-all font-medium">{{ $project->name }}</a>
                                        </h3>
                                        <h4><span
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
                                            <li class="flex flex-wrap items-center pr-[15px] border-r border-[#E0DEDE]">
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
                                                    class="font-lora text-base text-primary leading-none font-medium">Starting:
                                                    {{ $project->formatted_starting_price }}</span>

                                                <span class="flex flex-wrap items-center">
                                                    <a href="{{ route('project.properties.index', [$project->slug]) }}"
                                                        class="inline-flex items-center gap-1 text-xs text-secondary hover:text-primary font-medium transition-colors">
                                                        View Project
                                                        <svg width="14" height="10" viewBox="0 0 26 11" fill="currentColor">
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
                            <!-- swiper-slide end-->
                        @empty
                            <div class="col-span-12 text-center py-10">
                                <p class="text-gray-500">No popular projects found.</p>
                            </div>
                        @endforelse
                    </div>
                    <!-- Add Pagination -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>

        </div>
    </section>
    <!-- Popular Projects end -->

    <!-- About Us Sectin Start -->
    <section class="about-section pt-10">
        <div class="container">
            <div class="grid grid-cols-12 gap-6 items-center">
                <div class="col-span-12 lg:col-span-6">
                    <span class="text-secondary text-tiny inline-block mb-2">Why Choose us</span>
                    <h2
                        class="font-lora text-primary text-[24px] sm:text-[30px] leading-[1.277] xl:text-xl capitalize mb-5 lg:mb-16 font-medium max-w-[500px]">
                        We Provide Latest Properties for our valuable Clients<span class="text-secondary">.</span></h2>
                    <div class="scene" data-relative-input="true">
                        <img data-depth="0.1" src="{{ asset('assets/images/about/about1.png') }}" class=""
                            loading="lazy" width="729" height="663" alt="about Image">
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-6 lg:pl-[70px]">
                    <p class="max-w-[448px] ">Huge number of propreties availabe here for buy, sell and
                        Rent. Also you find here co-living property so lots opportunity
                        you have to choose here and enjoy huge discount. </p>

                    <div class="-mb-10 mt-12 xl:mt-[70px] 2xl:mt-[100px]">
                        <div class="flex flex-wrap mb-5 lg:mb-10">
                            <img src="{{ asset('assets/images/icon/doller.png') }}" class="self-start mr-5"
                                loading="lazy" width="50" height="50" alt="about Image">
                            <div class="flex-1">
                                <h3 class="font-lora text-primary text-[22px] xl:text-lg capitalize mb-2">Budget
                                    Friendly</h3>
                                <p class="max-w-[315px]">Properties are most budget friendly so you
                                    have opportunity to find the best one</p>
                            </div>

                        </div>
                        <div class="flex flex-wrap mb-5 lg:mb-10">
                            <img src="{{ asset('assets/images/icon/location.png') }}" class="self-start mr-5"
                                loading="lazy" width="50" height="50" alt="about Image">
                            <div class="flex-1">
                                <h3 class="font-lora text-primary text-[22px] xl:text-lg capitalize mb-2">Prime
                                    Location</h3>
                                <p class="max-w-[315px]">Properties are most budget friendly so you
                                    have opportunity to find the best one</p>
                            </div>

                        </div>
                        <div class="flex flex-wrap mb-5 lg:mb-10">
                            <img src="{{ asset('assets/images/icon/trusted.png') }}" class="self-start mr-5"
                                loading="lazy" width="50" height="50" alt="about Image">
                            <div class="flex-1">
                                <h3 class="font-lora text-primary text-[22px] xl:text-lg capitalize mb-2">
                                    Trusted by
                                    Thousand</h3>
                                <p class="max-w-[315px]">Properties are most budget friendly so you
                                    have opportunity to find the best one</p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- About Us Sectin End -->

    <!-- Featured Properties Start -->
    <section class="featured-properties py-[80px] lg:py-[125px]">
        <div class="container">
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <span class="text-secondary text-tiny inline-block mb-2">Newly Added</span>
                </div>
                <div class="col-span-12 flex flex-wrap flex-col md:flex-row items-start justify-between mb-[50px]">
                    <div class="mb-5 lg:mb-0">
                        <h2 class="font-lora text-primary text-[24px] sm:text-[30px] xl:text-xl capitalize font-medium">
                            Featured
                            Properties<span class="text-secondary">.</span></h2>
                    </div>
                    <ul class="all-properties flex flex-wrap lg:pt-[10px]">
                        <li data-tab="all-properties" class="mr-[30px] md:mr-[45px] mb-4 lg:mb-0 leading-none active">
                            <button
                                class="leading-none capitalize text-primary hover:text-secondary transition-all text-[16px] ease-out">All
                                Properties</button>
                        </li>
                        @foreach($types as $type)
                            <li data-tab="{{ $type->slug }}" class="mr-[30px] md:mr-[45px] mb-4 lg:mb-0 leading-none">
                                <button
                                    class="leading-none capitalize text-primary hover:text-secondary transition-all text-[16px] ease-out">{{ $type->name }}</button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-span-12">
                    {{-- ALL PROPERTIES TAB --}}
                    <div class="all-properties properties-tab-content active">
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-[30px]">
                            @forelse($featured_properties as $property)
                                <div class="swiper-slide">
                                    <div
                                        class="overflow-hidden rounded-md drop-shadow-[0px_0px_5px_rgba(0,0,0,0.1)] bg-[#FFFDFC] text-center transition-all duration-300 hover:-translate-y-[10px]">
                                        <div class="relative">
                                            <a href="{{ route('project.properties.show', [$property->project->slug, $property->slug]) }}"
                                                class="block">
                                                <img src="{{ asset($property->image) }}" class="w-full h-full"
                                                    loading="lazy" width="370" height="266" alt="{{ $property->name }}">
                                            </a>
                                            <div class="flex flex-wrap flex-col absolute top-5 right-5">
                                                <button
                                                    class="flex flex-wrap items-center bg-[rgb(11,44,61,0.8)] p-[5px] rounded-[2px] text-white mb-[5px] text-xs">
                                                    <img class="mr-1" src="{{ asset('assets/images/icon/camera.png') }}"
                                                        loading="lazy" width="13" height="10" alt="camera icon">07
                                                </button>
                                                <button
                                                    class="flex flex-wrap items-center bg-[rgb(11,44,61,0.8)] p-[5px] rounded-[2px] text-white text-xs">
                                                    <img class="mr-1" src="{{ asset('assets/images/icon/video.png') }}"
                                                        loading="lazy" width="14" height="10" alt="camera icon">08
                                                </button>
                                            </div>
                                            <span
                                                class="absolute bottom-5 left-5 bg-[#FFFDFC] p-[5px] rounded-[2px] text-primary leading-none text-[14px] font-normal capitalize">
                                                {{ $property->type }}
                                            </span>
                                        </div>

                                        <div class="py-[20px] px-[20px] text-left">
                                            <h3>
                                                <a href="{{ route('project.properties.show', [$property->project->slug, $property->slug]) }}"
                                                    class="font-lora leading-tight text-[22px] xl:text-[26px] text-primary hover:text-secondary transition-all font-medium">
                                                    {{ $property->name }}
                                                </a>
                                            </h3>
                                            <h4>
                                                <a href="{{ route('project.properties.show', [$property->project->slug, $property->slug]) }}"
                                                    class="font-light text-[14px] leading-[1.75] underline">
                                                    {{ $property->address }}
                                                </a>
                                            </h4>
                                            <span class="font-light text-sm">Added:
                                                {{ $property->created_at->format('d F, Y') }}</span>
                                            <ul
                                                class="flex flex-wrap items-center justify-between text-[12px] mt-[10px] mb-[15px] pb-[10px] border-b border-[#E0E0E0]">
                                                <li
                                                    class="flex flex-wrap items-center pr-[25px] sm:pr-[5px] md:pr-[25px] border-r border-[#E0DEDE]">
                                                    <svg class="mr-[5px]" width="14" height="14" viewBox="0 0 14 14"
                                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M11.8125 9.68709V4.31285C12.111 4.23634 12.384 4.0822 12.6037 3.86607C12.8234 3.64994 12.982 3.37951 13.0634 3.08226C13.1448 2.78501 13.1461 2.47151 13.0671 2.1736C12.9882 1.87569 12.8318 1.60398 12.6139 1.38605C12.396 1.16812 12.1243 1.01174 11.8263 0.932792C11.5284 0.85384 11.2149 0.855126 10.9177 0.936521C10.6204 1.01792 10.35 1.17652 10.1339 1.39623C9.91774 1.61593 9.7636 1.88892 9.68709 2.18747H4.31285C4.23634 1.88892 4.0822 1.61593 3.86607 1.39623C3.64994 1.17652 3.37951 1.01792 3.08226 0.936521C2.78501 0.855126 2.47151 0.85384 2.1736 0.932792C1.87569 1.01174 1.60398 1.16812 1.38605 1.38605C1.16812 1.60398 1.01174 1.87569 0.932792 2.1736C0.85384 2.47151 0.855126 2.78501 0.936521 3.08226C1.01792 3.37951 1.17652 3.64994 1.39623 3.86607C1.61593 4.0822 1.88892 4.23634 2.18747 4.31285V9.68709C1.88892 9.7636 1.61593 9.91774 1.39623 10.1339C1.17652 10.35 1.01792 10.6204 0.936521 10.9177C0.855126 11.2149 0.85384 11.5284 0.932792 11.8263C1.01174 12.1243 1.16812 12.396 1.38605 12.6139C1.60398 12.8318 1.87569 12.9882 2.1736 13.0671C2.47151 13.1461 2.78501 13.1448 3.08226 13.0634C3.37951 12.982 3.64994 12.8234 3.86607 12.6037C4.0822 12.384 4.23634 12.111 4.31285 11.8125H9.68709C9.7636 12.111 9.91774 12.384 10.1339 12.6037C10.35 12.8234 10.6204 12.982 10.9177 13.0634C11.2149 13.1448 11.5284 13.1461 11.8263 13.0671C12.1243 12.9882 12.396 12.8318 12.6139 12.6139C12.8318 12.396 12.9882 12.1243 13.0671 11.8263C13.1461 11.5284 13.1448 11.2149 13.0634 10.9177C12.982 10.6204 12.8234 10.35 12.6037 10.1339C12.384 9.91774 12.111 9.7636 11.8125 9.68709Z" />
                                                    </svg>
                                                    <span>{{ $property->size }} Sq.ft</span>
                                                </li>
                                                <li
                                                    class="flex flex-wrap items-center pr-[25px] sm:pr-[5px] md:pr-[25px] border-r border-[#E0DEDE]">
                                                    <svg class="mr-[5px]" width="14" height="10" viewBox="0 0 14 10"
                                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.0002 4.18665V2.33331C13.0002 1.23331 12.1002 0.333313 11.0002 0.333313H8.3335C7.82016 0.333313 7.3535 0.533313 7.00016 0.853313C6.64683 0.533313 6.18016 0.333313 5.66683 0.333313H3.00016C1.90016 0.333313 1.00016 1.23331 1.00016 2.33331V4.18665C0.593496 4.55331 0.333496 5.07998 0.333496 5.66665V9.66665H1.66683V8.33331H12.3335V9.66665H13.6668V5.66665C13.6668 5.07998 13.4068 4.55331 13.0002 4.18665Z" />
                                                    </svg>
                                                    <span>{{ $property->bedrooms }} Beds</span>
                                                </li>
                                                <li
                                                    class="flex flex-wrap items-center pr-[25px] sm:pr-[5px] md:pr-[25px] border-r border-[#E0DEDE]">
                                                    <svg class="mr-[5px]" width="14" height="14" viewBox="0 0 14 14"
                                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M12.6875 7.65627H2.1875V2.7344C2.18699 2.54904 2.22326 2.36543 2.29419 2.19418C2.36512 2.02294 2.46932 1.86746 2.60075 1.73676L2.61168 1.72582C2.81765 1.52015 3.0821 1.38309 3.36889 1.33336C3.65568 1.28362 3.95083 1.32364 4.21403 1.44795C3.96546 1.86122 3.86215 2.34571 3.9205 2.82443C3.97885 3.30315 4.19552 3.74864 4.53608 4.0901L4.83552 4.38954L4.28436 4.94073L4.90304 5.55941L5.4542 5.00825L8.5082 1.95431L9.05937 1.40314L8.44066 0.78443L7.88946 1.3356L7.59002 1.03616C7.23151 0.678646 6.75892 0.458263 6.2546 0.413412C5.75029 0.368561 5.24622 0.502086 4.83025 0.790719C4.3916 0.513704 3.87178 0.394114 3.35619 0.451596C2.84059 0.509078 2.35987 0.740213 1.993 1.10703L1.98207 1.11797C1.76912 1.32975 1.6003 1.58165 1.48537 1.85911C1.37044 2.13657 1.31168 2.43407 1.3125 2.7344V7.65627H0.4375V8.53127H1.3125V9.37072C1.31248 9.44126 1.32386 9.51133 1.34619 9.57823L2.16016 12.02C2.20359 12.1508 2.28712 12.2645 2.39887 12.345C2.51062 12.4256 2.64491 12.4689 2.78266 12.4688H3.1354L2.81641 13.5625H3.72786L4.04688 12.4688H9.73711L10.0652 13.5625H10.9785L10.6504 12.4688H11.2172C11.355 12.4689 11.4893 12.4256 11.6011 12.3451C11.7129 12.2645 11.7964 12.1508 11.8398 12.02L12.6538 9.57823C12.6761 9.51133 12.6875 9.44126 12.6875 9.37072V8.53127H13.5625V7.65627H12.6875Z" />
                                                    </svg>
                                                    <span>{{ $property->bathrooms }} Baths</span>
                                                </li>
                                                <li class="flex flex-wrap items-center">
                                                    <svg class="mr-[5px]" width="14" height="14" viewBox="0 0 14 14"
                                                        fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M12.25 6.98507H12.236L11.1307 4.49805C11.0275 4.26615 10.8592 4.06913 10.6464 3.93083C10.4335 3.79253 10.1851 3.71887 9.93125 3.71875H4.06875C3.81491 3.71888 3.56655 3.79256 3.3537 3.93086C3.14085 4.06916 2.97263 4.26616 2.86937 4.49805L1.76397 6.98507H1.75C1.51802 6.98533 1.29561 7.0776 1.13157 7.24164C0.967531 7.40568 0.875261 7.62809 0.875 7.86007V10.9226C0.875261 11.1546 0.967531 11.377 1.13157 11.541C1.29561 11.705 1.51802 11.7973 1.75 11.7976V12.9062C1.7502 13.0802 1.81941 13.247 1.94243 13.3701C2.06546 13.4931 2.23226 13.5623 2.40625 13.5625H3.9375C4.11149 13.5623 4.27829 13.4931 4.40131 13.3701C4.52434 13.247 4.59355 13.0802 4.59375 12.9062V11.7976H9.40625V12.9062C9.40645 13.0802 9.47566 13.247 9.59869 13.3701C9.72171 13.4931 9.88851 13.5623 10.0625 13.5625H11.5938C11.7677 13.5623 11.9345 13.4931 12.0576 13.3701C12.1806 13.247 12.2498 13.0802 12.25 12.9062V11.7976C12.482 11.7973 12.7044 11.705 12.8684 11.541C13.0325 11.377 13.1247 11.1546 13.125 10.9226V7.86007C13.1247 7.62809 13.0325 7.40568 12.8684 7.24164C12.7044 7.0776 12.482 6.98533 12.25 6.98507Z" />
                                                    </svg>
                                                    <span>{{ $property->garages }} Garage</span>
                                                </li>
                                            </ul>

                                            <ul>
                                                <li class="flex flex-wrap items-center justify-between">
                                                    <span class="font-lora text-base text-primary leading-none font-medium">
                                                        Price: {{ $property->formatted_price }}
                                                    </span>
                                                    <span class="flex flex-wrap items-center">
                                                        <button class="mr-[15px] text-[#9D9C9C] hover:text-secondary"
                                                            aria-label="share">
                                                            <svg width="16" height="16" viewBox="0 0 16 16"
                                                                fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M13.1667 11.6667C12.8572 11.6667 12.5605 11.7896 12.3417 12.0084C12.1229 12.2272 12 12.5239 12 12.8334C12 13.1428 12.1229 13.4395 12.3417 13.6583C12.5605 13.8771 12.8572 14 13.1667 14C13.4761 14 13.7728 13.8771 13.9916 13.6583C14.2104 13.4395 14.3333 13.1428 14.3333 12.8334C14.3333 12.5239 14.2104 12.2272 13.9916 12.0084C13.7728 11.7896 13.4761 11.6667 13.1667 11.6667Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </button>
                                                        <button class="text-[#9D9C9C] hover:text-secondary"
                                                            aria-label="wishlist">
                                                            <svg width="16" height="16" viewBox="0 0 16 16"
                                                                fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M7.9999 2.74799L7.2829 2.01099C5.5999 0.280988 2.5139 0.877988 1.39989 3.05299C0.876895 4.07599 0.758895 5.55299 1.71389 7.43799C2.63389 9.25299 4.5479 11.427 7.9999 13.795C11.4519 11.427 13.3649 9.25299 14.2859 7.43799C15.2409 5.55199 15.1239 4.07599 14.5999 3.05299C13.4859 0.877988 10.3999 0.279988 8.7169 2.00999L7.9999 2.74799Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </button>
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3 text-center py-10">
                                    <p class="text-gray-500">No featured properties found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @foreach($types as $type)
                        <div class="{{ $type->slug }} properties-tab-content">
                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-[30px]">
                                @forelse($featured_properties->where('property_type_id', $type->id) as $property)
                                    <div class="swiper-slide">
                                        <div
                                            class="overflow-hidden rounded-md drop-shadow-[0px_0px_5px_rgba(0,0,0,0.1)] bg-[#FFFDFC] text-center transition-all duration-300 hover:-translate-y-[10px]">
                                            <div class="relative">
                                                <a href="{{ route('project.properties.show', [$property->project->slug, $property->slug]) }}"
                                                    class="block">
                                                    <img src="{{ asset($property->image) }}" class="w-full h-full"
                                                        loading="lazy" width="370" height="266" alt="{{ $property->name }}">
                                                </a>
                                                <div class="flex flex-wrap flex-col absolute top-5 right-5">
                                                    <button
                                                        class="flex flex-wrap items-center bg-[rgb(11,44,61,0.8)] p-[5px] rounded-[2px] text-white mb-[5px] text-xs">
                                                        <img class="mr-1" src="{{ asset('assets/images/icon/camera.png') }}"
                                                            loading="lazy" width="13" height="10" alt="camera icon">07
                                                    </button>
                                                    <button
                                                        class="flex flex-wrap items-center bg-[rgb(11,44,61,0.8)] p-[5px] rounded-[2px] text-white text-xs">
                                                        <img class="mr-1" src="{{ asset('assets/images/icon/video.png') }}"
                                                            loading="lazy" width="14" height="10" alt="camera icon">08
                                                    </button>
                                                </div>
                                                <span
                                                    class="absolute bottom-5 left-5 bg-[#FFFDFC] p-[5px] rounded-[2px] text-primary leading-none text-[14px] font-normal capitalize">
                                                    {{ $property->type }}
                                                </span>
                                            </div>

                                            <div class="py-[20px] px-[20px] text-left">
                                                <h3>
                                                    <a href="{{ route('project.properties.show', [$property->project->slug, $property->slug]) }}"
                                                        class="font-lora leading-tight text-[22px] xl:text-[26px] text-primary hover:text-secondary transition-all font-medium">
                                                        {{ $property->name }}
                                                    </a>
                                                </h3>
                                                <h4>
                                                    <a href="{{ route('project.properties.show', [$property->project->slug, $property->slug]) }}"
                                                        class="font-light text-[14px] leading-[1.75] underline">
                                                        {{ $property->address }}
                                                    </a>
                                                </h4>
                                                <span class="font-light text-sm">Added:
                                                    {{ $property->created_at->format('d F, Y') }}</span>
                                                <ul
                                                    class="flex flex-wrap items-center justify-between text-[12px] mt-[10px] mb-[15px] pb-[10px] border-b border-[#E0E0E0]">
                                                    <li
                                                        class="flex flex-wrap items-center pr-[25px] sm:pr-[5px] md:pr-[25px] border-r border-[#E0DEDE]">
                                                        <svg class="mr-[5px]" width="14" height="14" viewBox="0 0 14 14"
                                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M11.8125 9.68709V4.31285C12.111 4.23634 12.384 4.0822 12.6037 3.86607C12.8234 3.64994 12.982 3.37951 13.0634 3.08226C13.1448 2.78501 13.1461 2.47151 13.0671 2.1736C12.9882 1.87569 12.8318 1.60398 12.6139 1.38605C12.396 1.16812 12.1243 1.01174 11.8263 0.932792C11.5284 0.85384 11.2149 0.855126 10.9177 0.936521C10.6204 1.01792 10.35 1.17652 10.1339 1.39623C9.91774 1.61593 9.7636 1.88892 9.68709 2.18747H4.31285C4.23634 1.88892 4.0822 1.61593 3.86607 1.39623C3.64994 1.17652 3.37951 1.01792 3.08226 0.936521C2.78501 0.855126 2.47151 0.85384 2.1736 0.932792C1.87569 1.01174 1.60398 1.16812 1.38605 1.38605C1.16812 1.60398 1.01174 1.87569 0.932792 2.1736C0.85384 2.47151 0.855126 2.78501 0.936521 3.08226C1.01792 3.37951 1.17652 3.64994 1.39623 3.86607C1.61593 4.0822 1.88892 4.23634 2.18747 4.31285V9.68709C1.88892 9.7636 1.61593 9.91774 1.39623 10.1339C1.17652 10.35 1.01792 10.6204 0.936521 10.9177C0.855126 11.2149 0.85384 11.5284 0.932792 11.8263C1.01174 12.1243 1.16812 12.396 1.38605 12.6139C1.60398 12.8318 1.87569 12.9882 2.1736 13.0671C2.47151 13.1461 2.78501 13.1448 3.08226 13.0634C3.37951 12.982 3.64994 12.8234 3.86607 12.6037C4.0822 12.384 4.23634 12.111 4.31285 11.8125H9.68709C9.7636 12.111 9.91774 12.384 10.1339 12.6037C10.35 12.8234 10.6204 12.982 10.9177 13.0634C11.2149 13.1448 11.5284 13.1461 11.8263 13.0671C12.1243 12.9882 12.396 12.8318 12.6139 12.6139C12.8318 12.396 12.9882 12.1243 13.0671 11.8263C13.1461 11.5284 13.1448 11.2149 13.0634 10.9177C12.982 10.6204 12.8234 10.35 12.6037 10.1339C12.384 9.91774 12.111 9.7636 11.8125 9.68709Z" />
                                                        </svg>
                                                        <span>{{ $property->size }} Sq.ft</span>
                                                    </li>
                                                    <li
                                                        class="flex flex-wrap items-center pr-[25px] sm:pr-[5px] md:pr-[25px] border-r border-[#E0DEDE]">
                                                        <svg class="mr-[5px]" width="14" height="10" viewBox="0 0 14 10"
                                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M13.0002 4.18665V2.33331C13.0002 1.23331 12.1002 0.333313 11.0002 0.333313H8.3335C7.82016 0.333313 7.3535 0.533313 7.00016 0.853313C6.64683 0.533313 6.18016 0.333313 5.66683 0.333313H3.00016C1.90016 0.333313 1.00016 1.23331 1.00016 2.33331V4.18665C0.593496 4.55331 0.333496 5.07998 0.333496 5.66665V9.66665H1.66683V8.33331H12.3335V9.66665H13.6668V5.66665C13.6668 5.07998 13.4068 4.55331 13.0002 4.18665Z" />
                                                        </svg>
                                                        <span>{{ $property->bedrooms }} Beds</span>
                                                    </li>
                                                    <li
                                                        class="flex flex-wrap items-center pr-[25px] sm:pr-[5px] md:pr-[25px] border-r border-[#E0DEDE]">
                                                        <svg class="mr-[5px]" width="14" height="14" viewBox="0 0 14 14"
                                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M12.6875 7.65627H2.1875V2.7344C2.18699 2.54904 2.22326 2.36543 2.29419 2.19418C2.36512 2.02294 2.46932 1.86746 2.60075 1.73676L2.61168 1.72582C2.81765 1.52015 3.0821 1.38309 3.36889 1.33336C3.65568 1.28362 3.95083 1.32364 4.21403 1.44795C3.96546 1.86122 3.86215 2.34571 3.9205 2.82443C3.97885 3.30315 4.19552 3.74864 4.53608 4.0901L4.83552 4.38954L4.28436 4.94073L4.90304 5.55941L5.4542 5.00825L8.5082 1.95431L9.05937 1.40314L8.44066 0.78443L7.88946 1.3356L7.59002 1.03616C7.23151 0.678646 6.75892 0.458263 6.2546 0.413412C5.75029 0.368561 5.24622 0.502086 4.83025 0.790719C4.3916 0.513704 3.87178 0.394114 3.35619 0.451596C2.84059 0.509078 2.35987 0.740213 1.993 1.10703L1.98207 1.11797C1.76912 1.32975 1.6003 1.58165 1.48537 1.85911C1.37044 2.13657 1.31168 2.43407 1.3125 2.7344V7.65627H0.4375V8.53127H1.3125V9.37072C1.31248 9.44126 1.32386 9.51133 1.34619 9.57823L2.16016 12.02C2.20359 12.1508 2.28712 12.2645 2.39887 12.345C2.51062 12.4256 2.64491 12.4689 2.78266 12.4688H3.1354L2.81641 13.5625H3.72786L4.04688 12.4688H9.73711L10.0652 13.5625H10.9785L10.6504 12.4688H11.2172C11.355 12.4689 11.4893 12.4256 11.6011 12.3451C11.7129 12.2645 11.7964 12.1508 11.8398 12.02L12.6538 9.57823C12.6761 9.51133 12.6875 9.44126 12.6875 9.37072V8.53127H13.5625V7.65627H12.6875Z" />
                                                        </svg>
                                                        <span>{{ $property->bathrooms }} Baths</span>
                                                    </li>
                                                    <li class="flex flex-wrap items-center">
                                                        <svg class="mr-[5px]" width="14" height="14" viewBox="0 0 14 14"
                                                            fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M12.25 6.98507H12.236L11.1307 4.49805C11.0275 4.26615 10.8592 4.06913 10.6464 3.93083C10.4335 3.79253 10.1851 3.71887 9.93125 3.71875H4.06875C3.81491 3.71888 3.56655 3.79256 3.3537 3.93086C3.14085 4.06916 2.97263 4.26616 2.86937 4.49805L1.76397 6.98507H1.75C1.51802 6.98533 1.29561 7.0776 1.13157 7.24164C0.967531 7.40568 0.875261 7.62809 0.875 7.86007V10.9226C0.875261 11.1546 0.967531 11.377 1.13157 11.541C1.29561 11.705 1.51802 11.7973 1.75 11.7976V12.9062C1.7502 13.0802 1.81941 13.247 1.94243 13.3701C2.06546 13.4931 2.23226 13.5623 2.40625 13.5625H3.9375C4.11149 13.5623 4.27829 13.4931 4.40131 13.3701C4.52434 13.247 4.59355 13.0802 4.59375 12.9062V11.7976H9.40625V12.9062C9.40645 13.0802 9.47566 13.247 9.59869 13.3701C9.72171 13.4931 9.88851 13.5623 10.0625 13.5625H11.5938C11.7677 13.5623 11.9345 13.4931 12.0576 13.3701C12.1806 13.247 12.2498 13.0802 12.25 12.9062V11.7976C12.482 11.7973 12.7044 11.705 12.8684 11.541C13.0325 11.377 13.1247 11.1546 13.125 10.9226V7.86007C13.1247 7.62809 13.0325 7.40568 12.8684 7.24164C12.7044 7.0776 12.482 6.98533 12.25 6.98507Z" />
                                                        </svg>
                                                        <span>{{ $property->garages }} Garage</span>
                                                    </li>
                                                </ul>

                                                <ul>
                                                    <li class="flex flex-wrap items-center justify-between">
                                                        <span class="font-lora text-base text-primary leading-none font-medium">
                                                            Price: {{ $property->formatted_price }}
                                                        </span>
                                                        <span class="flex flex-wrap items-center">
                                                            <button class="mr-[15px] text-[#9D9C9C] hover:text-secondary"
                                                                aria-label="share">
                                                                <svg width="16" height="16" viewBox="0 0 16 16"
                                                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M13.1667 11.6667C12.8572 11.6667 12.5605 11.7896 12.3417 12.0084C12.1229 12.2272 12 12.5239 12 12.8334C12 13.1428 12.1229 13.4395 12.3417 13.6583C12.5605 13.8771 12.8572 14 13.1667 14C13.4761 14 13.7728 13.8771 13.9916 13.6583C14.2104 13.4395 14.3333 13.1428 14.3333 12.8334C14.3333 12.5239 14.2104 12.2272 13.9916 12.0084C13.7728 11.7896 13.4761 11.6667 13.1667 11.6667Z"
                                                                        fill="currentColor" />
                                                                </svg>
                                                            </button>
                                                            <button class="text-[#9D9C9C] hover:text-secondary"
                                                                aria-label="wishlist">
                                                                <svg width="16" height="16" viewBox="0 0 16 16"
                                                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M7.9999 2.74799L7.2829 2.01099C5.5999 0.280988 2.5139 0.877988 1.39989 3.05299C0.876895 4.07599 0.758895 5.55299 1.71389 7.43799C2.63389 9.25299 4.5479 11.427 7.9999 13.795C11.4519 11.427 13.3649 9.25299 14.2859 7.43799C15.2409 5.55199 15.1239 4.07599 14.5999 3.05299C13.4859 0.877988 10.3999 0.279988 8.7169 2.00999L7.9999 2.74799Z"
                                                                        fill="currentColor" />
                                                                </svg>
                                                            </button>
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-3 text-center py-10">
                                        <p class="text-gray-500">No featured {{ strtolower($type->name) }} found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </section>
    <!-- Featured Properties End -->

    <!-- Video Section Start -->
    <section class="video-section">
        <div class="container">
            <div
                class="grid grid-cols-12 gap-3 sm:gap-[30px] items-center bg-primary z-[-2] lg:pl-[60px] lg:pr-0 lg:py-0 sm:pl-10 sm:pr-10 pl-5 pr-5 py-5 sm:py-12 rounded-[7px]">
                <div class="col-span-12 lg:col-span-6 relative">
                    <div class="mb-5 lg:mb-0 max-w-[450px]">
                        <span class="text-secondary text-tiny inline-block mb-2">Take a video tour</span>
                        <h2
                            class="font-lora text-white text-[24px] sm:text-[30px] leading-[1.277] xl:text-xl mb-[10px] font-medium">
                            Watch the video for taking your decision easily<span class="text-secondary">.</span></h2>
                        <a href="#" class="flex flex-wrap items-center text-secondary text-tiny mt-[20px]">View all
                            <svg class="ml-[10px]" width="26" height="11" viewBox="0 0 26 11" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20.0877 0.69303L24.2075 5.00849H0V5.99151H24.2075L20.0877 10.307L20.7493 11L26 5.5L20.7493 0L20.0877 0.69303Z"
                                    fill="currentColor"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-6 text-center">
                    <div class="relative rounded-[24px] lg:pt-[45px] lg:pr-[45px] z-[1] lg:inline-block">
                        <div class="">
                            <img src="{{ asset('assets/images/video/shape-3.png') }}"
                                class="absolute top-[30px] right-[30px] z-[-1]" loading="lazy" width="50" height="60"
                                alt="shape image">
                            <img src="{{ asset('assets/images/video/shape-2.png') }}"
                                class="absolute left-1/2 hidden lg:block lg:bottom-5 lg:-left-[160px]" loading="lazy"
                                width="128" height="56" alt="Shape">
                        </div>
                        <div class="relative lg:-mb-16">
                            <div class="scene" data-relative-input="true">
                                <img data-depth="0.1" src="{{ asset('assets/images/video/video.png') }}"
                                    class="rounded-[24px] max-w-full" loading="lazy" width="507" height="349"
                                    alt="video image">
                            </div>
                            <a href="https://www.youtube.com/watch?v=mSC6GwizOag" class="play-button bg-white text-white hover:text-primary absolute left-0 right-0 mx-auto top-1/2 -translate-y-1/2 hover:scale-105 hover:bg-primary w-[55px] h-[55px] flex 
            flex-wrap z-[1] items-center justify-center opacity-100 shadow-[0px 4px 4px rgba(0, 0, 0, 0.25)] transition-all rounded-full group
            
            before:block before:absolute  before:bg-white before:opacity-80 before:shadow-[0px 4px 4px rgba(0, 0, 0, 0.25)] hover:before:bg-primary hover:before:opacity-80 before:w-[70px] before:h-[70px] before:rounded-full before:z-[-1]
            " aria-label="play button">
                                <svg width="21" height="22" viewBox="0 0 21 22" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path class="stroke-primary group-hover:stroke-white"
                                        d="M1.63861 10.764V6.70324C1.63861 1.66145 5.20893 -0.403178 9.57772 2.11772L13.1024 4.14812L16.6271 6.17853C20.9959 8.69942 20.9959 12.8287 16.6271 15.3496L13.1024 17.38L9.57772 19.4104C5.20893 21.9313 1.63861 19.8666 1.63861 14.8249V10.764Z"
                                        stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="flex flex-row items-baseline gap-2 mt-10 leading-[1]">
                <span class="text-secondary text-[16px] sm:text-[20px] font-lora font-normal">Have a question?</span>
                <a class="text-primary text-[22px] sm:text-[28px] font-lora font-medium"
                    href="{{ $global_setting->whatsapp }}">{{ $global_setting->phone_number[0] }}</a>
            </div>
        </div>
    </section>
    <!-- Video Section End -->

    <!-- Explore Cities Start-->
    <section class="explore-cities-section pb-[50px] pt-[80px] lg:pt-[125px]">
        <div class="container">
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <div class="mb-[30px] lg:mb-[60px] text-center">
                        <span class="text-secondary text-tiny inline-block mb-2">Explore Cities</span>
                        <h2 class="font-lora text-primary text-[24px] sm:text-[30px] xl:text-xl capitalize font-medium">
                            Find
                            Your
                            Neighborhood<span class="text-secondary">.</span></h2>
                    </div>
                    <div class="cities-slider">
                        <div class="swiper  -mx-[30px] -my-[60px] px-[30px] py-[60px]">
                            <div class="swiper-wrapper">
                                @forelse($cities as $city)
                                    <!-- swiper-slide start -->
                                    <div class="swiper-slide text-center">
                                        <div class="relative group">
                                            <a href="{{ route('city.index', $city->slug) }}"
                                                class="block group-hover:shadow-[0_10px_15px_0px_rgba(0,0,0,0.1)] transition-all duration-300">
                                                <img src="{{ asset($city->image) }}"
                                                    class="w-full h-full block mx-auto rounded-[6px]" loading="lazy"
                                                    width="270" height="380" alt="{{ $city->name }}">
                                                <div
                                                    class="bg-[rgb(255,253,252,0.9)] rounded-[6px] px-[5px] py-[15px] absolute group-hover:bottom-[25px] group-hover:opacity-100 bottom-[0px] opacity-0 left-[25px] right-[25px] transition-all duration-500">
                                                    <span
                                                        class="font-lora font-normal text-[18px] text-primary transition-all leading-none">{{ $city->name }}</span>
                                                    <p
                                                        class="font-light text-[14px] capitalize text-secondary transition-all leading-none">
                                                        {{ $city->projects_count ?? 0 }}
                                                        {{ ($city->projects_count ?? 0) == 1 ? 'Project' : 'Projects' }}
                                                    </p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- swiper-slide end-->
                                @empty
                                    <div class="col-span-12 text-center py-10">
                                        <p class="text-gray-500">No cities available.</p>
                                    </div>
                                @endforelse
                            </div>
                            <!-- Add Pagination -->
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Explore Cities End-->

    <!-- Brand section Start-->
    <section class="brand-section pt-[80px] lg:pt-[125px] pb-[80px] lg:pb-[125px]">
        <div class="container">
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <div class="mb-[60px] text-center">
                        <span class="text-secondary text-tiny inline-block mb-2">Our Partner’s</span>
                        <h2 class="font-lora text-primary text-[24px] sm:text-[30px] xl:text-xl capitalize font-medium">
                            Reliable Partner’s<span class="text-secondary">.</span></h2>
                    </div>
                </div>
                <div class="col-span-12">
                    <div class="brand-slider">
                        <div class="swiper">
                            <div class="swiper-wrapper">
                                <!-- swiper-slide start -->
                                @if(isset($partners) && count($partners) > 0)
                                    @foreach($partners as $partner)
                                        <div class="swiper-slide text-center">
                                            <a href="{{ $partner->link ?? '#' }}" class="block">
                                                <img src="{{ asset($partner->logo) }}" class="w-auto h-auto block mx-auto"
                                                    loading="lazy" width="125" height="109" alt="{{ $partner->name }}">
                                            </a>
                                        </div>
                                    @endforeach
                                @else
                                    <!-- Fallback default partner brands -->
                                    <div class="swiper-slide text-center">
                                        <a href="#" class="block">
                                            <img src="{{ asset('assets/images/brand/brand1.png') }}"
                                                class="w-auto h-auto block mx-auto" loading="lazy" width="125" height="109"
                                                alt="Partner Brand 1">
                                        </a>
                                    </div>
                                @endif
                                <!-- swiper-slide end-->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Brand section End-->

    <!-- Team Section Etart-->
    <section class="team-section pb-[80px] lg:pb-[125px] overflow-hidden">
        <div class="container">
            <div class="grid sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 gap-x-5 md:gap-x-[30px] mb-[-30px]">
                <div class="xl:pr-[20px] self-center mb-[30px] sm:col-span-3 lg:col-span-1 max-w-[500px]">
                    <span class="text-secondary text-tiny capitalize inline-block mb-[15px]">Our Agents</span>
                    <h2
                        class="font-lora text-primary text-[24px] sm:text-[30px] leading-[1.277] xl:text-xl capitalize mb-[15px] font-medium">
                        Here is our Experts<span class="text-secondary">.</span></h2>

                    <p>Huge number propreties availabe
                        here for invesment, dream home and plots for sale , you can choose from
                        find here co-living property lots
                        to choose here and enjoy huge. </p>
                    <a href="{{ route('projects.index') }}"
                        class="flex flex-wrap items-center text-secondary text-tiny mt-[20px]">View
                        Projects
                        <svg class="ml-[10px]" width="26" height="11" viewBox="0 0 26 11" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20.0877 0.69303L24.2075 5.00849H0V5.99151H24.2075L20.0877 10.307L20.7493 11L26 5.5L20.7493 0L20.0877 0.69303Z"
                                fill="currentColor"></path>
                        </svg>
                    </a>

                </div>
                <!-- single team start -->
                @if(isset($agents) && count($agents) > 0)
                    @foreach($agents as $team)
                        <div class="text-center group mb-[30px]">
                            <div class="relative rounded-[6px_6px_0px_0px]">
                                <a href="agent-details.html">
                                    <img src="{{ asset($team->image) }}" class="w-auto h-auto block mx-auto" loading="lazy"
                                        width="215" height="310" alt="{{ $team->name }}">
                                </a>
                                <ul class="flex flex-col absolute w-full top-[30px] left-0 overflow-hidden">
                                    @if($team->facebook)
                                        <li
                                            class="translate-x-[0px] group-hover:translate-x-[30px] opacity-0 group-hover:opacity-100 transition-all duration-300 mb-[15px]">
                                            <a href="{{ $team->facebook }}" target="_blank" aria-label="facebook"
                                                class="w-[26px] h-[26px] transition-all rounded-full bg-[#FFF6F0] flex items-center justify-center hover:drop-shadow-[0px_4px_10px_rgba(0,0,0,0.25)] text-[#494949] hover:text-[#3B5998]">
                                                <svg width="7" height="12" viewBox="0 0 7 12" fill="currentColor"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M4.36 4.20156V3.12156C4.36 2.65356 4.468 2.40156 5.224 2.40156H6.16V0.601562H4.72C2.92 0.601562 2.2 1.78956 2.2 3.12156V4.20156H0.760002V6.00156H2.2V11.4016H4.36V6.00156H5.944L6.16 4.20156H4.36Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </a>
                                        </li>
                                    @endif
                                    @if($team->twitter)
                                        <li
                                            class="translate-x-[0px] group-hover:translate-x-[30px] opacity-0 group-hover:opacity-100 transition-all duration-500 mb-[15px]">
                                            <a href="{{ $team->twitter }}" target="_blank" aria-label="twitter"
                                                class="w-[26px] h-[26px] transition-all rounded-full bg-[#FFF6F0] flex items-center justify-center hover:drop-shadow-[0px_4px_10px_rgba(0,0,0,0.25)] text-[#494949] hover:text-[#3B5998]">
                                                <svg width="14" height="12" viewBox="0 0 14 12" fill="currentColor"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M13.6667 1.93957C13.1669 2.15783 12.6376 2.30093 12.096 2.36424C12.6645 2.0304 13.092 1.50098 13.2987 0.874908C12.76 1.18846 12.1725 1.40931 11.5607 1.52824C11.303 1.25838 10.9931 1.04383 10.6498 0.897693C10.3065 0.751554 9.93709 0.676884 9.564 0.678241C8.05333 0.678241 6.82866 1.88491 6.82866 3.37157C6.82866 3.58224 6.85266 3.78824 6.89933 3.98491C5.81571 3.93337 4.75474 3.65651 3.78411 3.172C2.81348 2.68749 1.9545 2.00596 1.26199 1.17091C1.01921 1.58051 0.891605 2.04809 0.892662 2.52424C0.893126 2.96955 1.00455 3.40773 1.21685 3.79917C1.42916 4.19061 1.73566 4.52298 2.10866 4.76624C1.67498 4.75224 1.25068 4.63646 0.869995 4.42824V4.46157C0.869995 5.76691 1.81333 6.85557 3.06333 7.10357C2.8284 7.16591 2.58638 7.1975 2.34333 7.19757C2.16666 7.19757 1.99533 7.18091 1.828 7.14757C2.00672 7.68619 2.34873 8.15578 2.80654 8.49113C3.26435 8.82648 3.81522 9.01095 4.38266 9.01891C3.40937 9.7686 2.21454 10.1736 0.985995 10.1702C0.764662 10.1702 0.547328 10.1569 0.333328 10.1329C1.5875 10.9267 3.04172 11.3471 4.52599 11.3449C9.55733 11.3449 12.308 7.24024 12.308 3.68091L12.2987 3.33224C12.8352 2.95469 13.2988 2.4828 13.6667 1.93957Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </a>
                                        </li>
                                    @endif
                                    @if($team->instagram)
                                        <li
                                            class="translate-x-[0px] group-hover:translate-x-[30px] opacity-0 group-hover:opacity-100 transition-all duration-700 last:mb-0 mb-[15px]">
                                            <a href="{{ $team->instagram }}" target="_blank" aria-label="instagram"
                                                class="w-[26px] h-[26px] transition-all rounded-full bg-[#FFF6F0] flex items-center justify-center hover:drop-shadow-[0px_4px_10px_rgba(0,0,0,0.25)] text-[#494949] hover:text-[#3B5998]">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="currentColor"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M7 3.79646C5.22656 3.79646 3.79531 5.22771 3.79531 7.00115C3.79531 8.77458 5.22656 10.2058 7 10.2058C8.77344 10.2058 10.2047 8.77458 10.2047 7.00115C10.2047 5.22771 8.77344 3.79646 7 3.79646ZM7 9.08396C5.85312 9.08396 4.91719 8.14802 4.91719 7.00115C4.91719 5.85427 5.85312 4.91834 7 4.91834C8.14687 4.91834 9.08281 5.85427 9.08281 7.00115C9.08281 8.14802 8.14687 9.08396 7 9.08396ZM10.3359 2.91834C9.92187 2.91834 9.5875 3.25271 9.5875 3.66677C9.5875 4.08084 9.92187 4.41521 10.3359 4.41521C10.75 4.41521 11.0844 4.0824 11.0844 3.66677C11.0845 3.56845 11.0652 3.47107 11.0277 3.38021C10.9901 3.28935 10.935 3.2068 10.8654 3.13727C10.7959 3.06775 10.7134 3.01262 10.6225 2.97506C10.5316 2.93749 10.4343 2.91821 10.3359 2.91834ZM13.2469 7.00115C13.2469 6.13865 13.2547 5.28396 13.2063 4.42302C13.1578 3.42302 12.9297 2.53552 12.1984 1.80427C11.4656 1.07146 10.5797 0.844898 9.57969 0.796461C8.71719 0.748023 7.8625 0.755836 7.00156 0.755836C6.13906 0.755836 5.28437 0.748023 4.42344 0.796461C3.42344 0.844898 2.53594 1.07302 1.80469 1.80427C1.07187 2.53709 0.84531 3.42302 0.796873 4.42302C0.748435 5.28552 0.756248 6.14021 0.756248 7.00115C0.756248 7.86209 0.748435 8.71834 0.796873 9.57927C0.84531 10.5793 1.07344 11.4668 1.80469 12.198C2.5375 12.9308 3.42344 13.1574 4.42344 13.2058C5.28594 13.2543 6.14062 13.2465 7.00156 13.2465C7.86406 13.2465 8.71875 13.2543 9.57969 13.2058C10.5797 13.1574 11.4672 12.9293 12.1984 12.198C12.9312 11.4652 13.1578 10.5793 13.2063 9.57927C13.2562 8.71834 13.2469 7.86365 13.2469 7.00115ZM11.8719 10.6855C11.7578 10.9699 11.6203 11.1824 11.4 11.4011C11.1797 11.6215 10.9687 11.759 10.6844 11.873C9.8625 12.1996 7.91094 12.1261 7 12.1261C6.08906 12.1261 4.13594 12.1996 3.31406 11.8746C3.02969 11.7605 2.81719 11.623 2.59844 11.4027C2.37812 11.1824 2.24062 10.9715 2.12656 10.6871C1.80156 9.86365 1.875 7.91209 1.875 7.00115C1.875 6.09021 1.80156 4.13709 2.12656 3.31521C2.24062 3.03084 2.37812 2.81834 2.59844 2.59959C2.81875 2.38084 3.02969 2.24177 3.31406 2.12771C4.13594 1.80271 6.08906 1.87615 7 1.87615C7.91094 1.87615 9.86406 1.80271 10.6859 2.12771C10.9703 2.24177 11.1828 2.37927 11.4016 2.59959C11.6219 2.8199 11.7594 3.03084 11.8734 3.31521C12.1984 4.13709 12.125 6.09021 12.125 7.00115C12.125 7.91209 12.1984 9.86365 11.8719 10.6855Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <div
                                class="bg-[#FFFDFC] z-[1] drop-shadow-[0px_2px_15px_rgba(0,0,0,0.1)] rounded-[0px_0px_6px_6px] px-3 md:px-[15px] py-[20px] border-b-[6px] border-primary transition-all duration-500 before:transition-all before:duration-300 group-hover:border-secondary relative">
                                <h3>
                                    @if($team->project)
                                        <a href="{{ route('project.properties.index', $team->project->slug) }}"
                                            class="font-lora font-normal text-base text-primary group-hover:text-secondary">{{ $team->name }}</a>
                                    @else
                                        <a href="javascript:void(0)"
                                            class="font-lora font-normal text-base text-primary group-hover:text-secondary">{{ $team->name }}</a>
                                    @endif
                                </h3>
                                <p class="font-normal text-[14px] leading-none capitalize mt-[5px] group-hover:text-body">
                                    {{ $team->designation }}
                                </p>

                                {{-- Agent associations (City, Project, Developer) --}}
                                <div class="mt-4 pt-3 border-t border-gray-100 flex flex-col gap-1 text-left">
                                    @if($team->city)
                                        <div class="text-[12px] text-gray-500">
                                            <span class="font-medium text-gray-400">City:</span>
                                            <a href="{{ route('city.index', $team->city->slug) }}"
                                                class="text-secondary hover:underline ml-1 font-medium">{{ $team->city->name }}</a>
                                        </div>
                                    @endif
                                    @if($team->project)
                                        <div class="text-[12px] text-gray-500">
                                            <span class="font-medium text-gray-400">Project:</span>
                                            <a href="{{ route('project.properties.index', $team->project->slug) }}"
                                                class="text-secondary hover:underline ml-1 font-medium">{{ $team->project->name }}</a>
                                        </div>
                                    @endif
                                    @if($team->developer)
                                        <div class="text-[12px] text-gray-500">
                                            <span class="font-medium text-gray-400">Developer:</span>
                                            <span class="text-primary ml-1 font-medium">{{ $team->developer->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback default teams if database not yet migrated/seeded -->
                    <div class="text-center group mb-[30px]">
                        <div class="relative rounded-[6px_6px_0px_0px]">
                            <a href="agent-details.html">
                                <img src="{{ asset('assets/images/team/person1.png') }}" class="w-auto h-auto block mx-auto"
                                    loading="lazy" width="215" height="310" alt="Amelia Margaret">
                            </a>
                            <ul class="flex flex-col absolute w-full top-[30px] left-0 overflow-hidden">
                                <li
                                    class="translate-x-[0px] group-hover:translate-x-[30px] opacity-0 group-hover:opacity-100 transition-all duration-300 mb-[15px]">
                                    <a href="#" aria-label="svg"
                                        class="w-[26px] h-[26px] transition-all rounded-full bg-[#FFF6F0] flex items-center justify-center hover:drop-shadow-[0px_4px_10px_rgba(0,0,0,0.25)] text-[#494949] hover:text-[#3B5998]">
                                        <svg width="7" height="12" viewBox="0 0 7 12" fill="currentColor"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M4.36 4.20156V3.12156C4.36 2.65356 4.468 2.40156 5.224 2.40156H6.16V0.601562H4.72C2.92 0.601562 2.2 1.78956 2.2 3.12156V4.20156H0.760002V6.00156H2.2V11.4016H4.36V6.00156H5.944L6.16 4.20156H4.36Z"
                                                fill="currentColor" />
                                        </svg>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div
                            class="bg-[#FFFDFC] z-[1] drop-shadow-[0px_2px_15px_rgba(0,0,0,0.1)] rounded-[0px_0px_6px_6px] px-3 md:px-[15px] py-[20px] border-b-[6px] border-primary transition-all duration-500 before:transition-all before:duration-300 group-hover:border-secondary relative">
                            <h3><a href="agent-details.html"
                                    class="font-lora font-normal text-base text-primary group-hover:text-secondary">Amelia
                                    Margaret</a></h3>
                            <p class="font-normal text-[14px] leading-none capitalize mt-[5px] group-hover:text-body">Real
                                Estate Broker</p>
                        </div>
                    </div>
                @endif
                <!-- single team end-->
            </div>
        </div>
    </section>
    <!-- Team Section End-->
    <x-partials.news-letter-section />
</x-partials.layout>