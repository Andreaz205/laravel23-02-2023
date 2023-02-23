<template>
    <AuthenticatedLayout>


        <div class="card">
            <div>
                <div class="d-flex flex-col">

                    <div class="d-flex items-center justify-center text-xl mb-3" v-if="canContacts.edit">Изменить контактную информацию сайта</div>
                    <div class="flex justify-center items-center flex-column">
                        <div class="text-center">Телефон</div>
                        <input type="text" @change="setPhone" :value="contacts && contacts.phone ? contacts.phone : ''" class="w-4/5 border border-gray-400 m-3" autocomplete="off" :disabled="!canContacts.edit">
                    </div>

                    <div class="flex justify-center items-center flex-column">
                        <div class="text-center">Почта</div>
                        <input @change='setEmail' type="email"  :value="contacts && contacts.email ? contacts.email : ''" class="w-4/5 border border-gray-400 m-3" autocomplete="off" :disabled="!canContacts.edit">
                    </div>
                    <div class="flex justify-center gap-x-14 mb-3">
                        <button type='submit' class="btn btn-primary bg-blue-500 " v-if="canContacts.edit" @click="save">
                            Сохранить
                        </button>
                        <button class=" btn btn-danger " v-if="canContacts.delete" @click="clearContacts">
                            Очистить
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Баннер</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form>
                <div class="card-body">
                    <BannerForm :banner-items="bannerItems"/>
                </div>
                <!-- /.card-body -->
                <div class="card-footer"></div>
            </form>
        </div>


    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BannerForm from '@/Pages/Home/BannerForm.vue'
import {Link} from '@inertiajs/vue3'
export default {
    name: "Index",
    components: {AuthenticatedLayout, BannerForm, Link},
    props: ['contacts', 'canContacts', 'bannerItems'],
    data () {
        return {
            phone: null,
            email: null,
        }
    },
    methods: {
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
    mounted() {
        console.log(this.$props.contacts)
    }

}
</script>

<style scoped>

</style>
