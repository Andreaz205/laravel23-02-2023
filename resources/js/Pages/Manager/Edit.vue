<template>
    <AuthenticatedLayout>
        <a href="/admin/managers">Назад</a>
        <div class="d-flex justify-start m-5">
            <div class="mr-5">
                Имя
            </div>
            <div>
                <input type="text" v-model="name" class="border border-black">
            </div>
        </div>
        <div class="d-flex justify-center items-center text-3xl">Права пользователя</div>
        <div>
            <div>
                <div class='text-lg d-flex justify-center items-center'>Доступ</div>

                <div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked="">
                        <label class="form-check-label text-bold">Доступ в раздел Заказы</label>
                    </div>
                </div>

                <div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked="">
                        <label class="form-check-label text-bold">Доступ в раздел Кленты</label>
                    </div>
                </div>

                <div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked="">
                        <label class="form-check-label text-bold">Доступ в раздел Товары</label>
                    </div>
                </div>
            </div>
            <div>
                <div class="d-flex justify-center items-center text-3xl">Заказы</div>
                <div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Редактировать информацию по заказу(поля, статус, способ опалты, доставки...)</label>
                        </div>
                    </div>

                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Изменять содержимое заказа</label>
                        </div>
                    </div>

                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Удалять заказы</label>
                        </div>
                    </div>

                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Изменять цены, применять скидки и купоны</label>
                        </div>
                    </div>

                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Экспорт заказов</label>
                        </div>
                    </div>

                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Редактировать чужие заказы</label>
                        </div>
                    </div>

                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Редактировать неназначенные заказы</label>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="d-flex justify-center items-center text-3xl">Клиенты</div>
                <div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Редактировать информацию клиентов</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Экспорт клиентов</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Назначать и удалять бонусные баллы</label>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="d-flex justify-center items-center text-3xl">Товары</div>
                <div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Редактировать цены и варианты товаров</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Просматривать закупочные цены</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Просматривать дополнительные цены</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Редактировать информацию о товаре</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Редактировать отзывы</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Редактировать категории</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Импорт товаров</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Экспорт товаров</label>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked="">
                            <label class="form-check-label text-bold">Удалять товары и категории</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
export default {
    name: "Edit",
    components: {
        AuthenticatedLayout
    },
    data () {
        return {
            name: this.$props.manager.name,
            managerData: this.$props.manager,
        }
    },
    props: ['manager'],

}
</script>

<style scoped>

</style>
