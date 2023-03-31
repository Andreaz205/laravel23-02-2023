<template>
    <Spinner v-if="isLoading"/>
    <AuthenticatedLayout>
        <Errors :errors="errors" />
        <div class="card">
            <div class="card-header text-center text-xl relative">
                На главной странице варианты {{kit.title}}
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
                                        <template v-if="product?.kit_variant?.id === variant.id">
                                            <button class="btn btn-default">
                                                Указан
                                            </button>
                                        </template>
                                        <button v-else class="btn btn-primary" @click="bindVariant(variant)">
                                            Указать
                                        </button>
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
import Spinner from "@/Components/Spinner.vue";
import Errors from "@/Components/Errors/Errors.vue";
export default {
    name: "Edit",
    components: {Errors, Spinner, AuthenticatedLayout, Link},
    props: ['products', 'kit'],
    data () {
        return {
            errors: null,
            isLoading: false,
        }
    },
    methods: {
        async bindVariant(variant) {
            try {
                this.isLoading = true
                await axios.get(`/admin/kits/${this.kit.id}/products/bind-variants/${variant.id}`)
                let searchedProduct = this.products.find(product => product.id === variant.product_id)
                searchedProduct.kit_variant = variant
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
         }
    }
}
</script>

<style scoped>

</style>
