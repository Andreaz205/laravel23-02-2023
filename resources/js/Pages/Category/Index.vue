<template>

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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="flex justify-between items-center w-full">
                            <h3 class="card-title">Управление категориями</h3>
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ./card-header -->
                    <div class="card-body p-0">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <td class="border-0 flex flex-nowrap">
                                    <button
                                            type="button" class="mr-2"
                                            data-toggle="modal" data-target="#addCategoryButton"
                                    >
                                        <i class="fas fa-plus" ></i>
                                    </button>
                                    <button id="close" class="mr-2">
                                        <i class="fas fa-times" ></i>
                                    </button>
                                    <form id="edit">
                                        <button type="button"
                                                data-toggle="modal" data-target="#editCategoryButton">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                            <tr aria-expanded="true" id="catalog">
                                <td>
                                    <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                                    Каталог
                                </td>
                            </tr>
                            <tr class="expandable-body">
                                <td>
                                    <div class="p-0">
                                        <table class="table table-hover">
                                            <tbody>
                                                <template v-if="categories && categories.length">
                                                    <template v-for="category in categories">
                                                        <CategoryRow v-if="!category.parent_category_id" :selected-category="selectedCategory" :category="category" :key="category.id" @changeSelectedCategory="changeSelectedCategory"/>
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
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<script>
import CategoryRow from '@/Pages/Category/CategoryRow.vue'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    name: "Index",
    components: {AuthenticatedLayout, CategoryRow},
    props: ['categoriesData'],
    data () {
        return {
            categories: this.categoriesData?.data,
            selectedCategory: null,
            newCategory: null,
        }
    },
    methods: {
        async addCategory() {
            try {
                let data = {
                    name: this.newCategory,
                    category_id: this.selectedCategory?.id || null
                }
                let response = await axios.post('/admin/categories', data)
                this.categories.push(response.data.data)
            } catch (e) {
                alert(e)
            }
        },
        changeSelectedCategory(category) {
            if (this.selectedCategory?.id === category.id) return this.selectedCategory = null
            this.selectedCategory = category
        }
    }
}
</script>

<style scoped>

</style>
