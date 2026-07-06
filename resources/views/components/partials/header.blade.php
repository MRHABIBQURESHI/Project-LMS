<!-- header top start -->
<div class="bg-primary font-lora text-white py-[11px]">
    <div class="container">
        <div class="grid items-center grid-cols-12 gap-x-[30px]">
            <div class="col-span-12 sm:col-span-6 text-center sm:text-left">
                <p>Have a question? <a class="hover:text-secondary"
                        href="tel:{{ $global_setting->phone_number[0] }}">{{ $global_setting->phone_number[0] }}</a></p>
            </div>
            <div class="col-span-12 sm:col-span-6 text-center sm:text-right">
                <p>Visit us: {{ $global_setting->working_hours }}</p>
            </div>
        </div>
    </div>
</div>

<header id="sticky-header" class="absolute left-0 top-[15px] lg:top-[30px] xl:top-[40px] w-full z-10">
    <div class="container">
        <div class="grid grid-cols-12">
            <div class="col-span-12">
                <div class="flex flex-wrap items-center justify-between">
                    <a href="{{ route('home') }}" class="block">
                        <img class="w-full h-full white-logo" src="{{ asset($global_setting->white_logo) }}"
                            loading="lazy" width="99" height="46" alt="{{ config('app.name') }} logo">
                        <img class="w-full h-full hidden dark-logo" src="{{ asset($global_setting->black_logo) }}"
                            loading="lazy" width="99" height="46" alt="{{ config('app.name') }} logo">
                    </a>
                    <nav class="flex flex-wrap items-center">
                        <ul
                            class="hidden lg:flex flex-wrap items-center font-lora text-[16px] xl:text-[18px] leading-none text-black">
                            <li class="mr-7 xl:mr-[40px] relative group py-[20px]">
                                <a href="{{ route('home') }}"
                                    class="sticky-dark transition-all text-white hover:text-secondary">Home</a>
                            </li>
                            <li class="mr-7 xl:mr-[40px] relative group py-[20px]">
                                <a href="{{ route('projects.index') }}"
                                    class="sticky-dark transition-all text-white hover:text-secondary">Projects</a>
                            </li>
                            <li class="mr-7 xl:mr-[40px] relative group py-[20px]">
                                <a href="{{ route('about') }}"
                                    class="sticky-dark transition-all text-white hover:text-secondary">About</a>
                            </li>
                            <li class="mr-7 xl:mr-[40px] relative group py-[20px]">
                                <a href="{{ route('contact') }}"
                                    class="sticky-dark transition-all text-white hover:text-secondary">Contact</a>
                            </li>
                        </ul>

                        <ul class="flex flex-wrap items-center">
                            @if(auth()->check() && auth()->user()->role === 'admin')
                                <li>
                                    <a href="{{ route('panel.dashboard') }}"
                                        class="sticky-btn before:rounded-md before:block before:absolute before:left-auto before:right-0 before:inset-y-0 before:-z-[1] before:bg-white before:w-0 hover:before:w-full hover:before:left-0 hover:before:right-auto hover:text-primary before:transition-all leading-none px-[20px] py-[15px] capitalize font-medium text-white hidden sm:block text-[14px] xl:text-[16px] relative after:block after:absolute after:inset-0 after:-z-[2] after:bg-secondary after:rounded-md after:transition-all">Dashboard</a>
                                </li>
                            @else
                                <li>
                                    <a href="{{ route('contact') }}"
                                        class="sticky-btn before:rounded-md before:block before:absolute before:left-auto before:right-0 before:inset-y-0 before:-z-[1] before:bg-white before:w-0 hover:before:w-full hover:before:left-0 hover:before:right-auto hover:text-primary before:transition-all leading-none px-[20px] py-[15px] capitalize font-medium text-white hidden sm:block text-[14px] xl:text-[16px] relative after:block after:absolute after:inset-0 after:-z-[2] after:bg-secondary after:rounded-md after:transition-all">Let's Connect</a>
                                </li>
                            @endif
                            <li class="ml-2 sm:ml-5 lg:hidden">
                                <a href="#offcanvas-mobile-menu"
                                    class="offcanvas-toggle flex text-[#016450] hover:text-secondary">
                                    <svg width="24" height="24" class="fill-current" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 448 512">
                                        <path
                                            d="M0 96C0 78.33 14.33 64 32 64H416C433.7 64 448 78.33 448 96C448 113.7 433.7 128 416 128H32C14.33 128 0 113.7 0 96zM0 256C0 238.3 14.33 224 32 224H416C433.7 224 448 238.3 448 256C448 273.7 433.7 288 416 288H32C14.33 288 0 273.7 0 256zM416 448H32C14.33 448 0 433.7 0 416C0 398.3 14.33 384 32 384H416C433.7 384 448 398.3 448 416C448 433.7 433.7 448 416 448z" />
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- offcanvas-overlay start -->
<div class="offcanvas-overlay hidden fixed inset-0 bg-black opacity-50 z-50"></div>
<!-- offcanvas-overlay end -->
<!-- offcanvas-mobile-menu start -->
<div id="offcanvas-mobile-menu"
    class="offcanvas left-0 transform -translate-x-full fixed font-normal text-sm top-0 z-50 h-screen xs:w-[300px] lg:w-[380px] transition-all ease-in-out duration-300 bg-white">
    <div class="py-12 pl-6 pr-6 h-[100vh] overflow-y-auto">
        <!-- close button start -->
        <button class="offcanvas-close text-primary text-[25px] w-10 h-10 absolute right-4 top-4 z-[1] hover:text-secondary transition-all"
            aria-label="offcanvas">x</button>
        <!-- close button end -->

        <!-- logo start -->
        <a href="{{ route('home') }}" class="block mb-[30px] pl-5">
            <img src="{{ asset($global_setting->black_logo) }}" width="99" height="46" loading="lazy"
                alt="{{ config('app.name') }} logo">
        </a>
        <!-- logo end -->

        <!-- offcanvas-menu start -->
        <nav class="offcanvas-menu mr-[20px]">
            <ul>
                <li class="relative block border-b-primary border-b first:border-t first:border-t-primary">
                    <a href="{{ route('home') }}"
                        class="block capitalize font-normal text-black hover:text-secondary text-base my-2 py-1 px-5">Home</a>
                </li>
                <li class="relative block border-b-primary border-b">
                    <a href="{{ route('projects.index') }}"
                        class="block capitalize font-normal text-black hover:text-secondary text-base my-2 py-1 px-5">Projects</a>
                </li>
                <li class="relative block border-b-primary border-b">
                    <a href="{{ route('about') }}"
                        class="block capitalize font-normal text-black hover:text-secondary text-base my-2 py-1 px-5">About</a>
                </li>
                <li class="relative block border-b-primary border-b">
                    <a href="{{ route('contact') }}"
                        class="block capitalize font-normal text-black hover:text-secondary text-base my-2 py-1 px-5">Contact</a>
                </li>
            </ul>
        </nav>

        <!-- CTA button start -->
        <div class="mt-8 px-5">
            @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('panel.dashboard') }}"
                    class="block text-center py-3 px-5 bg-secondary text-white font-medium rounded-md hover:bg-primary transition-all text-base">Dashboard</a>
            @else
                <a href="{{ route('contact') }}"
                    class="block text-center py-3 px-5 bg-secondary text-white font-medium rounded-md hover:bg-primary transition-all text-base">Let's Connect</a>
            @endif
        </div>
        <!-- CTA button end -->
    </div>
</div>
<!-- offcanvas-mobile-menu end -->