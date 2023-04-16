<template>
    <div class="card m-4">
        <div class="card-header">
            <div class="row">
                <div class="col-12 text-center mt-2 mb-2">
                    <span class="text-xl">Бонусы</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="text-center text-lg">
                        Бонусы за заказы клиентов
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-3">
                    Включены:
                </div>
                <div class="col-9">
                    <input type="checkbox" class="form-control" v-model="is_active">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-3">
                    Бонусный процент
                </div>
                <div class="col-9">
                    <input type="text" class="form-control" v-model="bonus_percent">
                    <small>Процент от суммы заказа переходящий в бонусные баллы</small>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-3">
                    Максимальная скидка
                </div>
                <div class="col-9">
                    <input type="text" class="form-control" v-model="max_discount_percents">
                    <small>Максимальная скидка, процент от суммы заказа, который клиент может оплатить бонусными
                        баллами</small>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-3">
                    Коэффициент
                </div>
                <div class="col-9">
                    <input type="text" class="form-control" v-model="coefficient_conversion">
                    <small>
                        Коэффициент конвертации денег в баллы и наоборот. Например, если коэффициент = 2, то 1р. = 2 балла
                    </small>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-3">
                    Бонусные баллы за регистрацию
                </div>
                <div class="col-9">
                    <input type="text" class="form-control" v-model="register_bonuses">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-3">
                    Не использовать для уценённых товаров:
                </div>
                <div class="col-9">
                    <input type="checkbox" class="form-control" v-model="allow_discounted">
                </div>
            </div>

            <div class="row">
                <div class="col-3">
                    Не использовать для комплектов тваров:
                </div>
                <div class="col-9">
                    <input type="checkbox" class="form-control" v-model="allow_kits">
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-3">
                    <div>
                        Категории:
                    </div>
                </div>
                <div class="col-9">
                    <select class="form-control" v-model="categorySelect">
                        <option value="all">Все</option>
                        <option value="select" v-if="categories && categories.length">Выбрать категории</option>
                    </select>
                </div>
            </div>

            <template v-if="categorySelect === 'select'">
                <div class="row mt-4" v-if="categories && categories.length">
                    <div class="col-12">
                        <table class="table table-hover border-x-2 border-b-2">
                            <tbody>
                            <template v-for="category in categories" :key="category.id">
                                <CategoryRow
                                    v-if="!category.parent_category_id"
                                    @changeCheckboxValue="handleChangeCategoryCheckboxValue"
                                    :category="category"
                                    :key="category.id"
                                    :delete-button="false"
                                    :has-checkbox="true"
                                    :checked-ids="this.bonus.categories && this.bonus.categories.length ? this.bonus.categories.map(c => c.id) : []"
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
                    <select class="form-control" v-model="groupSelect">
                        <option value="all">Все</option>
                        <option value="without_groups">Вне группы</option>
                        <option value="selected" v-if="groups && groups.length">Выбрать группы</option>
                    </select>
                </div>
            </div>

            <template v-if="groupSelect === 'selected'">
                <div class="row mt-4" v-if="groups && groups.length">
                    <div class="col-12">
                        <template v-for="group in groups" :key="group.id">
                            <div class="flex justify-between items-center">
                                <div>
                                    {{group.title}}
                                </div>
                                <div>
                                    <input type="checkbox" class="form-control" v-model="groupsForm" :value="group.id">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div class="row">
                <div class="col-12">
                    <button
                        @click="submit"
                        class="btn btn-primary"
                    >
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CategoryRow from '@/Pages/Category/CategoryRow.vue'
export default {
    name: "BonusesForm",
    props: ['bonus', 'categories', 'groups'],
    components: {CategoryRow},
    data() {
        return {
            errors: null,
            categoriesForm: this.bonus.categories && this.bonus.categories.length ? this.bonus.categories.map(category => category.id) : [],
            groupsForm: this.bonus.groups && this.bonus.groups.length ? this.bonus.groups.map(category => category.id) : [],
            categorySelect: this.bonus.categories && this.bonus.categories.length ? 'select' : 'all',
            groupSelect: this.bonus.available_groups ?? 'all',
            is_active: this.bonus.is_active,
            bonus_percent: this.bonus.bonus_percent,
            max_discount_percents: this.bonus.max_discount_percents,
            coefficient_conversion: this.bonus.coefficient_conversion,
            register_bonuses: this.bonus.register_bonuses,
            allow_discounted: this.bonus.allow_discounted,
            allow_kits: this.bonus.allow_kits,
        }
    },
    methods: {
        handleChangeCategoryCheckboxValue(category, isChecked) {
            if (isChecked) {
                return this.categoriesForm.push(category.id)
            } else {
                this.categoriesForm = this.categoriesForm.filter(categoryId => categoryId !== category.id)
            }
        },
        async submit() {
            try {
                if (this.groupSelect === 'select') {

                }
                let data = {
                    categories: this.categoriesForm,
                    groups: this.groupsForm && this.groupsForm.length ? this.groupsForm : this.groupSelect !== 'select' ? this.groupSelect : 'all',
                    is_active: this.is_active,
                    bonus_percent: this.bonus_percent,
                    max_discount_percents: this.max_discount_percents,
                    coefficient_conversion: this.coefficient_conversion,
                    register_bonuses: this.register_bonuses,
                    allow_discounted: this.allow_discounted,
                    allow_kits: this.allow_kits,
                }
                let response = await axios.patch('/admin/bonuses', data)
            } catch (e) {
                alert(e?.response?.data?.message ?? e?.message ?? e)
            }
        }
    },
    watch: {
        categorySelect(currentValue, prevValue) {
            if (currentValue !== 'select') this.categoriesForm = []
        },
        groupSelect(currentValue, prevValue) {
            if (currentValue !== 'select') this.groupsForm = []
        }
    },
    mounted () {

    }
}
</script>

<style scoped>

</style>
