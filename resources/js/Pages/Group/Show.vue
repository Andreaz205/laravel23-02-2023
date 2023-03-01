<template>
<!--    <AuthenticatedLayout>-->
        <div class="card card-white">
            <div class="card-header">
                <div class="flex ">
                    <Link href="/admin/groups">
                        <button class="btn btn-default mr-3">
                            Назад
                        </button>
                    </Link>
                    <span class="bg-gray-300 p-1.5 rounded text-black">Группа {{group.title}}</span>
                </div>


            </div>
            <form class="card-body" @submit="onSubmit">

                <div class="row">
                    <div class="col-12">
                        <label>Название группы</label>
                        <input type="text" class="form-control" v-model="title">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Величина скидки</label>
                            <input type="text" v-model="discount" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <label>Группа по умолчанию</label>
                    </div>
                    <div class="col-8">
                        <input type="checkbox" class="form-control" v-model="is_default">
                    </div>
                    <small class="col-12 text-gray-500">
                        Все пользователи при создании будут попадать в эту группу
                    </small>
                </div>

                <div class="row">
                    <div class="col-12">
                        <label>Описание скидки</label>
                        <textarea class="form-control" v-model="discount_description"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <label>Не использовать для уценённых товаров</label>
                    </div>
                    <div class="col-8">
                        <input type="checkbox" v-model="allow_discounted">
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <label>Не использовать для комплектов товаров</label>
                    </div>
                    <div class="col-8">
                        <input type="checkbox" v-model="allow_kits">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Категории товаров</label>
                            <select class="form-control" @change="handleChangeCategorySelect" >
                                <option value="all">Все</option>
                                <option v-if="categoriesData && categoriesData.length" value="select" :selected="groupData.data.categories && groupData.data.categories.length">Выбрать категории</option>
<!--                                    <option v-for="category in categoriesData" :value="category.id">{{category.name}}</option>-->
                            </select>
                        </div>
                    </div>
                </div>


                <template v-if="isCategoriesFormOpen">
                    <div class="row mt-4" v-if="categoriesData && categoriesData.length">
                        <div class="col-12">
                            <table class="table table-hover border-x-2 border-b-2">
                                <tbody>
                                <template v-for="category in categoriesData" :key="category.id">
                                    <CategoryRow
                                        v-if="!category.parent_category_id"
                                        @changeCheckboxValue="handleChangeCategoryCheckboxValue"
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


                <!-- /.form group -->
                <button class="btn btn-primary bg-blue-500" type="submit">
                    Сохранить
                </button>
            </form>
        </div>
<!--    </AuthenticatedLayout>-->
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CategoryRow from '@/Pages/Category/CategoryRow.vue'
import {Link, router} from "@inertiajs/vue3";
export default {
    name: "Show",
    components: {AuthenticatedLayout, Link, CategoryRow},
    props: ['groupData', 'categoriesData'],
    layout: AuthenticatedLayout,
    data () {
        return {

            isCategoriesFormOpen: !!(this.groupData.data.categories && this.groupData.data.categories.length),
            categories: this.groupData?.data.categories && this.groupData?.data.categories.length
                ? this.groupData?.data.categories.map(category => category.id)
                : 'all',
            is_default: this.groupData?.data.is_default,
            title: this.groupData?.data.title,
            allow_discounted: this.groupData?.data.allow_discounted,
            allow_kits: this.groupData?.data.allow_discounted,
            discount_description: this.groupData?.data.discount_description,
            discount: this.groupData?.data.discount,


            group: this.groupData?.data,
        }
    },
    methods: {
        handleChangeCategoryCheckboxValue(category, isChecked) {
            if (isChecked) {
                if (this.categories === 'all') return this.categories = [category.id]
                return this.categories.push(category.id)
            } else {
                if (this.categories !== 'all') return this.categories = this.categories.filter(categoryId => categoryId !== category.id)
            }
        },
        handleChangeCategorySelect(event) {
            let value = event.target.options[event.target.options.selectedIndex].value
            if (value === 'select') return this.isCategoriesFormOpen = true
            this.isCategoriesFormOpen = false
        },
        async onSubmit(evt) {
            evt.preventDefault()
            try {
                let data = {
                    discount: this.discount,
                    categories: this.categories === 'all' ? [] : this.categories,
                    title: this.title,
                    allow_discounted: this.allow_discounted,
                    allow_kits: this.allow_kits,
                    discount_description: this.discount_description,
                    is_default: this.is_default
                }
                await axios.patch(`/admin/groups/${this.group.id}`, data)
                router.visit('/admin/groups')

            } catch (e) {
                alert(e)
            }
        },
    },
    mounted() {
        console.log(this.categoriesData)
    }
}
</script>

<style scoped>

</style>
