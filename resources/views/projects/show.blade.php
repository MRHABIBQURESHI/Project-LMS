<x-partials.layout>
    <!-- Hero section start -->
    <section
        class="bg-no-repeat bg-center bg-cover bg-[#FFF6F0] h-[350px] lg:h-[513px] flex flex-wrap items-center relative before:absolute before:inset-0 before:content-[''] before:bg-[#000000] before:opacity-[70%]"
        style="background-image: url({{ asset($proptery->banner_image ?? "assets/images/breadcrumb/bg-1.png") }});">
        <div class="container">
            <div class="grid grid-cols-12">
                <div class="col-span-12">
                    <div class="max-w-[600px]  mx-auto text-center text-white relative z-[1]">
                        <div class="mb-5"><span class="text-base block">{{ $proptery->project_name }} in
                                {{ $proptery->city_name }}</span></div>
                        <h1
                            class="font-lora text-[36px] sm:text-[50px] md:text-[68px] lg:text-[50px] leading-tight xl:text-2xl font-medium">
                            {{ $proptery->name ?? '' }}
                        </h1>

                        <p class="text-base mt-5 max-w-[500px] mx-auto text-center">
                            {!! $proptery->description ?? '' !!}
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
            <div class="grid grid-cols-12 mb-[-30px] gap-[30px] xl:gap-[50px]">
                <div class="col-span-12 md:col-span-6 lg:col-span-8 mb-[30px]">
                    <img src="{{ asset($proptery->image ?? 'assets/images/properties-details/post1.png') }}"
                        class="w-auto h-auto rounded-[8px]" loading="lazy"
                        alt="{{ $proptery->name ?? 'Property Image' }}" width="770" height="465">
                    <div class="mt-[45px] mb-[35px]">
                        <h2
                            class="font-lora leading-tight text-[22px] md:text-[28px] lg:text-[36px] text-primary mb-[5px] font-medium">
                            {{ $proptery->name ?? '' }}
                        </h2>
                        <div class="flex flex-wrap justify-between items-center mb-[20px] gap-4">
                            <h3 class="font-light text-[18px] text-secondary underline">{{ $proptery->address ?? '' }}
                            </h3>
                            <span
                                class="font-lora text-[20px] sm:text-[24px] text-secondary font-medium">{{ $proptery->formatted_price ?? '' }}</span>
                        </div>
                        <div class="prose max-w-none text-body">
                            {!! $proptery->description ?? '' !!}
                        </div>
                    </div>

                    <div
                        class="xl:flex xl:flex-nowrap xl:justify-between gap-y-[30px] gap-x-[15px] xl:gap-x-[0px] mb-[30px] items-center">
                        <div class="grid grid-cols-12 gap-y-[30px] gap-x-[15px] xl:gap-x-[20px] xl:mr-[30px]">
                            @if(!empty($proptery->gallery_images) && is_array($proptery->gallery_images))
                                @foreach($proptery->gallery_images as $index => $gallery_image)
                                    @php
                                        $colSpan = ($index % 4 === 0 || $index % 4 === 3) ? 'col-span-7' : 'col-span-5';
                                    @endphp
                                    <div class="{{ $colSpan }}">
                                        <a href="{{ asset($gallery_image) }}" class="gallery-image">
                                            <img class="object-cover rounded-[8px] w-full h-full"
                                                src="{{ asset($gallery_image) }}" alt="gallery image" loading="lazy" width="270"
                                                height="187">
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-7">
                                    <a href="{{ asset('assets/images/properties-details/gallery/01.png') }}"
                                        class="gallery-image">
                                        <img class="object-cover rounded-[8px] w-full h-full"
                                            src="{{ asset('assets/images/properties-details/01.png') }}" alt="gallery image"
                                            loading="lazy" width="270" height="187">
                                    </a>
                                </div>
                                <div class="col-span-5">
                                    <a href="{{ asset('assets/images/properties-details/gallery/03.png') }}"
                                        class="gallery-image">
                                        <img class="object-cover rounded-[8px] w-full h-full"
                                            src="{{ asset('assets/images/properties-details/03.png') }}" alt="gallery image"
                                            loading="lazy" width="170" height="187">
                                    </a>
                                </div>
                                <div class="col-span-5">
                                    <a href="{{ asset('assets/images/properties-details/gallery/07.png') }}"
                                        class="gallery-image">
                                        <img class="object-cover rounded-[8px] w-full h-full"
                                            src="{{ asset('assets/images/properties-details/07.png') }}" alt="gallery image"
                                            loading="lazy" width="170" height="187">
                                    </a>
                                </div>
                                <div class="col-span-7">
                                    <a href="{{ asset('assets/images/properties-details/gallery/05.png') }}"
                                        class="gallery-image">
                                        <img class="object-cover rounded-[8px] w-full h-full"
                                            src="{{ asset('assets/images/properties-details/05.png') }}" alt="gallery image"
                                            loading="lazy" width="270" height="187">
                                    </a>
                                </div>
                            @endif
                        </div>
                        <p class="xl:max-w-[270px] mt-7 xl:mt-0 text-[16px] leading-[2] font-normal">
                            Our premium properties offer modern co-living space, luxury specifications, and are situated
                            in prime locations with excellent amenities and nearby attractions. Discover your dream
                            space today.
                        </p>
                    </div>

                    <h4
                        class="font-lora text-primary text-[24px] leading-[1.277] sm:text-[28px] capitalize mt-[50px] mb-[40px] font-medium">
                        Property Amenities<span class="text-secondary">.</span>
                    </h4>

                    <ul
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 px-[15px] mx-[-15px] mt-[40px]">
                        @if($proptery->bedrooms)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>{{ $proptery->bedrooms }} Bedrooms</span>
                            </li>
                        @endif
                        @if($proptery->bathrooms)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>{{ $proptery->bathrooms }} Bathrooms</span>
                            </li>
                        @endif
                        @if($proptery->garages)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>{{ $proptery->garages }} Garages</span>
                            </li>
                        @endif
                        @if($proptery->air_conditioning)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Air Conditioning</span>
                            </li>
                        @endif
                        @if($proptery->alarm)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Alarm</span>
                            </li>
                        @endif
                        @if($proptery->balcony)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Balcony</span>
                            </li>
                        @endif
                        @if($proptery->cable_tv || $proptery->tv_cable)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Cable TV</span>
                            </li>
                        @endif
                        @if($proptery->central_heating)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Central Heating</span>
                            </li>
                        @endif
                        @if($proptery->dryer)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Dryer</span>
                            </li>
                        @endif
                        @if($proptery->dishwasher)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Dishwasher</span>
                            </li>
                        @endif
                        @if($proptery->garage)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Garage</span>
                            </li>
                        @endif
                        @if($proptery->gym)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Gym</span>
                            </li>
                        @endif
                        @if($proptery->library)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Library</span>
                            </li>
                        @endif
                        @if($proptery->laundry_room)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Laundry Room</span>
                            </li>
                        @endif
                        @if($proptery->microwave)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Microwave</span>
                            </li>
                        @endif
                        @if($proptery->oven)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>10 Nearby Restaurant</span>
                            </li>
                        @endif
                        @if($proptery->parking)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Parking</span>
                            </li>
                        @endif
                        @if($proptery->pets_allowed)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Pets Allowed</span>
                            </li>
                        @endif
                        @if($proptery->refrigerator)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Refrigerator</span>
                            </li>
                        @endif
                        @if($proptery->security_system)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Security System</span>
                            </li>
                        @endif
                        @if($proptery->swimming_pool)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Swimming Pool</span>
                            </li>
                        @endif
                        @if($proptery->tennis_court)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Tennis Court</span>
                            </li>
                        @endif
                        @if($proptery->wifi)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Wifi</span>
                            </li>
                        @endif
                        @if($proptery->washer)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Washer</span>
                            </li>
                        @endif
                        @if($proptery->wine_cellar)
                            <li class="flex flex-wrap items-center mb-[25px]">
                                <img class="mr-[15px]" src="{{ asset('assets/images/about/check.png') }}" loading="lazy"
                                    alt="icon" width="20" height="20">
                                <span>Wine Cellar</span>
                            </li>
                        @endif
                    </ul>
                    <h5
                        class="font-lora text-primary text-[24px] sm:text-[28px] leading-[1.277] capitalize lg:mt-[25px] mb-[40px] font-medium">

                        Floor Plan<span class="text-secondary">.</span>
                    </h5>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-[30px]">
                        @if(!empty($proptery->floor_plan_images) && is_array($proptery->floor_plan_images))
                            @foreach($proptery->floor_plan_images as $index => $floor_plan)
                                <div class="text-center">
                                    <img src="{{ asset($floor_plan) }}" alt="Floor Plan">
                                    <p>{{ $index == 0 ? 'Ground floor' : ($index == 1 ? '1st Floor' : ($index + 1) . ' Floor') }}
                                    </p>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center">
                                <img src="{{ asset('assets/images/floor-plan/floor1.png') }}" alt="Floor Plan">
                                <p>Ground floor</p>
                            </div>

                            <div class="text-center">
                                <img src="{{ asset('assets/images/floor-plan/floor3.png') }}" alt="Floor Plan">
                                <p>1st Floor</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-span-12 md:col-span-6 lg:col-span-4 mb-[30px]">
                    <aside class="mb-[-60px] asidebar">
                        <div class="mb-[60px]">
                            <h3 class="text-primary leading-none text-[24px] font-lora underline mb-[40px] font-medium">
                                Property Search <span class="text-secondary">.</span></h3>

                            <form
                                action="{{ route('project.properties.show', [$proptery->project_slug, $proptery->slug]) }}"
                                method="GET" class="relative">
                                <div class="relative mb-[25px] bg-white">
                                    <input name="q" value="{{ request('q') }}"
                                        class="font-light w-full leading-[1.75] placeholder:opacity-100 placeholder:text-body border border-primary border-opacity-60 rounded-[8px] pl-[40px] pr-[20px] py-[8px] focus:border-secondary focus:border-opacity-60 focus:outline-none focus:drop-shadow-[0px_6px_15px_rgba(0,0,0,0.1)] bg-white"
                                        type="text" placeholder="Search" value="{{ request('q') }}">
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
                                <div class="relative mb-[25px] bg-white">
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
                                        class="font-light w-full border border-primary border-opacity-60 rounded-[8px] pl-[40px] pr-[20px] py-[10px] focus:outline-none bg-white appearance-none cursor-pointer text-[14px]">
                                        <option value="">Property Category</option>
                                        @foreach ($propertyTypes as $propertyType)
                                            <option value="{{ $propertyType->id }}" {{ request('property_type_id') == $propertyType->id ? 'selected' : '' }}>
                                                {{ $propertyType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="relative mb-[25px] bg-white">
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
                                        class="font-light w-full h-[45px] border border-primary border-opacity-60 rounded-[8px] pl-[40px] pr-[20px] py-[8px] focus:outline-none bg-white appearance-none cursor-pointer text-[14px]">
                                        <option value="">City</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="relative mb-[25px] bg-white">
                                    <svg class="absolute top-1/2 -translate-y-1/2 z-[1] left-[20px] pointer-events-none"
                                        width="16" height="16" viewBox="0 0 16 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5.78125 9.55323C5.78125 10.4132 6.44125 11.1066 7.26125 11.1066H8.93458C9.64792 11.1066 10.2279 10.4999 10.2279 9.75323C10.2279 8.9399 9.87458 8.65323 9.34792 8.46657L6.66125 7.53323C6.13458 7.34657 5.78125 7.0599 5.78125 6.24657C5.78125 5.4999 6.36125 4.89323 7.07458 4.89323H8.74792C9.56792 4.89323 10.2279 5.58657 10.2279 6.44657"
                                            stroke="#0B2C3D" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M8 4V12" stroke="#0B2C3D" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M7.9987 14.6667C11.6806 14.6667 14.6654 11.6819 14.6654 8C14.6654 4.3181 11.6806 1.33333 7.9987 1.33333C4.3168 1.33333 1.33203 4.3181 1.33203 8C1.33203 11.6819 4.3168 14.6667 7.9987 14.6667Z"
                                            stroke="#0B2C3D" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <select name="project_id"
                                        class="font-light w-full border border-primary border-opacity-60 rounded-[8px] pl-[40px] pr-[20px] py-[10px] focus:outline-none bg-white appearance-none cursor-pointer text-[14px]">
                                        <option value="">Project</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="relative mb-[25px] bg-white">
                                    <svg class="absolute top-1/2 -translate-y-1/2 z-[1] left-[20px] pointer-events-none"
                                        width="14" height="14" viewBox="0 0 14 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.33268 4.66667H4.66602V9.33334H9.33268V4.66667Z" stroke="#0B2C3D"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M2.91602 12.8333C3.87852 12.8333 4.66602 12.0458 4.66602 11.0833V9.33333H2.91602C1.95352 9.33333 1.16602 10.1208 1.16602 11.0833C1.16602 12.0458 1.95352 12.8333 2.91602 12.8333Z"
                                            stroke="#0B2C3D" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M2.91602 4.66667H4.66602V2.91667C4.66602 1.95417 3.87852 1.16667 2.91602 1.16667C1.95352 1.16667 1.16602 1.95417 1.16602 2.91667C1.16602 3.87917 1.95352 4.66667 2.91602 4.66667Z"
                                            stroke="#0B2C3D" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M9.33398 4.66667H11.084C12.0465 4.66667 12.834 3.87917 12.834 2.91667C12.834 1.95417 12.0465 1.16667 11.084 1.16667C10.1215 1.16667 9.33398 1.95417 9.33398 2.91667V4.66667Z"
                                            stroke="#0B2C3D" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M11.084 12.8333C12.0465 12.8333 12.834 12.0458 12.834 11.0833C12.834 10.1208 12.0465 9.33333 11.084 9.33333H9.33398V11.0833C9.33398 12.0458 10.1215 12.8333 11.084 12.8333Z"
                                            stroke="#0B2C3D" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <select name="property_size"
                                        class="font-light w-full border border-primary border-opacity-60 rounded-[8px] pl-[40px] pr-[20px] py-[10px] focus:outline-none bg-white appearance-none cursor-pointer text-[14px]">
                                        <option value="">Property Size</option>
                                        @foreach ($propertySizes as $propertySize)
                                            <option value="{{ $propertySize->size }}" {{ request('property_size') == $propertySize->size ? 'selected' : '' }}>
                                                {{ $propertySize->size }} Sq.ft
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit"
                                    class="block z-[1] before:rounded-md before:block before:absolute before:left-auto before:right-0 before:inset-y-0 before:z-[-1] before:bg-secondary before:w-0 hover:before:w-full hover:before:left-0 hover:before:right-auto before:transition-all leading-none px-[30px] py-[12px] capitalize font-medium text-white text-[14px] xl:text-[16px] relative after:block after:absolute after:inset-0 after:z-[-2] after:bg-primary after:rounded-md after:transition-all">Search</button>

                            </form>
                        </div>

                        <div class="mb-[60px]">
                            <h3 class="text-primary leading-none text-[24px] font-lora underline mb-[40px] font-medium">
                                Featured Property<span class="text-secondary">.</span></h3>
                            <div class="                            <div class=" sidebar-carousel relative">
                                <div class="swiper p-1">
                                    <!-- Additional required wrapper -->
                                    <div class="swiper-wrapper">
                                        @foreach($featured_properties as $featured)
                                            <div class="swiper-slide">
                                                <div
                                                    class="overflow-hidden rounded-md drop-shadow-[0px_2px_3px_rgba(0,0,0,0.1)] bg-[#FFFDFC] text-center mb-[40px]">
                                                    <div class="relative">
                                                        <a href="{{ route('project.properties.show', [$featured->project->slug, $featured->slug]) }}"
                                                            class="block">
                                                            <img src="{{ asset($featured->image ?? 'assets/images/properties/propertie-slider-1.png') }}"
                                                                class="w-full h-[220px] object-cover" loading="lazy"
                                                                alt="{{ $featured->name }}">
                                                        </a>
                                                    </div>

                                                    <div class="pt-[15px] pb-[20px] px-[20px] text-left">
                                                        <h3>
                                                            <a href="{{ route('project.properties.show', [$featured->project->slug, $featured->slug]) }}"
                                                                class="font-lora leading-tight text-[18px] text-primary hover:text-secondary font-medium">
                                                                {{ $featured->name }}
                                                            </a>
                                                        </h3>
                                                        <h4 class="leading-none mt-1">
                                                            <span
                                                                class="font-light text-[14px] leading-[1.75] text-gray-500 underline">
                                                                {{ $featured->address }}
                                                            </span>
                                                        </h4>
                                                        <ul class="mt-[10px]">
                                                            <li class="flex flex-wrap items-center justify-between">
                                                                <span
                                                                    class="font-lora text-[14px] text-secondary leading-none font-medium">
                                                                    {{ $featured->formatted_price }}
                                                                </span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- If we need navigation buttons -->
                                <div class="flex flex-wrap items-center justify-center mt-[25px]">
                                    <div
                                        class="swiper-button-prev w-[26px] h-[26px] rounded-full bg-primary  text-white hover:bg-secondary static mx-[5px] mt-[0px]">
                                    </div>
                                    <div
                                        class="swiper-button-next w-[26px] h-[26px] rounded-full bg-primary  text-white hover:bg-secondary static mx-[5px] mt-[0px]">
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="mb-[60px]">
                            <h3 class="text-primary leading-none text-[24px] font-lora underline mb-[30px] font-medium">
                                Our Agents<span class="text-secondary">.</span></h3>

                            @php
                                // Get agents assigned to this property's project
                                $property_agents = collect();
                                if (isset($proptery->project_id)) {
                                    $property_agents = \App\Models\Agent::where('project_id', $proptery->project_id)
                                        ->where('status', true)->get();
                                }
                                // If no assigned agents, get 2 random agents
                                if ($property_agents->isEmpty()) {
                                    $property_agents = \App\Models\Agent::where('status', true)
                                        ->inRandomOrder()->take(2)->get();
                                }
                            @endphp

                            <div class="grid sm:grid-cols-2 lg:grid-cols-2 gap-x-[20px] mb-[-20px]">
                                @forelse($property_agents->take(2) as $agent)
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

                                <!-- single team end-->
                            </div>
                        </div>

                        {{-- Tags
                        <div class="mb-[60px]">
                            <h3 class="text-primary leading-none text-[24px] font-lora underline mb-[40px] font-medium">
                                Tags<span class="text-secondary">.</span></h3>
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
                        </div> --}}
                    </aside>
                </div>
            </div>
        </div>
    </section>
    <!-- Popular Properties end -->

</x-partials.layout>