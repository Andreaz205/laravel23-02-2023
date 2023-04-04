<template>
    <Spinner v-if="isLoading"/>
    <AuthenticatedLayout>


        <div class="card">
            <div class="card-header text-center text-lg ">
                Скидки на главной странице
            </div>
        </div>
        <FlashMessage />

        <div class="row" v-if="sales && sales.length">
            <div class="col-6" v-for="formItem in form" :key="formItem.id">
                <div class="card m-2 min-h-[300px]">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <div class="h-[300px] w-[300px] border-black border-2 relative" >
                                    <img :src="formItem.image_url" alt="" class="w-full h-full object-cover">
                                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 btn btn-default">
                                        <label :for="`image-input-${formItem.id}`" class="w-full h-full flex justify-center items-center flex-column mb-0 cursor-pointer">Изменить</label>
                                        <input type="file" hidden :id="`image-input-${formItem.id}`" @change="loadImage($event, formItem)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-12">
                                <div class="flex justify-center flex-column items-center">
                                    <div>
                                        <div>
                                            Описание
                                        </div>
                                        <textarea class="form-control" type="text" v-model="formItem.description" />
                                    </div>
                                    <div>
                                        <div>
                                            Значение
                                        </div>
                                        <input class="form-control" type="text" v-model="formItem.value">
                                    </div>

                                    <div>
                                        <div>
                                            Ссылка
                                        </div>
                                        <input class="form-control" type="text" v-model="formItem.link">
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center">
                <button class="btn btn-primary" @click="saveCards">
                    Сохранить
                </button>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Spinner from "@/Components/Spinner.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
export default {
    name: "Sale",
    props: [
        'sales'
    ],
    components: {FlashMessage, Spinner, AuthenticatedLayout},
    data () {
        return {
            isLoading: false,
            form: this.sales.map(sale => ({
                id: sale.id,
                image_url: sale.image_url,
                description: sale.description,
                value: sale.value,
                link: sale.link,
            }))
        }
    },
    methods: {
        async loadImage(event, sale) {
            try {
                this.isLoading = true
                let file = event.target.files[0]

                let formData = new FormData
                formData.append('image', file)
                let response = await axios.post(`/admin/main-page-sales/${sale.id}/image`, formData)
                sale.image_url = response.data.image_url
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.message ?? e)
            }
        },
        async saveCards() {
            try {
                this.isLoading = true
                let data = this.form.map(formItem => ({
                    id: formItem.id,
                    link: formItem.link,
                    description: formItem.description,
                }))

                await this.$inertia.post(`/admin/main-page-sales/update`, {sales: data})

                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
