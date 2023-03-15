<template>
    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header text-xl text-center">
                Настройки офрмления заказов
            </div>

            <div class="card-body">
                <div class="col-12">
                    Настройка полей заказа
                </div>
                <div class="col-12">
                    <div class="container-fluid table-responsive">
                        <table class="table table-hover text-nowrap border-x-2 border-b-2">
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Тип</th>
                                <th>Обязательное</th>
                                <th>Категории</th>
                                <th>Группы клиентов</th>
                                <th>Величина скидки</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody v-if="discounts?.accumulative_discounts?.discounts && discounts.accumulative_discounts.discounts.length">
                            <tr v-for="discount in discounts.accumulative_discounts.discounts">
                                <td>{{discount.threshold}} p</td>
                                <td>{{discount.allow_discounted ? 'Да' : 'Нет'}}</td>
                                <td>{{ discount.allow_kits ? 'Да' : 'Нет'}}</td>
                                <td>
                                    <template v-if="discount.categories && discount.categories.length">
                                        <span v-for="category in discount.categories">{{category.name}},</span>
                                    </template>

                                    <template v-else>
                                        Все
                                    </template>
                                </td>
                                <td>
                                    <template v-if="discount.available_groups === 'all'">
                                        Все
                                    </template>
                                    <template v-if="discount.available_groups === 'without_groups'">
                                        Вне группы
                                    </template>
                                    <template v-if="
                                                                discount.available_groups === 'selected' &&
                                                                discount.groups &&
                                                                discount.groups.length"
                                    >
                                        <span v-for="group in discount.groups">{{group.title}}</span><span> </span>
                                    </template>
                                </td>
                                <td>
                                    {{discount.value}} %
                                </td>
                                <td>
                                    <button class="btn btn-danger" @click="deleteDiscount(discount)">
                                        Удалить
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-primary">
                    Добавить
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
export default {
    name: "Index",
    components: {AuthenticatedLayout},
    props: ['fields'],
    data () {
        return {

        }
    },
    methods: {
         async addField() {

         }
    }
}
</script>

<style scoped>

</style>
