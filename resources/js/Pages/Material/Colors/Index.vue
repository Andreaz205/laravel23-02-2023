<template>
    <Spinner v-if="isLoading" />
    <AuthenticatedLayout>
        <FlashMessage />
        <div class="card">
            <div class="card-header text-center text-xl relative">

                <div class="absolute left-1 top-1/2 -translate-y-1/2">
                    <Link :href="`/admin/materials/${material.id}`">
                        <button class="btn btn-default">
                            Назад
                        </button>
                    </Link>
                </div>
                Цвета для материала {{material.title}}

            </div>
        </div>
        <div class="px-5">
            <Errors :errors="errors"/>
        </div>
        <div class="card m-5">
            <div class="card-header">
                Доступные наборы свойств
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="searchTerm">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default" @click="search">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <ul class="list-group list-group-flush" v-if="paginated_material_sets.data">
                        <li class="list-group-item" v-for="(set, key) in paginated_material_sets.data">
                            <span class="flex items-center justify-between">
                                {{set.title}}
                                <span class="" v-if="set.color">
                                    <img :src="set.color.image_url" alt="" class="h-[50px] w-[50px] rounded-full">
                                </span>
                                <button v-else class="btn btn-default">
                                    <label :for="`btn-${key}`" class="mb-0 cursor-pointer">Добавить цвет</label>
                                    <input type="file" :id="`btn-${key}`" @change="handleColorChange($event, set)" class="hidden">
                                </button>
                            </span>

                        </li>
                    </ul>
                    <div v-else class="text-center p-5 text-xl">
                        Для данного материала наборы ещё не готовы!
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <Pagination :items="paginated_material_sets" :fetch-page="fetchPage"/>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link, router} from '@inertiajs/vue3'
import Errors from "@/Components/Errors/Errors.vue";
import Spinner from "@/Components/Spinner.vue";
import CustomPagination from "@/Components/CustomPagination.vue";
import Pagination from "@/Components/Pagination.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
export default {
    name: "Colors",
    components: {FlashMessage, Pagination, CustomPagination, Spinner, Errors, AuthenticatedLayout, Link},
    props: [
        'material',
        'paginated_material_sets'
    ],
    data () {
        return {
            isLoading: false,
            errors: null,
            searchTerm: '',
        }
    },
    methods: {
        async search () {
            try {
                this.isLoading = true
                let response = await axios.get(`/admin/materials/${this.material.id}/search?term=${this.searchTerm}`)
                this.$page.props.paginated_material_sets = response.data
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        },
        async handleColorChange(event, set) {
            try {
                this.isLoading = true
                let file = event.target.files[0]
                const formData = new FormData
                formData.append('image', file)
                let lastValueId = set[0].id
                formData.append('material_unit_value_id', lastValueId)
                let response = await axios.post(`/admin/materials/${this.material.id}/colors/add`, formData)
                set.color = response.data
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
            }

        },
        fetchPage(url) {
            router.visit(this.$page.props.ziggy.location + url)
        }
    }
}
</script>

<style scoped>

</style>
