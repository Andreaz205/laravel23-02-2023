<template>
    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header text-center text-xl relative">
                Редактировать набор {{kit.title}}
            </div>
            <div class="absolute -translate-y-1/2 top-1/2 left-1">
                <Link href="/admin/kits">
                    <button class="btn btn-default">
                        Назад
                    </button>
                </Link>
            </div>
        </div>
        <template v-if="products && products.length">
            <div class="row" v-for="product in products">
                <div class="col-12">
                    <div class="card mx-4 my-2">
                        <div class="card-header">
                            {{product.title}}
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush" v-if="product.variants && product.variants.length">
                                <li class="list-group-item cursor-pointer" v-for="variant in product.variants">
                                    <div class="flex justify-between items-center">
                                        <div class="flex justify-start items-center">
                                            <img :src="variant.images[0]?.image_url ?? '/storage/images/no-image.jpg'" class="w-[100px] h-[100px]" alt="" />
                                            <span class="ml-2">{{variant.title}}</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </AuthenticatedLayout>
</template>

<script>
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
export default {
    name: "Edit",
    components: {AuthenticatedLayout, Link},
    props: ['products', 'kit'],
}
</script>

<style scoped>

</style>
