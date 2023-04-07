<template>
    <Spinner v-if="isLoading"/>
    <Errors :errors="errors"/>
    <div class="form-horizontal">
        <div class="row my-3">
            <div class="col-4">Тип пользователя</div>
            <div class="col-4">
               Организация
            </div>
<!--            <div class="col-4">-->
<!--                <button class="btn btn-warning" @click="changeUserKind('single')">Изменить тип пользователя на физическое лицо</button>-->
<!--            </div>-->
        </div>
        <div class="form-group row">
            <label for="inputName" class="col-sm-2 col-form-label">Название</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputName" placeholder="Имя" v-model="name">
            </div>
        </div>
        <div class="form-group row">
            <label for="inputEmail" class="col-sm-2 col-form-label">Почта</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="inputEmail" placeholder="Почта" v-model="email">
            </div>
        </div>
        <div class="form-group row">
            <label for="phone" class="col-sm-2 col-form-label">Телефон</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="phone" placeholder="Телефон" v-model="phone">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Юридический адрес</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="Юридический адрес" v-model="jural_address">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Дополнительный телефон</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="Дополнительный телефон" v-model="additional_phone">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">ОГРН</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="ОГРН" v-model="ogrn">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">БИК</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="БИК" v-model="bic">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">ИНН</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="ИНН" v-model="inn">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Название банка</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="Название банка" v-model="bank_name">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Корреспондентский счёт</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="Корреспондентский счёт" v-model="correspondent_account">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Расчётный счёт</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="Расчётный счёт" v-model="calculated_account">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Адрес разгрузки</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" placeholder="Адрес разгрузки" v-model="unloading_address">
            </div>
        </div>

        <div class="form-group row" v-if="groups && groups.length">
            <label>Изменить группу клиента</label>
            <select class="form-control" v-model="group_id">
                <option value="destroy">Вне группы</option>
                <option v-for="group in groups" :value="group.id">{{group.title}}</option>
            </select>
        </div>

        <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" v-model="is_subscribed_to_news">Подписать на новости
                    </label>
                </div>
            </div>
        </div>
        <template v-if="fields && fields.length">
            <div class="row">
                <div class="col-12">
                    Дополнительные поля
                </div>
            </div>
            <div class="row" v-for="field in fields" :key="field.id">
                <div class="col-3">
                    <label>{{ field.title }}</label>
                </div>

                <div class="col-9">
                    <template v-for="userField in user.fields" :key="userField.id">
                        <input v-if="userField.user_field_id === field.id" class="form-control" type="text" v-model="userField.value">
                    </template>
                </div>
            </div>
        </template>
        <div class="form-group row">
            <div class="offset-sm-2 col-sm-10">
                <button type="submit" class="btn btn-primary bg-blue-500" @click="onSubmit">Сохранить</button>
            </div>
        </div>
    </div>
</template>

<script>
import Spinner from "@/Components/Spinner.vue";
import Errors from "@/Components/Errors/Errors.vue";
export default {
    name: "ShowOrganization",
    components: {Errors, Spinner},
    props: ['user', 'groups', 'changeUserKind', 'fields'],
    data () {
        return {
            isLoading: false,
            errors: null,
            is_subscribed_to_news: this.user.is_subscribed_to_news,
            name: this.user.name,
            email: this.user.email,
            phone: this.user.phone,
            jural_address: this.user.jural_address,
            additional_phone: this.user.additional_phone,
            ogrn: this.user.ogrn,
            inn: this.user.inn,
            bic: this.user.bic,
            bank_name: this.user.bank_name,
            correspondent_account: this.user.correspondent_account,
            calculated_account: this.user.calculated_account,
            unloading_address: this.user.unloading_address,
            group_id: this.user.group_id || 'destroy',
        }
    },
    methods: {
        async onSubmit () {
            try {
                this.isLoading = true
                let data = {
                    is_subscribed_to_news: this.is_subscribed_to_news,
                    name: this.name,
                    email: this.email,
                    phone: this.phone,
                    inn: this.inn,
                    jural_address: this.jural_address,
                    additional_phone: this.additional_phone,
                    ogrn: this.ogrn,
                    bic: this.bic,
                    bank_name: this.bank_name,
                    correspondent_account: this.correspondent_account,
                    calculated_account: this.calculated_account,
                    unloading_address: this.unloading_address,
                    group_id: this.group_id === 'destroy' ? null : this.group_id,
                }
                let fields = Object.keys(this.$refs).map(key => {
                    if (key.includes('field')) return this.$refs[key][0]
                })
                let fieldsData = fields.map(field => {
                    return {
                        user_field_id: field.id,
                        value: field.value,
                    }
                })
                let response = await axios.patch(`/admin/users/${this.user.id}/update`, {data, fields: fieldsData})
                this.$page.props.user = response.data.data
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
