<template>
    <div class="card">
        <div class="card-header">
            Доставка
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item cursor-pointer">
                    <Link :href="`/admin/products/${productId}/delivery-settings/yandex`">
                        <img src="/service_images/yandex_delivery_logo.webp" alt="Яндекс Доставка"/>
                    </Link>
                </li>
                <li class="list-group-item cursor-pointer">
                    <Link :href="`/admin/products/${productId}/delivery-settings/business-lines`">
                        <img src="/service_images/business_lines_logo.png" alt="Деловые Линии"/>
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import {Link} from '@inertiajs/vue3'
export default {
    name: "DeliveryCard",
    components: {Link},
    props: ['productId']
}
</script>

<style scoped>

</style>
