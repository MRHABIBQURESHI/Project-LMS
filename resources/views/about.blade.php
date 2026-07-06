<x-partials.layout :title="'About Us'">
    <!-- Hero section start -->
    <section
        class="bg-no-repeat bg-left-bottom xl:bg-right-bottom bg-contain xl:bg-cover bg-[#E9F1FF] h-[450px] lg:h-[500px] xl:h-[650px] flex flex-wrap items-center relative">
        <div class="container">
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <div class="max-w-[420px] text-center mx-auto">
                        <div class="mb-5"><span class="text-base text-secondary block">About us</span></div>
                        <h1
                            class="font-lora text-primary text-[36px] sm:text-[50px] md:text-[68px] lg:text-[50px] leading-tight xl:text-2xl title font-medium">
                            About {{ config('app.name') }}<span class="text-secondary">.</span>
                        </h1>
                        <p class="text-base mt-5">Huge number of propreties availabe here for buy and sell, also you
                            can find here co-living property</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero section end -->


    <!-- About section start -->
    <section class="relative z-[1] mt-[80px] xl:mt-0">
        <div class="container">
            <div class="items-center">
                <div class="lg:mb-[60px] mb-10 -mt-[150px]">
                    <img class="mx-auto w-full" src="{{ asset('assets/images/about/about5.png') }}" width="597"
                        height="716" alt="about image">
                </div>
                <div class="max-w-[830px] mx-auto text-center">
                    <span class="text-secondary text-tiny inline-block mb-2">Since 1975</span>
                    <h2
                        class="font-lora text-primary text-[24px] sm:text-[30px] leading-[1.3888] xl:text-[35px] capitalize mb-[30px] lg:mb-[50px] font-medium">
                        We Provide Right Choice of Properties that You need and have great opportunity to choose
                        from thousands of Collection<span class="text-secondary">.</span></h2>
                    <div class="flex justify-center">
                        <ul class="flex flex-wrap list-none">
                            <li class="block">
                                <span class="font-lora text-secondary text-xl"><span class="counter-up">20</span>
                                    <span>k+</span></span>
                                <p>Properties</p>
                            </li>
                            <li class="block pl-[30px] sm:pl-[40px] md:pl-[60px]">
                                <span class="font-lora text-secondary text-xl"><span class="counter-up">12</span>
                                    <span>k+</span></span>
                                <p>Customers</p>
                            </li>
                            <li class="block pl-[30px] sm:pl-[40px] md:pl-[60px]">
                                <span class="font-lora text-secondary text-xl"><span class="counter-up">160</span>
                                    <span>+</span></span>
                                <p>Awards Win</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About section end -->


    <!-- About Us Sectin Start -->
    <section class="about-section pt-[80px] lg:pt-[120px]">
        <div class="container">
            <div class="grid grid-cols-12 gap-6 items-center">
                <div class="col-span-12 lg:col-span-6">
                    <span class="text-secondary text-tiny inline-block mb-2">Why Choose us</span>
                    <h2
                        class="font-lora text-primary text-[24px] sm:text-[30px] leading-[1.277] xl:text-xl capitalize mb-5 lg:mb-16 font-medium max-w-[500px]">
                        We Provide Latest Properties for our valuable Clients.<span class="text-secondary">.</span>
                    </h2>
                    <div class="scene" data-relative-input="true">
                        <img data-depth="0.1" src="{{ asset('assets/images/about/about7.png') }}" class=""
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

    <x-partials.news-letter-section />

</x-partials.layout>