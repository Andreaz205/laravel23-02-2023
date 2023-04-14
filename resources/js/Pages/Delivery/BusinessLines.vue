<template>
    <div class="card">
        <div class="card-header text-center text-xl relative">
            Деловые линии
            <button class="btn btn-default absolute top-1/2 -translate-y-1/2 left-1">
                <Link href="/admin/delivery">
                    Ко всем
                </Link>
            </button>
        </div>
    </div>
</template>

<script>
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
export default {
    name: "DeliveryLines",
    components: {Link},
    layout: AuthenticatedLayout,

}
</script>

<style scoped>

</style>
