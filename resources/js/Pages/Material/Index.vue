<template>
    <Spinner v-if="isLoading"/>
    <div class="modal fade" id="createOptionsModal" tabindex="-1" role="dialog" aria-labelledby="optionNameColorModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Указать свойства</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <Errors :errors="errors"/>
                    <div class="row">
                        <label class="col-4">Введите название</label>
                        <div class="col-8">
                            <input type="text" v-model="newMaterialTitle" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal" ref="close">Закрыть</button>
                    <button
                        class="btn btn-primary"
                        @click="createOption"
                    >
                        Сохранить
                    </button>
                    <!--                        <button type="button" class="btn btn-primary bg-blue-500" @click="saveOption">Сохранить-->
                    <!--                        </button>-->
                </div>
            </div>
        </div>
    </div>
    <AuthenticatedLayout>
        <Errors :errors="errors" />
        <FlashMessage />
        <div class="text-center text-xl p-4">
            Категории
        </div>
        <template v-if="categories && categories.length">
            <div class="container-fluid mt-3">
                <div class="row">
                    <div v-for="category in categories" :key="category.id" class="col-4">
                        <Link :href="`/admin/materials/categories/${category.id}`">
                            <div class="card p-4 min-h-[200px] hover:bg-gray-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{category.name}}</h5>
                                    <div class="card-text" v-if="category.materials && category.materials.length">
                                        <div v-for="(material, key) in category.materials.slice(0,3)">
                                            <div>
                                                - {{material.title}}
                                            </div>
                                            <div v-if="key == 2">...</div>
                                        </div>


                                    </div>
                                    <div v-else class="card-text">
                                        У данной категории не указаны материалы!
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </template>
        <template v-else>
            Вы еще не создавали категорий!
        </template>

        <div class="text-center text-xl p-4">
            Материалы
        </div>
        <template v-if="materials && materials.length">
            <div class="container-fluid mt-3">
                <div class="row">
                    <div v-for="material in materials" :key="material.id" class="col-4">
                            <div class="card min-h-[200px]  relative">

                                <Link :href="`/admin/materials/${material.id}`" class="block card-body p-4 hover:bg-gray-100">

                                        <h5 class="card-title">{{material.title}}</h5>
                                        <div class="card-text mt-10" v-if="material.plain_units && material.plain_units.length">
                                            <div class="flex">
                                                <small v-for="(unit, key) in material.plain_units.slice(0,3)">
                                                    {{unit?.title}} <i class="fas fa-long-arrow-alt-right mx-2" v-if="key !== 2 && key < material.plain_units.length - 1 "></i>
                                                </small>
                                            </div>


                                        </div>
                                        <div v-else class="card-text">
                                            К данному материалу не указаны элементы!
                                        </div>



                                </Link>
                                <div
                                    :class="[
                                        'absolute top-5 right-5 h-[50px] w-[50px] flex justify-center items-center rounded-xl cursor-pointer transition duration-300',
                                        {'bg-gray-300 hover:opacity-70 hover:bg-sky-300': !material.with_colors, 'bg-sky-300 hover:opacity-70 hover:bg-red-300': material.with_colors},

                                    ]"
                                     @click="handleColorClick(material)"
                                >
                                    <i class="fas fa-palette text-gray-700"></i>
                                </div>
                            </div>
                    </div>
                </div>
            </div>

        </template>


        <button
            type="button" class="btn btn-primary bg-blue"
            data-toggle="modal"
            data-target="#createOptionsModal"
        >
            Добавить
        </button>
        <ContentPreview />
    </AuthenticatedLayout>
</template>

<script>
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Spinner from '@/Components/Spinner.vue'
import Errors from "@/Components/Errors/Errors.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
import ContentPreview from "@/Pages/Material/ContentPreview.vue";
export default {
    name: "Index",
    components: {ContentPreview, FlashMessage, Errors, AuthenticatedLayout, Link, Spinner},
    props: ['categories', 'materials'],
    data () {
        return {
            newMaterialTitle: '',
            isLoading: false,
            errors: null,
        }
    },
    methods: {
        async createOption() {
            try {
                this.isLoading = true
                let data = {
                    title: this.newMaterialTitle
                }
                let response = await axios.post(`/admin/materials`, data)
                this.$page.props.materials.push(response.data)
                this.isLoading = false
                this.$refs.close.click()
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        },
        async handleColorClick(material) {
            try {
                this.isLoading = true
                this.$inertia.patch(`/admin/materials/${material.id}/toggle-color`)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
