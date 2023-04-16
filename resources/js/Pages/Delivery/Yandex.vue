<template>
    <Spinner v-if="isLoading"/>
    <div class="card">
        <div class="card-header text-center text-xl relative">
            Yandex Доставка в другой день
            <button class="btn btn-default absolute top-1/2 -translate-y-1/2 left-1">
                <Link href="/admin/delivery">
                    Ко всем
                </Link>
            </button>
        </div>
    </div>
    <FlashMessage />

    <div class="card m-3">
        <div class="card-header text-center">
            Пункт приёма (терминал)
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    Тип оплаты для клиента:
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    <input type="radio" v-model="payment_method" id="cashless" value="cashless">
                    <label for="cashless">Безналичный</label>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    <input type="radio" v-model="payment_method" id="card_on_receipt" value="card_on_receipt">
                    <label for="card_on_receipt">Картой при получении</label>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    <input type="radio" v-model="payment_method" id="cash_on_receipt" value="cash_on_receipt">
                    <label for="cash_on_receipt">Наличными при получени</label>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    <input type="radio" v-model="payment_method" id="already_paid" value="already_paid">
                    <label for="already_paid">Уже оплачено</label>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-12">
                    Терминал в городе отправления:
                </div>
            </div>

            <div class="row">
                <div class="col-10">
                    <input type="text" class="form-control" disabled :value="data.platform_address">
                </div>
                <div class="col-2">
                    <button class="btn btn-warning">
                        Изменить
                    </button>
                </div>

            </div>

            <div class="row mt-2">
                <div class="col-12 text-center">
                   <button class="btn btn-primary" @click="saveChanges">
                       Сохранить
                   </button>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Errors from "@/Components/Errors/Errors.vue";
import Spinner from "@/Components/Spinner.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
export default {
    name: "Yandex",
    components: {FlashMessage, Spinner, Errors, Link},
    layout: AuthenticatedLayout,
    props: [
        'data'
    ],
    data () {
        return {
            isLoading: false,
            errors: null,
            payment_method: this.data?.payment_method ?? null,
            platform_id: this.data?.platform_id ?? null,
            platform_address: this.data?.platform_address ?? null,
        }
    },
    methods: {
        async saveChanges() {
            try {
                this.isLoading = true
                let data = {
                    payment_method: this.payment_method,
                    platform_id: "3ed47320-6e04-40eb-85ba-ed702cd0d7d2",
                    platform_address: 'Монтажников 16'
                }
                await this.$inertia.patch('/admin/delivery/yandex', data)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e?.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
