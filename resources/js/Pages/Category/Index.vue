<template>
    <Spinner v-if="isLoading" />
    <!--    TODO: Модальное окно для добавления категорий-->
    <div class="modal fade" id="addCategoryButton" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Добавить категорию</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <Errors :errors="errors"/>

<!--                    <div class="container-fluid" v-if="product.option_names">-->
<!--                        <div class="row" v-for="name in product.option_names">-->
<!--                            <div class="col-sm-6">-->
<!--                                <span>{{ name.title }}</span>-->
<!--                            </div>-->
<!--                            <div class="col-sm-6">-->
<!--                                <button class="btn btn-danger" @click="deleteOption(name.id)">Удалить</button>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

                    <div class="form-group">
                        <label for="addCategoryInput">Название категории</label>
                        <input type="text" class="form-control" id="addCategoryInput" v-model="newCategory" placeholder="Введите название для категории">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button class="btn btn-primary" @click="addCategory">
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--    TODO: Модальное окно для редактирования категории-->
    <div class="modal fade" id="editCategoryButton" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Фотографии варианта</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button type="submit" class="btn btn-primary bg-blue">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <AuthenticatedLayout>

                <div class="card">
                    <div class="card-header text-center text-xl">
                        Управление категориями
                    </div>
                    <!-- ./card-header -->
                    <div class="card-body p-0">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <td class="border-0 text-center text-lg relative">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2">
                                        <button
                                            type="button" class="mr-2"
                                            data-toggle="modal" data-target="#addCategoryButton"
                                        >
                                            <i class="fas fa-plus" ></i>
                                        </button>

                                        <button type="button"
                                                data-toggle="modal" data-target="#editCategoryButton">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>

                                    Каталог
                                </td>
                            </tr>
                            <tr class="expandable-body">
                                <td>
                                    <div class="p-0">
                                        <table class="table table-hover">
                                            <tbody>
                                                <template v-if="categories && categories.length">
                                                    <template v-for="category in categories" :key="category.id">
                                                        <CategoryRow
                                                            v-if="!category.parent_category_id"
                                                            :change-selected-category="changeSelectedCategory"
                                                            :selected-category="selectedCategory"
                                                            :category="category"
                                                            @deleteCategory="deleteCategory"
                                                            @changeSelectedCategory="changeSelectedCategory"
                                                            :delete-button="true"
                                                        />
                                                    </template>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
    </AuthenticatedLayout>

</template>

<script>
import CategoryRow from '@/Pages/Category/CategoryRow.vue'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Errors from "@/Components/Errors/Errors.vue";
import Spinner from "@/Components/Spinner.vue";
export default {
    name: "Index",
    components: {Spinner, Errors, AuthenticatedLayout, CategoryRow},
    props: ['categoriesData'],
    data () {
        return {
            isLoading: false,
            categories: this.categoriesData?.data,
            selectedCategory: null,
            newCategory: null,
            errors: null,
        }
    },
    methods: {
        async addCategory() {
            try {
                this.isLoading = true
                let data = {
                    name: this.newCategory,
                    category_id: this.selectedCategory?.id || null
                }
                let response = await axios.post('/admin/categories', data)
                this.selectedCategory ? this.selectedCategory.child_categories.push(response.data.data) : this.categories.push(response.data.data)
                this.newCategory = null
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        },
        changeSelectedCategory(category) {
            if (this.selectedCategory?.id === category?.id) return this.selectedCategory = null
            this.selectedCategory = category
        },
        async deleteCategory(data) {
            try {
                this.isLoading = true
                await axios.delete(`/admin/categories/${data.category.id}`)
                if (data.category.parent_category_id) {
                    let index = this.categories.find(category => category.id === data.category.parent_category_id).child_categories.indexOf(data.category)
                    this.categories.find(category => category.id === data.category.parent_category_id).child_categories.splice(index, 1)
                } else {
                    this.categories = this.categories.filter(cat => cat.id !== data.category.id)
                }
                this.$emit('changeSelectedCategory', null)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.message ?? e)
            }
        },
    }
}
</script>

<style scoped>

</style>
