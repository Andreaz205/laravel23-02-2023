<template>

    <div class="card">
        <div class="card-header text-center text-lg">
            Настройки доставки
        </div>
    </div>

    <div class="row mt-3">

        <div class="col-6">
            <div class="card m-3">
                <Link href="/admin/delivery/yandex" class="block">
                    <div class="card-body">
                        Yandex
                    </div>
                </Link>
            </div>
        </div>

        <div class="col-6">
            <div class="card m-3">
                <Link href="/admin/delivery/business-lines" class="block">
                    <div class="card-body">
                        Деловые линии
                    </div>
                </Link>
            </div>
        </div>

    </div>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from '@inertiajs/vue3'
export default {
    name: "Index",
    components: {AuthenticatedLayout, Link},

    layout: AuthenticatedLayout,
}
</script>

<style scoped>

</style>
