<!-- ===== Sidebar Start ===== -->
<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0">
    <!-- SIDEBAR HEADER -->
    <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
        class="flex items-center gap-2 pt-8 sidebar-header pb-7">
        <a href="{{ route('distributor.dashboard') }}">
            <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
                <img class="dark:hidden" src="{{ asset('images/logo/logo.svg') }}" alt="Logo" />
                <img class="hidden dark:block" src="{{ asset('images/logo/logo-dark.svg') }}" alt="Logo" />
            </span>

            <img class="logo-icon" :class="sidebarToggle ? 'lg:block' : 'hidden'"
                src="{{ asset('images/logo/logo-icon.svg') }}" alt="Logo" />
        </a>
    </div>
    <!-- SIDEBAR HEADER -->

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <!-- Sidebar Menu -->
        <nav x-data="{ selected: $persist('Dashboard') }">
            <!-- Menu Group -->
            <div>
                <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
                        MENU
                    </span>

                    <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'"
                        class="mx-auto fill-current menu-group-icon" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                            fill="" />
                    </svg>
                </h3>

                <ul class="flex flex-col gap-4 mb-6">


                    <!-- Menu Item Dashboard -->
                    <li>
                        <a href="{{ route('distributor.dashboard') }}"
                            @click="selected = (selected === 'Dashboard' ? '':'Dashboard')" class="menu-item group"
                            :class="(selected === 'Dashboard') || (page === 'dashboard') ? 'menu-item-active' :
                            'menu-item-inactive'">
                            <svg :class="(selected === 'Dashboard') || (page === 'dashboard') ? 'menu-item-icon-active' :
                            'menu-item-icon-inactive'"
                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"
                                    fill="" />
                            </svg>

                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Dashboard
                            </span>
                        </a>
                    </li>


<li>
    <a href="{{ route('distributor.orders.index') }}"
        @click="selected = (selected === 'Manage Orders' ? '':'Manage Orders')" class="menu-item group"
        :class="(selected === 'Manage Orders') || (page === 'manage-orders') ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Manage Orders') || (page === 'manage-orders') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Clipboard list icon for Manage Orders -->
            <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5C15 6.10457 14.1046 7 13 7H11C9.89543 7 9 6.10457 9 5Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M9 12H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M9 16H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
            Manage Orders
        </span>
    </a>
</li>

<li>
    <a href="{{ route('distributor.retailers.index') }}"
        @click="selected = (selected === 'Retail Network' ? '':'Retail Network')" class="menu-item group"
        :class="(selected === 'Retail Network') || (page === 'retail-network') ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Retail Network') || (page === 'retail-network') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Network/connection icon for Retail Network -->
            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M19 12C19 15.866 15.866 19 12 19C8.13401 19 5 15.866 5 12C5 8.13401 8.13401 5 12 5C15.866 5 19 8.13401 19 12Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2 12H5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M19 12H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M12 2V5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M12 19V22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
            Retail Network
        </span>
    </a>
</li>

<li>
    <a href="{{ route('distributor.stock.index') }}"
        @click="selected = (selected === 'Stock Overview' ? '':'Stock Overview')" class="menu-item group"
        :class="(selected === 'Stock Overview') || (page === 'stock-overview') ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Stock Overview') || (page === 'stock-overview') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Warehouse/inventory icon for Stock Overview -->
            <path d="M3 8L12 3L21 8V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V8Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M3 8L12 13L21 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M12 21V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M9 21V11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M15 21V11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M7 11H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
            Stock Overview
        </span>
    </a>
</li>

<li>
    <a href="{{ route('distributor.retailer-sales.index') }}"
        @click="selected = (selected === 'Retail Sale' ? '':'Retail Sale')" class="menu-item group"
        :class="(selected === 'Retail Sale') || (page === 'retail-sale') ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Retail Sale') || (page === 'retail-sale') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Shopping cart icon for Retail Sale -->
            <path d="M3 6H22L19 16H6L3 6Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M16 20C17.1046 20 18 19.1046 18 18C18 16.8954 17.1046 16 16 16C14.8954 16 14 16.8954 14 18C14 19.1046 14.8954 20 16 20Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M8 20C9.10457 20 10 19.1046 10 18C10 16.8954 9.10457 16 8 16C6.89543 16 6 16.8954 6 18C6 19.1046 6.89543 20 8 20Z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M3 6L2 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M9 11H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
            Retail Sale
        </span>
    </a>
</li>

<li>
    <a href="#"
        @click="selected = (selected === 'Retail Orders' ? '':'Retail Orders')" class="menu-item group"
        :class="(selected === 'Retail Orders') || (page === 'retail-orders') ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Retail Orders') || (page === 'retail-orders') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Package delivery icon for Retail Orders -->
            <path d="M12 2L20 7V17L12 22L4 17V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M12 22V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M20 7L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M4 7L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M8 5L16 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M8 15L16 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M16 5L8 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
            Retail Orders
        </span>
    </a>
</li>







                </ul>
            </div>









        </nav>
        <!-- Sidebar Menu -->

    </div>
</aside>
<!-- ===== Sidebar End ===== -->
