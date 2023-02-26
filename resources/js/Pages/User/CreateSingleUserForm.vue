<template>
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

export default {
    name: "CreateSingleUserForm",
    props: ['groups'],
    data () {
        return {
            name: null,
            phone: null,
            email: null,
            password: null,
            group_id: this.groups ? 'none' : null,
            is_subscribed_to_news: true,
        }
    },
    methods: {
        async submitForm(event) {
            event.preventDefault()
            try {
                let data = {
                    group_id: this.group_id === 'none' ? null : this.group_id,
                    name: this.name,
                    phone: this.phone,
                    email: this.email,
                    password: this.password,
                    is_subscribed_to_news: this.is_subscribed_to_news,
                }
                let response = await axios.post('/admin/users/single', data)
                router.visit('/admin/users')
            } catch (e) {
                alert(e.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
