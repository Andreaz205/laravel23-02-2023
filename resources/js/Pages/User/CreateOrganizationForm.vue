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
                        <label>Название организации</label>
                        <input type="text" class="form-control" v-model="name" placeholder="Введите название организации">
                    </div>

                    <div class="form-group">
                        <label>Юридический адрес</label>
                        <input type="text" class="form-control" v-model="jural_address" placeholder="Введите юридический адрес">
                    </div>

                    <div class="form-group">
                        <label>ИНН</label>
                        <input type="text" class="form-control" v-model="inn" placeholder="Введите ИНН">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" v-model="password"  placeholder="Пароль">
                    </div>

                    <div class="form-group">
                        <label>Телефон</label>
                        <input type="text" class="form-control" v-model="phone" placeholder="Введите телефон">
                    </div>

                    <div class="form-group">
                        <label>Дополнительный телефон</label>
                        <input type="text" class="form-control" v-model="additional_phone" placeholder="Введите дополнительный телефон">
                    </div>

                    <div class="form-group">
                        <label>ОГРН</label>
                        <input type="text" class="form-control" v-model="ogrn" placeholder="Введите ОГРН">
                    </div>

                    <div class="form-group">
                        <label>БИК</label>
                        <input type="text" class="form-control" v-model="bic" placeholder="Введите БИК">
                    </div>

                    <div class="form-group">
                        <label>Название банка</label>
                        <input type="text" class="form-control" v-model="bank_name" placeholder="Введите название банка">
                    </div>

                    <div class="form-group">
                        <label>Корреспондентский счёт</label>
                        <input type="text" class="form-control" v-model="correspondent_account" placeholder="Введите корреспондентский счёт">
                    </div>

                    <div class="form-group">
                        <label>Расчётный счёт</label>
                        <input type="text" class="form-control" v-model="calculated_account" placeholder="Введите расчётный счёт">
                    </div>

                    <div class="form-group">
                        <label>Адрес разгрузки</label>
                        <input type="text" class="form-control" v-model="unloading_address" placeholder="Введите адрес разгрузки">
                    </div>

                    <div class="form-group row" v-if="groups && groups.length">
                        <label>Выберите группу клиента</label>
                        <select class="form-control" v-model="group_id">
                            <option value="none">Вне группы</option>
                            <option v-for="group in groups" :value="group.id">{{group.title}}</option>
                        </select>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="news_subscribe-organization" v-model="is_subscribed_to_news">
                        <label class="form-check-label" for="news_subscribe-organization">Подписать на новости</label>
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
    name: "CreateOrganizationForm",
    props: ['groups'],
    data() {
        return {
            email: null,
            name: null,
            password: null,
            jural_address: null,
            inn: null,
            phone: null,
            additional_phone: null,
            ogrn: null,
            bic: null,
            bank_name: null,
            correspondent_account: null,
            calculated_account: null,
            unloading_address: null,
            group_id: this.groups ? 'none' : null,
            is_subscribed_to_news: true,
        }
    },
    methods: {
        async submitForm(event){
            event.preventDefault()
            try {
                let data = {
                    group_id: this.group_id === 'none' ? null : this.group_id,
                    email: this.email,
                    name: this.name,
                    password: this.password,
                    jural_address: this.jural_address,
                    inn: this.inn,
                    phone: this.phone,
                    additional_phone: this.additional_phone,
                    ogrn: this.ogrn,
                    bic: this.bic,
                    bank_name: this.bank_name,
                    correspondent_account: this.correspondent_account,
                    calculated_account: this.calculated_account,
                    unloading_address: this.unloading_address,
                    is_subscribed_to_news: this.is_subscribed_to_news,
                }
                let response = await axios.post('/admin/users/organizations', data)
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
