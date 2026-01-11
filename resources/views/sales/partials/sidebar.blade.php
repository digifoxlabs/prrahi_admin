<!-- ===== Sidebar Start ===== -->
<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0">
    <!-- SIDEBAR HEADER -->
    <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
        class="flex items-center gap-2 pt-8 sidebar-header pb-7">
        <a href="{{ route('sales.dashboard') }}">
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


              <li>
    <a href="{{ route('sales.dashboard') }}"
        @click="selected = (selected === 'Dashboard' ? '':'Dashboard')" class="menu-item group"
        :class="(selected === 'Dashboard') || (page === 'dashboard') ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Dashboard') || (page === 'dashboard') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
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
    <a href="{{ route('sales.orders.index') }}"
        @click="selected = (selected === 'Manage Orders' ? '':'Manage Orders')" class="menu-item group"
        :class="(selected === 'Manage Orders') || (page === 'orders') ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Manage Orders') || (page === 'manage-orders') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Clipboard with check marks in dashboard style -->
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M8.75 3.75C8.75 2.92157 9.42157 2.25 10.25 2.25H13.75C14.5784 2.25 15.25 2.92157 15.25 3.75V5.25H8.75V3.75ZM7.25 5.25V3.75C7.25 2.09315 8.59315 0.75 10.25 0.75H13.75C15.4069 0.75 16.75 2.09315 16.75 3.75V5.25H18.5C19.7426 5.25 20.75 6.25736 20.75 7.5V19.5C20.75 20.7426 19.7426 21.75 18.5 21.75H5.5C4.25736 21.75 3.25 20.7426 3.25 19.5V7.5C3.25 6.25736 4.25736 5.25 5.5 5.25H7.25ZM4.75 7.5C4.75 7.08579 5.08579 6.75 5.5 6.75H18.5C18.9142 6.75 19.25 7.08579 19.25 7.5V19.5C19.25 19.9142 18.9142 20.25 18.5 20.25H5.5C5.08579 20.25 4.75 19.9142 4.75 19.5V7.5ZM8.5 11.25C8.08579 11.25 7.75 11.5858 7.75 12C7.75 12.4142 8.08579 12.75 8.5 12.75H15.5C15.9142 12.75 16.25 12.4142 16.25 12C16.25 11.5858 15.9142 11.25 15.5 11.25H8.5ZM7.75 16C7.75 15.5858 8.08579 15.25 8.5 15.25H12.5C12.9142 15.25 13.25 15.5858 13.25 16C13.25 16.4142 12.9142 16.75 12.5 16.75H8.5C8.08579 16.75 7.75 16.4142 7.75 16Z"
                fill="" />
        </svg>
        <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
            Manage Orders
        </span>
    </a>
</li>

<li>
    <a href="{{ route('sales.retailers.index') }}"
        @click="selected = (selected === 'Retail Network' ? '':'Retail Network')" class="menu-item group"
        :class="(selected === 'Retail Network') || (page === 'retail-network') ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Retail Network') || (page === 'retail-network') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Network nodes in dashboard style -->
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M12 7.75C9.65279 7.75 7.75 9.65279 7.75 12C7.75 14.3472 9.65279 16.25 12 16.25C14.3472 16.25 16.25 14.3472 16.25 12C16.25 9.65279 14.3472 7.75 12 7.75ZM6.25 12C6.25 8.82436 8.82436 6.25 12 6.25C15.1756 6.25 17.75 8.82436 17.75 12C17.75 15.1756 15.1756 17.75 12 17.75C8.82436 17.75 6.25 15.1756 6.25 12ZM12 5.25C8.27208 5.25 5.25 8.27208 5.25 12C5.25 15.7279 8.27208 18.75 12 18.75C15.7279 18.75 18.75 15.7279 18.75 12C18.75 8.27208 15.7279 5.25 12 5.25ZM2.75 12C2.75 6.89137 6.89137 2.75 12 2.75C17.1086 2.75 21.25 6.89137 21.25 12C21.25 17.1086 17.1086 21.25 12 21.25C6.89137 21.25 2.75 17.1086 2.75 12Z"
                fill="" />
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M12 1.75C12.4142 1.75 12.75 2.08579 12.75 2.5V5C12.75 5.41421 12.4142 5.75 12 5.75C11.5858 5.75 11.25 5.41421 11.25 5V2.5C11.25 2.08579 11.5858 1.75 12 1.75ZM12 18.25C12.4142 18.25 12.75 18.5858 12.75 19V21.5C12.75 21.9142 12.4142 22.25 12 22.25C11.5858 22.25 11.25 21.9142 11.25 21.5V19C11.25 18.5858 11.5858 18.25 12 18.25ZM2.5 11.25C2.91421 11.25 3.25 11.5858 3.25 12C3.25 12.4142 2.91421 12.75 2.5 12.75H1C0.585786 12.75 0.25 12.4142 0.25 12C0.25 11.5858 0.585786 11.25 1 11.25H2.5ZM21.5 11.25C21.9142 11.25 22.25 11.5858 22.25 12C22.25 12.4142 21.9142 12.75 21.5 12.75H20C19.5858 12.75 19.25 12.4142 19.25 12C19.25 11.5858 19.5858 11.25 20 11.25H21.5Z"
                fill="" />
        </svg>
        <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
            Retail Network
        </span>
    </a>
</li>

<li>
    <a href="{{ route('sales.distributors.index') }}"
        @click="selected = (selected === 'Distributors' ? '':'Distributors')" class="menu-item group"
        :class="(selected === 'Distributors') || (page === 'distributors') ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Distributors') || (page === 'distributors') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Building/Organization icon for Distributors -->
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M7 2.75C5.48122 2.75 4.25 3.98122 4.25 5.5V18.5C4.25 20.0188 5.48122 21.25 7 21.25H17C18.5188 21.25 19.75 20.0188 19.75 18.5V5.5C19.75 3.98122 18.5188 2.75 17 2.75H7ZM5.75 5.5C5.75 4.80964 6.30964 4.25 7 4.25H17C17.6904 4.25 18.25 4.80964 18.25 5.5V18.5C18.25 19.1904 17.6904 19.75 17 19.75H7C6.30964 19.75 5.75 19.1904 5.75 18.5V5.5ZM8.75 7C8.75 6.58579 9.08579 6.25 9.5 6.25H14.5C14.9142 6.25 15.25 6.58579 15.25 7C15.25 7.41421 14.9142 7.75 14.5 7.75H9.5C9.08579 7.75 8.75 7.41421 8.75 7ZM8.75 11C8.75 10.5858 9.08579 10.25 9.5 10.25H14.5C14.9142 10.25 15.25 10.5858 15.25 11C15.25 11.4142 14.9142 11.75 14.5 11.75H9.5C9.08579 11.75 8.75 11.4142 8.75 11ZM9.5 14.25C9.08579 14.25 8.75 14.5858 8.75 15C8.75 15.4142 9.08579 15.75 9.5 15.75H14.5C14.9142 15.75 15.25 15.4142 15.25 15C15.25 14.5858 14.9142 14.25 14.5 14.25H9.5Z"
                fill="" />
        </svg>
        <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
            Manage Distributors
        </span>
    </a>
</li>

<li>
    <a href="{{ route('sales.retail.orders.index') }}"
        @click="selected = (selected === 'Retail Orders' ? '':'Retail Orders')" class="menu-item group"
        :class="(selected === 'Retail Orders') || (page === 'retail-orders') ? 'menu-item-active' : 'menu-item-inactive'">
        <svg :class="(selected === 'Retail Orders') || (page === 'retail-orders') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Shopping cart in dashboard style -->
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M2.25 4.5C2.25 4.08579 2.58579 3.75 3 3.75H4.81802C5.31334 3.75 5.77049 4.03229 5.99426 4.47957L9.17574 10.7704C9.39951 11.2177 9.85666 11.5 10.352 11.5H17.8983C18.3343 11.5 18.7435 11.2673 18.9755 10.8867L21.9755 5.88665C22.3137 5.32461 21.925 2.75 21.2931 2.75H6C5.58579 2.75 5.25 3.08579 5.25 3.5C5.25 3.91421 5.58579 4.25 6 4.25H20.6448L18.0453 8.5H10.352C9.42595 8.5 8.55266 8.08419 7.96968 7.37085L5.22901 3.75H3C2.58579 3.75 2.25 4.08579 2.25 4.5ZM9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18C8.32843 18 9 18.6716 9 19.5ZM18 19.5C18 20.3284 17.3284 21 16.5 21C15.6716 21 15 20.3284 15 19.5C15 18.6716 15.6716 18 16.5 18C17.3284 18 18 18.6716 18 19.5ZM7.5 22.25C8.74264 22.25 9.75 21.2426 9.75 19.5C9.75 17.7574 8.74264 16.75 7.5 16.75C6.25736 16.75 5.25 17.7574 5.25 19.5C5.25 21.2426 6.25736 22.25 7.5 22.25ZM16.5 22.25C17.7426 22.25 18.75 21.2426 18.75 19.5C18.75 17.7574 17.7426 16.75 16.5 16.75C15.2574 16.75 14.25 17.7574 14.25 19.5C14.25 21.2426 15.2574 22.25 16.5 22.25Z"
                fill="" />
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
