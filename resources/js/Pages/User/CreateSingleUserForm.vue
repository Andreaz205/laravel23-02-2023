<template>
    <Spinner v-if="isLoading" />
    <Errors :errors="errors" />
    <div class="card card-primary">

        <!-- /.card-header -->
        <!-- form start -->
        <form>
            <div class="card-body">

                <div class="form-group">
                    <label>Почта</label>
                    <input type="text" class="form-control" v-model="email" placeholder="Введите почту">
                </div>

                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" class="form-control" v-model="name" placeholder="Введите имя">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" v-model="password"  placeholder="Пароль">
                </div>

                <div class="form-group">
                    <label>Телефон</label>
                    <input type="text" class="form-control" v-model="phone" placeholder="Введите телефон">
                </div>

                <div class="form-group row" v-if="groups && groups.length">
                    <label>Выберите группу клиента</label>
                    <select class="form-control" v-model="group_id">
                        <option value="none">Вне группы</option>
                        <option v-for="group in groups" :value="group.id">{{group.title}}</option>
                    </select>
                </div>

                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="news_subscribe" v-model="is_subscribed_to_news">
                    <label class="form-check-label" for="news_subscribe">Подписать на новости</label>
                </div>

                <UserFields form-type="create" :fields="fields" user-kind="single" :handle-change-fields="handleChangeFields"/>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button class="btn btn-primary bg-blue-500" @click="submitForm">Добавить</button>
            </div>
        </form>
    </div>
</template>

<script>
import {router} from "@inertiajs/vue3";
import UserFields from "@/Pages/User/UserFields.vue";
import Errors from "@/Components/Errors/Errors.vue";
import Spinner from "@/Components/Spinner.vue";

export default {
    name: "CreateSingleUserForm",
    components: {Spinner, Errors, UserFields},
    props: ['groups', 'fields'],
    data () {
        return {
            errors: null,
            fieldsData: JSON.parse(JSON.stringify(this.fields?.filter(field => field.user_kind === 'single'))),
            name: null,
            phone: null,
            email: null,
            password: null,
            group_id: this.groups ? 'none' : null,
            is_subscribed_to_news: true,
            isLoading: false,
        }
    },
    methods: {
        async submitForm(event) {
            try {
                this.isLoading = true
                event.preventDefault()
                let notFilledData = null
                let fields = this.fieldsData?.map(field => {
                    console.log(field)
                    if (field.is_required && !field.value) {
                        notFilledData = field
                    }
                    return {
                        id: field.id,
                        value: field.value
                    }
                })
                if (notFilledData) {
                     alert(`Необходимо заполнить обязательные поля ${notFilledData.title}`)
                    return this.isLoading = false
                }

                let data = {
                    group_id: this.group_id === 'none' ? null : this.group_id,
                    name: this.name,
                    phone: this.phone,
                    email: this.email,
                    password: this.password,
                    is_subscribed_to_news: this.is_subscribed_to_news,
                    fields: fields
                }

                let response = await axios.post('/admin/users/single', data)
                router.visit('/admin/users')
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e.message ?? e)
            }
        },
        handleChangeFields(fields) {
            this.fieldsData = fields
        }
    }
}
</script>

<style scoped>

</style>
