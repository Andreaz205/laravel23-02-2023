<template>

    <Spinner v-if="isLoading"/>
    <div class="modal fade" id="createFieldModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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

                       <Errors :errors="errors"/>

                       <div class="row">
                           <div class="col-4">
                               <label>Название</label>
                           </div>
                           <div class="col-8">
                               <input type="text" class="form-control" v-model="title">
                           </div>
                       </div>

                       <div class="row">
                            <div class="col-4">
                                <label>Обязательное</label>
                            </div>
                           <div class="col-8">
                               <input type="checkbox" class="form-control" v-model="is_required">
                           </div>
                       </div>

                       <div class="row">
                           <div class="col-4">
                               <label>Заполняется клиентом</label>
                           </div>
                           <div class="col-8">
                               <input type="checkbox" class="form-control" v-model="is_user_fill">
                           </div>
                       </div>

                       <div class="row">
                           <div class="col-4">
                               <label>Тип клиента</label>
                           </div>
                           <div class="col-8">
                               <label for="single">Физическое лицо</label>
                               <input type="radio" class="form-control" v-model="user_kind" value="single" id="single">
                               <label for="organization">Организация</label>
                               <input type="radio" class="form-control" v-model="user_kind" value="organization" id="organization">
                           </div>
                       </div>

                       <div class="row ">
                           <div class="col-4">
                               <label>Тип</label>
                           </div>
                           <div class="col-8">
                               <select class="form-control" v-model="type">
                                   <option value="string">Строковое</option>
                                   <option value="text">Несколько строк</option>
                                   <option value="date">Дата</option>
                                   <option value="bool">Да/Нет(Checkbox)</option>
                               </select>
                           </div>
                       </div>

                       <div class="row mt-2">
                           <div class="col-4">
                               <label>Описание</label>
                           </div>
                           <div class="col-8">
                               <textarea  class="form-control" v-model="description" />
                           </div>
                       </div>
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть
                    </button>
                    <button type="button" class="btn btn-primary bg-blue-500" @click="submit">Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>


    <AuthenticatedLayout>

        <div class="card">
            <div class="card-header">
                Дополнительные поля клиентов
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap" v-if="fields && fields.length">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Тип</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr class="cursor-pointer" v-for="field in fields">
                            <td>{{ field.id }}</td>
                            <td>{{ field.title }}</td>
                            <td>{{ field.type === 'string' ? 'Строковое' : field.type === 'text' ? 'Несколько строк' ? field.type === 'date' : 'Дата' : 'Логическое Да/Нет(Checkbox)'}}</td>
                            <td>
                                <button @click="deleteField(field)" class="btn btn-danger">
                                    Удалить
                                </button>
                            </td>
                        </tr>

                    </tbody>

                </table>
                <div v-else class="h-96 flex items-center justify-center text-xl">
                    Вы ещё не создавали полей!
                </div>
            </div>
            <div class="card-footer">
                <button
                    type="button" class="btn btn-primary bg-blue"
                    data-toggle="modal" data-target="#createFieldModal"
                >
                    Добавить
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Spinner from "@/Components/Spinner.vue";
import Errors from "@/Components/Errors/Errors.vue";
export default {
    name: "UserSettings",
    components: {Errors, Spinner, AuthenticatedLayout},
    props: [
        'fieldsProps'
    ],
    data () {
        return {
            user_kind: 'single',
            fields: this.fieldsProps,
            errors: null,
            title: null,
            is_required: false,
            is_user_fill: true,
            type: 'string',
            description: null,
            isLoading: false
        }
    },
    methods: {
        async submit () {
            try {
                this.isLoading = true
                let data = {
                    title: this.title,
                    is_required: this.is_required,
                    is_user_fill: this.is_user_fill,
                    user_kind: this.user_kind,
                    type: this.type,
                    description: this.description
                }
                let response = await axios.post(`/admin/user-settings/fields`, data)
                this.fields.push(response.data)
                this.errors = null
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) {
                    return this.errors = e.response.data.errors
                }
                alert(e.message)
            }
        },
        async deleteField(field) {
            try {
                this.isLoading = true
                await axios.delete(`/admin/user-settings/fields/${field.id}`)
                this.fields.splice(this.fields.indexOf(field), 1)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e.message ?? e)
            }
        }
    }

}
</script>

<style scoped>

</style>
