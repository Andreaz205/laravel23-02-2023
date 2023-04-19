<template>
    <Spinner v-if="isLoading" />
    <AuthenticatedLayout>
        <div class="card m-3">
            <div class="card-header text-center text-xl">
                Контактная информация сайта
            </div>

            <div class="card-body">
                <div class="container-fluid">

                    <div class="form-group">
                        <div class="row">
                            <label class="col-3">Телефон</label>
                            <input type="text" @change="setPhone" :value="contacts && contacts.phone ? contacts.phone : ''" class="form-control col-9" autocomplete="off" :disabled="!canContacts.edit">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label class="col-3">Почта</label>
                            <input @change='setEmail' type="email"  :value="contacts && contacts.email ? contacts.email : ''" class="form-control col-9" autocomplete="off" :disabled="!canContacts.edit">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">

                <button type='submit' class="btn btn-primary bg-blue-500 " v-if="canContacts.edit" @click="save">
                    Сохранить
                </button>
                <button class="btn btn-danger ml-3" v-if="canContacts.delete" @click="clearContacts">
                    Очистить
                </button>

            </div>

        </div>

        <div class="card m-3">
            <div class="card-header text-center text-xl">
                Баннер
            </div>
            <!-- /.card-header -->
            <!-- form start -->

            <div class="card-body">
                <BannerForm :banner-items="bannerItems" ref="banner"/>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button class="btn btn-primary bg-blue-500" @click="saveOrder">Сохранить</button>
            </div>

        </div>


    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BannerForm from '@/Pages/Home/BannerForm.vue'
import {Link} from '@inertiajs/vue3'
import Spinner from "@/Components/Spinner.vue";
export default {
    name: "Index",
    components: {Spinner, AuthenticatedLayout, BannerForm, Link},
    props: ['contacts', 'canContacts', 'bannerItems'],
    data () {
        return {
            isLoading: false,
            phone: null,
            email: null,
        }
    },
    methods: {
        async saveOrder() {
            try {
                this.isLoading = true
                let items = this.$refs.banner.$props.items
                if (items.length < 1) return
                let orderArray = []
                let itemsLength = items.length
                for (let i = 0; i < itemsLength; i++) {
                    orderArray.push(items[i].id)
                }
                await axios.post('/admin/banner-items/order', {order: orderArray})
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e)
            }
        },
        async save () {
            try {
                let data = {
                    phone: this.phone,
                    email: this.email,
                }
                let response = await axios.patch('/admin/contacts', data)
                console.log(response)
            } catch (e) {
                alert(e)
            }
        },
        async clearContacts (){
            try {
                await axios.delete('/admin/contacts')
                this.phone = null
                this.email = null
            } catch (e) {
                alert(e)
            }
        },
        setPhone (e) {
            this.phone = e.target.value
        },
        setEmail (e) {
            this.email = e.target.value
        }
    },



}
</script>

<style scoped>

</style>
