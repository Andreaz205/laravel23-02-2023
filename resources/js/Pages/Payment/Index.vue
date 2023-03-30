<template>
    <div class="card">
        <div class="card-header text-center">
            Оплата
        </div>
        <div class="card-body">
            <div class="row">
                <label class="col-4">
                    Число
                </label>
                <div class="col-8">
                    <input type="text" class="form-control" v-model="amount">
                </div>
            </div>
            <div class="row">
                <label class="col-4">
                    Описание
                </label>
                <div class="col-8">
                    <input type="text" class="form-control" v-model="description">
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" @click="createPayment">
                    Отправить
                </button>
            </div>
        </div>
    </div>

        <div class="card">
            <div class="card-header text-center">
                Транзакции
            </div>
            <div class="card-body" v-if="transactions">
                <div class="row" v-for="transaction in transactions">
                   <div class="col-12">
                       {{transaction.id}} - {{transaction.amount}}  - {{transaction.description}} - {{transaction.status}}
                   </div>
                </div>
            </div>
        </div>


</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {router} from "@inertiajs/vue3";
export default {
    name: "Index",
    components: {AuthenticatedLayout},
    props: ['transactions'],
    data() {
        return {
            amount: null,
            description: null,
        }
    },
    methods: {
        async createPayment() {
            try {
                let data = {
                    amount: this.amount,
                    description: this.description,
                }
                let response = await axios.post(`/payment`, data)
                location.replace(response.data)
            } catch (e) {
                console.log(e)
                alert(e?.message ?? e)
            }
        }
    }

}
</script>

<style scoped>

</style>
