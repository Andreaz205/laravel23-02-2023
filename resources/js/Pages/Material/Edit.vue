<template>
    <div class="modal fade" id="bindOptionsModal" tabindex="-1" role="dialog" aria-labelledby="optionNameColorModal"
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
                    <template v-if="materials && materials.length">
                        <div v-for="material in materials" class="row">
                            <label class="col-4">{{material.title}}</label>
                            <div class="col-8">
                                <input type="checkbox" class="form-control" v-model="materialsForBind" :value="material.id">
                            </div>

                        </div>

                    </template>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                    <button
                        class="btn btn-primary"
                        @click="saveBindingChanges"
                    >
                        Сохранить
                    </button>

                </div>
            </div>
        </div>
    </div>
    <Spinner v-if="isLoading" />
    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header text-center text-lg relative">
                <div class="absolute left-3 top-1/2 -translate-y-1/2">
                    <button class="btn btn-default">
                        <Link href="/admin/materials">
                            Назад
                        </Link>
                    </button>
                </div>

                Свойства категории {{category.name}}
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush" v-if="category.materials && category.materials.length">
                    <li class="list-group-item" v-for="material in category.materials">{{material.title}}</li>
                </ul>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <button
                            type="button" class="btn btn-primary bg-blue"
                            data-toggle="modal" data-target="#bindOptionsModal"
                        >
                            Указать свойства
                        </button>
                    </li>
                </ul>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script>
import {ModelSelect} from 'vue-search-select'
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Spinner from "@/Components/Spinner.vue";
import 'vue-search-select/dist/VueSearchSelect.css';
import Errors from "@/Components/Errors/Errors.vue";
export default {
    name: "Edit",
    components: {Errors, AuthenticatedLayout, Spinner, ModelSelect, Link},
    props: [
        'category',
        'materials'
    ],
    data () {
        return {
            materialsForBind: this.category.materials.map(m => m.id),
            errors: null,
            isLoading: false,
            options: [
                {value: '1', text: '6'},
                {value: '2', text: '67'},
            ],
            item: {
                value: null,
                text: null,
            }
        }
    },
    methods: {
        async saveBindingChanges() {
            try {
                this.isLoading = true
                let data = {
                    materials: this.materialsForBind
                }
                let response = await axios.patch(`/admin/materials/${this.category.id}`, data)
                this.$page.props.category = response.data
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        }
    },
}
</script>

<style scoped>

</style>
