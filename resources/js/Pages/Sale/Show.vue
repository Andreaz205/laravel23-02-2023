<template>
    <AuthenticatedLayout>
        <div v-if="sale">
            <div class="text-center mb-3">{{sale.title}}</div>
            <template v-if="sale.image_url">
                <div  class="relative sale-image">
                    <img :src="sale.image_url" alt="" class="absolute top-0 left-0 w-full">
                    <div class="absolute right-0 top-0 cursor-pointer" @click="deleteImage">
                        <i class="far fa-window-close"></i>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="custom-switch-main" :checked="sale.is_public" @change="setIsPublic">
                        <label class="custom-control-label" for="custom-switch-main" style="cursor: pointer;">Опубликовать</label>
                    </div>
                </div>
            </template>

            <div v-else>
                <div>
                    <input type="file" @change="setImage($event)">
                </div>
                <button @click="save" class="btn btn-primary">
                    Сохранить
                </button>
            </div>
            <div>
                <button @click="setIsVariantsOpen(!isVariantsOpen)">Добавить товары</button>
                <div v-if="isVariantsOpen">
                    Товары
                    <div v-if="products" class="border">
                        <div v-for="product in products" class="d-flex">
                            <div>
                                {{product.title}}
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" :id="'custom-switch' + product.id" :checked="product.is_exists" @change="addProduct(product)">
                                    <label class="custom-control-label" :for="'custom-switch' + product.id" style="cursor: pointer;">Добавить в акцию</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    name: "Sale",
    components: {AuthenticatedLayout},
    props: ['data'],
    data () {
        return {
            isVariantsOpen: false,
            sale: this.$props.data,
            image: null,
            products: null
        }
    },
    methods: {
        async addProduct(product) {
            let {data} = await axios.get(`/admin/sales/${this.sale.id}/toggle-exists-product/${product.id}`)
            this.products.map(prod => {
                if (prod.id === product.id) prod.is_exists = !prod.is_exist
            })
        },
        async setIsVariantsOpen(isOpen) {
            this.isVariantsOpen = isOpen
            if (!this.products) {
                await this.fetchProducts()
            }
        },
        async fetchProducts() {
            let response = await axios.get(`/admin/sales/${this.sale.id}/sale-products`)
            this.products = response.data
        },
        async setImage(e) {
            let formData = new FormData

            let image = e.target.files[0]
            formData.append('image', image)
            this.image = formData

        },
        async save() {
            try {
                let {data} = await axios.post(`/admin/sales/${this.sale.id}/image`, this.image)
                alert('Успешно сохранено')
                this.sale.image_url = data.image_url
            } catch (e) {
                alert(e)
            }
        },
        async deleteImage(){
            try {
                let {data} = await axios.delete(`/admin/sales/${this.sale.id}/image`)
                alert(data)
                this.sale.image_url = null
            } catch (e) {
                alert(e)
            }
        },
        async setIsPublic() {
            try {
                let {data} = await axios.post(`/admin/sales/${this.sale.id}/public`)
                console.log(data)

            } catch (e) {
                alert(e)
            }
        }
    },
    computed: {},
}
</script>

<style scoped lang="scss">
.sale-image{
    width: 200px;
    height: 200px;
}
</style>
