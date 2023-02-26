<script>
import {Link, router} from '@inertiajs/vue3';

export default {
    name: 'AuthenticatedLayout',
    components: {
        Link
    },
    methods: {
        async logout() {
            try {
                await axios.post('/logout')
                router.visit(
                    '/admin/login', {
                        method: 'GET'
                    }
                )
            } catch (e) {
                alert(e)
            }
        }
    },
    mounted() {
        // let collapsedNavItems = [...document.getElementsByClassName('clps')]
        // collapsedNavItems.map(el => {
        //     el.addEventListener('click', function () {
        //         let childUl = el.querySelector('ul')
        //         if (el.classList.contains('menu-open')) {
        //             el.classList.remove('menu-is-opening', 'menu-open')
        //             childUl.style.display = 'none'
        //             console.log(1111)
        //         }
        //         else {
        //             console.log(2222)
        //             el.classList.add('menu-is-opening', 'menu-open')
        //             childUl.style.display = 'block'
        //         }
        //     })
        // })
    }
}
// const showingNavigationDropdown = ref(false);

</script>

<template>
    <div class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">

            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                    </li>
                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">

                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fas fa-user"></i>


                            <!--                        {{ auth('admin')->user()->name }}-->


                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <div href="#" class="dropdown-item">
                                <!-- Message Start -->
                                <div class="media">
                                    <div class="media-body">
                                        <a href="#">
                                            Личные найтроки
                                        </a>
                                    </div>
                                </div>
                                <!-- Message End -->
                            </div>
                            <div class="dropdown-divider"></div>
                            <div href="#" class="dropdown-item">
                                <!-- Message Start -->
                                <div class="media">

                                    <div class="media-body">
                                        <div class="w-full">
                                            <button type="submit" class="w-full" @click="logout">
                                                Logout
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Message End -->
                            </div>

                        </div>
                    </li>

                    <li>
                        <a href="/home">Home</a>
                    </li>

                </ul>
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="/" class="brand-link text-center">
                    <span class="brand-text font-weight-light">Yorcom</span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">

                    <!-- Sidebar Menu -->
                    <nav class="mt-2">

                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                            <li class="nav-item">
                                <!--                            <a href="/admin/home" class="nav-link">-->
                                <Link href="/admin" class="nav-link">
                                    <i class="nav-icon fas fa-home"></i>
                                    <p>Главная</p>
                                </Link>
                                <!--                            </a>-->
                            </li>

                            <li class="nav-item">
                                <!--                            <a href="/admin/orders" class="nav-link">-->
                                <Link href="/admin/orders" class="nav-link">
                                    <i class="nav-icon fas fa-shopping-bag"></i>
                                    <p>Заказы</p>
                                </Link>


                                <!--                            </a>-->
                            </li>

<!--                            <li :class="['nav-item clps', {'menu-open': route().current()?.includes('product')}]">-->
<!--&lt;!&ndash;                                ['nav-item clps', {'none': document.location !== 'admin/products'}]&ndash;&gt;-->
<!--&lt;!&ndash;                                {{window?.location}}&ndash;&gt;-->
<!--                                <a href="#" class="nav-link">-->
<!--                                    <i class="nav-icon fas fa-couch"></i>-->
<!--                                    <p>-->
<!--                                        Товары-->
<!--                                        <i class="fas fa-angle-left right"></i>-->
<!--                                    </p>-->
<!--                                </a>-->
<!--&lt;!&ndash;                                [{'none': document.location !== 'admin/products'}, {'block': document.location === 'admin/products'}]&ndash;&gt;-->
<!--                                <ul :class="['nav nav-treeview', {'block': route()?.current()?.includes('product')}]">-->
<!--                                    <li class="nav-item">-->
<!--                                        <Link href="/admin/products" class="nav-link">-->
<!--                                            <p>Каталог товаров</p>-->
<!--                                        </Link>-->
<!--                                    </li>-->

<!--&lt;!&ndash;                                    <li class="nav-item">&ndash;&gt;-->
<!--&lt;!&ndash;                                        <Link href="/admin/orders" class="nav-link">&ndash;&gt;-->
<!--&lt;!&ndash;                                            &lt;!&ndash;                            <a href="/admin/users/page" class="nav-link">&ndash;&gt;&ndash;&gt;-->
<!--&lt;!&ndash;                                            <i class="nav-icon fas fa-users"></i>&ndash;&gt;-->
<!--&lt;!&ndash;                                            <p>Клиенты</p>&ndash;&gt;-->
<!--&lt;!&ndash;                                        </Link>&ndash;&gt;-->
<!--&lt;!&ndash;                                    </li>&ndash;&gt;-->

<!--                                </ul>-->
<!--                            </li>-->

                            <li class="nav-item">
                                <Link href="/admin/products" class="nav-link">
                                    <i class="nav-icon fas fa-couch"></i>
                                    <p>Товары</p>
                                </Link>
                                <!--                            <a href="{{ route('product.index') }}" class="nav-link">-->

                                <!--                            </a>-->
                            </li>

                            <li class="nav-item">
                                <Link href="/admin/discounts" class="nav-link">
                                    <i class="nav-icon fas fa-percent"></i>
                                    <p>Скидки</p>
                                </Link>
                                <!--                            <a href="{{ route('product.index') }}" class="nav-link">-->

                                <!--                            </a>-->
                            </li>

                            <li class="nav-item">
                                <Link href="/admin/categories" class="nav-link">
                                    <i class="nav-icon fas fa-th-list"></i>
                                    <p>Категории</p>
                                </Link>
                                <!--                            <a href="/admin/categories" class="nav-link">-->

                                <!--                            </a>-->
                            </li>

                            <li class="nav-item">
                                <Link href="/admin/sales" class="nav-link">
                                    <i class="nav-icon fas fa-dollar-sign"></i>
                                    <p>Акции</p>
                                </Link>
                                <!--                            <a href="/admin/sales" class="nav-link">-->
                                <!--                            </a>-->
                            </li>


                            <li class="nav-item">
                                <Link href="/admin/managers" class="nav-link">
                                    <i class="nav-icon fas fa-user-tie"></i>
                                    <p>Менеджеры</p>
                                </Link>
                                <!--                            <a href="/admin/admins" class="nav-link">-->

                                <!--                            </a>-->
                            </li>

                            <li class="nav-item">
                                <Link href="/admin/users" class="nav-link">
                                <!--                            <a href="/admin/users/page" class="nav-link">-->
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Клиенты</p>
                                </Link>
                                <!--                            </a>-->
                            </li>

                            <li class="nav-item">
                                <Link href="/admin/reviews" class="nav-link">
                                <!--                            <a href="/admin/reviews" class="nav-link">-->
                                    <i class="nav-icon fas fa-comment-alt"></i>
                                    <p>Отзывы</p>
                                </Link>
                                <!--                            </a>-->
                            </li>

                            <li class="nav-item">
                                <Link href="/admin/rooms" class="nav-link">
                                <!--                            <a href="/admin/rooms" class="nav-link">-->
                                    <i class="nav-icon fas fa-vihara"></i>
                                    <p>Комнаты</p>
                                </Link>
                                <!--                            </a>-->
                            </li>

                            <li class="nav-item">
                                <Link href="/admin/abilities" class="nav-link">
                                    <!--                            <a href="/admin/rooms" class="nav-link">-->
                                    <i class="nav-icon fas fa-key"></i>
                                    <p>Роли и доступы</p>
                                </Link>
                                <!--                            </a>-->
                            </li>

                            <li class="nav-item">
                                <Link href="/admin/statistics" class="nav-link">
                                    <!--                            <a href="/admin/rooms" class="nav-link">-->
                                    <i class="nav-icon fas fa-chart-line"></i>
                                    <p>Аналитика</p>
                                </Link>
                                <!--                            </a>-->
                            </li>

                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <div id="app">
                    <slot/>
                </div>

            </div>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
        </div>
        <!-- ./wrapper -->
    </div>
        <!--    <div>-->
        <!--        <div class="min-h-screen bg-gray-100">-->
        <!--            <nav class="bg-white border-b border-gray-100">-->
        <!--                &lt;!&ndash; Primary Navigation Menu &ndash;&gt;-->
        <!--                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">-->
        <!--                    <div class="flex justify-between h-16">-->
        <!--                        <div class="flex">-->
        <!--                            &lt;!&ndash; Logo &ndash;&gt;-->
        <!--                            <div class="shrink-0 flex items-center">-->
        <!--                                <Link :href="route('dashboard')">-->
        <!--                                    <ApplicationLogo-->
        <!--                                        class="block h-9 w-auto fill-current text-gray-800"-->
        <!--                                    />-->
        <!--                                </Link>-->
        <!--                            </div>-->

        <!--                            &lt;!&ndash; Navigation Links &ndash;&gt;-->
        <!--                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">-->
        <!--                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">-->
        <!--                                    Dashboard-->
        <!--                                </NavLink>-->
        <!--                            </div>-->
        <!--                        </div>-->

        <!--                        <div class="hidden sm:flex sm:items-center sm:ml-6">-->
        <!--                            &lt;!&ndash; Settings Dropdown &ndash;&gt;-->
        <!--                            <div class="ml-3 relative">-->
        <!--                                <Dropdown align="right" width="48">-->
        <!--                                    <template #trigger>-->
        <!--                                        <span class="inline-flex rounded-md">-->
        <!--                                            <button-->
        <!--                                                type="button"-->
        <!--                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"-->
        <!--                                            >-->
        <!--                                                {{ $page.props.auth.user.name }}-->

        <!--                                                <svg-->
        <!--                                                    class="ml-2 -mr-0.5 h-4 w-4"-->
        <!--                                                    xmlns="http://www.w3.org/2000/svg"-->
        <!--                                                    viewBox="0 0 20 20"-->
        <!--                                                    fill="currentColor"-->
        <!--                                                >-->
        <!--                                                    <path-->
        <!--                                                        fill-rule="evenodd"-->
        <!--                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"-->
        <!--                                                        clip-rule="evenodd"-->
        <!--                                                    />-->
        <!--                                                </svg>-->
        <!--                                            </button>-->
        <!--                                        </span>-->
        <!--                                    </template>-->

        <!--                                    <template #content>-->
        <!--                                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>-->
        <!--                                        <DropdownLink :href="route('logout')" method="post" as="button">-->
        <!--                                            Log Out-->
        <!--                                        </DropdownLink>-->
        <!--                                    </template>-->
        <!--                                </Dropdown>-->
        <!--                            </div>-->
        <!--                        </div>-->

        <!--                        &lt;!&ndash; Hamburger &ndash;&gt;-->
        <!--                        <div class="-mr-2 flex items-center sm:hidden">-->
        <!--                            <button-->
        <!--                                @click="showingNavigationDropdown = !showingNavigationDropdown"-->
        <!--                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"-->
        <!--                            >-->
        <!--                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">-->
        <!--                                    <path-->
        <!--                                        :class="{-->
        <!--                                            hidden: showingNavigationDropdown,-->
        <!--                                            'inline-flex': !showingNavigationDropdown,-->
        <!--                                        }"-->
        <!--                                        stroke-linecap="round"-->
        <!--                                        stroke-linejoin="round"-->
        <!--                                        stroke-width="2"-->
        <!--                                        d="M4 6h16M4 12h16M4 18h16"-->
        <!--                                    />-->
        <!--                                    <path-->
        <!--                                        :class="{-->
        <!--                                            hidden: !showingNavigationDropdown,-->
        <!--                                            'inline-flex': showingNavigationDropdown,-->
        <!--                                        }"-->
        <!--                                        stroke-linecap="round"-->
        <!--                                        stroke-linejoin="round"-->
        <!--                                        stroke-width="2"-->
        <!--                                        d="M6 18L18 6M6 6l12 12"-->
        <!--                                    />-->
        <!--                                </svg>-->
        <!--                            </button>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->

        <!--                &lt;!&ndash; Responsive Navigation Menu &ndash;&gt;-->
        <!--                <div-->
        <!--                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"-->
        <!--                    class="sm:hidden"-->
        <!--                >-->
        <!--                    <div class="pt-2 pb-3 space-y-1">-->
        <!--                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">-->
        <!--                            Dashboard-->
        <!--                        </ResponsiveNavLink>-->
        <!--                    </div>-->

        <!--                    &lt;!&ndash; Responsive Settings Options &ndash;&gt;-->
        <!--                    <div class="pt-4 pb-1 border-t border-gray-200">-->
        <!--                        <div class="px-4">-->
        <!--                            <div class="font-medium text-base text-gray-800">-->
        <!--                                {{ $page.props.auth.user.name }}-->
        <!--                            </div>-->
        <!--                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>-->
        <!--                        </div>-->

        <!--                        <div class="mt-3 space-y-1">-->
        <!--                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>-->
        <!--                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">-->
        <!--                                Log Out-->
        <!--                            </ResponsiveNavLink>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </nav>-->

        <!--            &lt;!&ndash; Page Heading &ndash;&gt;-->
        <!--            <header class="bg-white shadow" v-if="$slots.header">-->
        <!--                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">-->
        <!--                    <slot name="header" />-->
        <!--                </div>-->
        <!--            </header>-->

        <!--            &lt;!&ndash; Page Content &ndash;&gt;-->
        <!--            <main>-->
        <!--                <slot />-->
        <!--            </main>-->
        <!--        </div>-->
        <!--    </div>-->
</template>



<style scoped lang="scss">
    .content-wrapper{
        min-height: 100vh;
    }
</style>
