<template>
    <div class="card">
        <div class="card-header text-center text-lg relative">
            Настройки доставки Yandex для товара {{product?.title}}
            <button class="btn btn-default absolute top-1/2 left-1 -translate-y-1/2">
                <Link :href="`/admin/products/${product.id}`">
                    К {{product?.title}}
                </Link>
            </button>
        </div>

    </div>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from '@inertiajs/vue3'
export default {
    name: "Index",
    layout: AuthenticatedLayout,
    components: {Link},
    props: ['product']
}
</script>

<style scoped>

</style>
