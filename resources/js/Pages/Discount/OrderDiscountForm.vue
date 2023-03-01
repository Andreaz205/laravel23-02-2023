<template>
    <div class="modal fade" id="createOrderDiscountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить свойства</h5>
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

                        <div class="row">
                            <div class="col-3">
                                <label>Описание скидки</label>
                            </div>
                            <div class="col-9">
                                <textarea class="form-control" v-model="description"></textarea>
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
                            <div class="row mt-4" v-if="categoriesData && categoriesData.length">
                                <div class="col-12">
                                    <table class="table table-hover border-x-2 border-b-2">
                                        <tbody>
                                        <template v-for="category in categoriesData" :key="category.id">
                                            <CategoryRow
                                                v-if="!category.parent_category_id"
                                                @changeCheckboxValue="handleChangeCheckboxValue"
                                                :category="category"
                                                :key="category.id"
                                                :delete-button="false"
                                                :has-checkbox="true"
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
                                <select class="form-control" @change="selectGroup">
                                    <option value="all">Все</option>
                                    <option value="without_groups">Вне группы</option>
                                    <option value="select">Выбрать группы</option>
                                </select>
                            </div>
                        </div>

                        <template v-if="isSelectGroupOpen">
                            <div class="row mt-4" v-if="groupsData && groupsData.length">
                                <div class="col-12">
                                    <template v-for="group in groupsData" :key="group.id">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                {{group.title}}
                                            </div>
                                            <div>
                                                <input type="checkbox" class="form-control" v-model="group.is_selected">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

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
import CategoryRow from "@/Pages/Category/CategoryRow.vue";

export default {
    name: "OrderDiscountForm",
    components: {CategoryRow},
    emits: ['discountCreated'],
    props: ['categoriesData', 'groupsData'],
    data () {
        return {
            // formCategories: this.categoriesData,
            // formGroups: this.groupsData,
            description: null,
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
            if (value == 'select') {
                this.categories = []
                return this.toggleOpenCategories()
            }
            this.isSelectCategoryOpen = false
            if (value == 'all') this.categories = []
        },
        selectGroup(event) {
            let value = event.target.options[event.target.selectedIndex].value
            if (value == 'select') {
                this.groups = []
                return this.toggleOpenGroups()
            }
            this.isSelectGroupOpen = false
            if (value == 'all') this.groups = null
            if (value == 'without_groups') this.groups = 'without_groups'
        },
        toggleOpenCategories() {
            this.isSelectCategoryOpen = !this.isSelectCategoryOpen
        },
        toggleOpenGroups() {
            this.isSelectGroupOpen = !this.isSelectGroupOpen
        },
        handleChangeCheckboxValue(category, isChecked) {
            if (isChecked) {
                if (this.categories === 'all') return this.categories = [category.id]
                return this.categories.push(category.id)
            } else {
                if (this.categories !== 'all') return this.categories = this.categories.filter(categoryId => categoryId !== category.id)
            }
        },
        async submit() {
            try {
                let groupsData = []
                if (this.groupsData && this.groupsData) {
                    this.groupsData.map(gr => {
                        if (gr.is_selected) {
                            groupsData.push(gr.id)
                        }
                    })
                }
                let data = {
                    description: this.description,
                    threshold: this.threshold,
                    value: this.value,
                    allow_discounted: this.allow_discounted,
                    allow_kits: this.allow_kits,
                    categories: this.categories === 'all' ? [] : this.categories,
                    groups: this.groups === 'all' ? [] : this.groups === 'without_groups' ? 'without_groups' : groupsData,
                }
                let response = await axios.post('/admin/discounts/order', data)
                let discount = response.data.data
                this.$emit('discountCreated', discount)
            } catch (e) {
                alert(e?.response?.data?.message ?? e?.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
