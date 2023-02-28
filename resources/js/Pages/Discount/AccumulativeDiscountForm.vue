<template>
    <div class="modal fade" id="createAccumulativeDiscountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить накопительную скидку</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-3">
                                <div>
                                    Стоимость всех заказов:
                                </div>
                            </div>
                            <div class="col-9">
                                <input class="form-control" v-model="threshold">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-3">
                                <div>
                                    Величина скидки:
                                </div>
                            </div>
                            <div class="col-9">
                                <input class="form-control" v-model="value">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <div>
                                    Не использовать для уценненых тваров:
                                </div>
                            </div>
                            <div class="col-9">
                                <input class="form-control" type="checkbox" v-model="allow_discounted">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <div>
                                    Не использовать для комплектов тваров:
                                </div>
                            </div>
                            <div class="col-9">
                                <input class="form-control" type="checkbox" v-model="allow_kits">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-3">
                                <div>
                                    Категории:
                                </div>
                            </div>
                            <div class="col-9">
                                <select class="form-control" @change="selectCategory">
                                    <option value="all">Все</option>
                                    <option value="select" v-if="categoriesData && categoriesData.length">Выбрать категории</option>
                                </select>
                            </div>
                        </div>

                        <template v-if="isSelectCategoryOpen">
                            <div class="row" v-if="categoriesData && categoriesData.length">
                                <div class="col-12">
                                    <table class="table table-hover">
                                        <tbody>
                                            <template v-for="(category) in categoriesData" :key="category.id">
                                                <CategoryRow
                                                    v-if="!category.parent_category_id"
                                                    :category="category"
                                                    :key="category.id"
                                                    :delete-button="false"
                                                />
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <div class="row mt-2">
                            <div class="col-3">
                                <div>
                                    Группы:
                                </div>
                            </div>
                            <div class="col-9">
                                <select class="form-control">
                                    <option value="all">Все</option>
                                    <option value="without_group">Вне группы</option>
                                    <option value="select">Выбрать группы</option>
                                </select>


                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="submit">
                        Создать скидку
                    </button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import CategoryRow from '@/Pages/Category/CategoryRow.vue'
export default {
    name: "AccumulativeDiscountForm",
    components: {CategoryRow},

    props: ['categoriesData', 'groupsData'],
    data () {
        return {
            isSelectGroupOpen: false,
            isSelectCategoryOpen: false,
            threshold: null,
            value: null,
            allow_discounted: false,
            allow_kits: false,
            categories: 'all',
            groups: 'all',
        }
    },
    methods: {
        selectCategory(event) {
            let value = event.target.options[event.target.selectedIndex].value
            if (value == 'select') return this.toggleOpenCategories()
            this.isSelectCategoryOpen = false
            if (value == 'all') this.categories = null
        },
        toggleOpenCategories() {
            this.isSelectCategoryOpen = !this.isSelectCategoryOpen
        },
        async submit() {
            try {
                let data = {
                    threshold: this.threshold,
                    value: this.value,
                    allow_discounted: this.allow_discounted,
                    allow_kits: this.allow_kits,
                    categories: [],
                    groups: [],
                }
                let response = await axios.post('/admin/discounts/accumulative', data)
                console.log(response)
            } catch (e) {
                console.log(e)
                alert(e?.response?.data?.message ?? e?.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
