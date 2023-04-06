<template>
    <template v-if="formData && formData.length">
        <h1 class="text-center text-xl">
            Дополнительные поля указанные в системе
        </h1>
        <div v-for="field in formData" class="row my-2">

            <template v-if="field.type === 'string'">
                <label>{{field.title}}</label>
                <input type="text" class="form-control" v-model="field.value">
                <small>{{field.description}}</small>
            </template>

            <template v-if="field.type === 'text'">
                <label>{{field.title}}</label>
                <textarea  class="form-control" v-model="field.value" />
                <small>{{field.description}}</small>
            </template>

            <template v-if="field.type === 'date'">
                <label>{{field.title}}</label>
                    <VueDatePicker v-model="field.value" placeholder="Укажите дату ..." text-input />
                <small>{{field.description}}</small>
            </template>

            <div class="flex gap-x-2 items-center my-3" v-if="field.type === 'bool'">
                <label>{{field.title}}</label>
                <input type="checkbox" class="form-control" v-model="field.value">
                <small>{{field.description}}</small>
            </div>
        </div>
    </template>

</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker'
export default {
    name: "UserFields",
    props: ['fields', 'formType', 'userKind', 'handleChangeFields'],
    components: {VueDatePicker},
    data () {
        return {
            // availableFields: this.fields.filter(field => field.user_kind !== this.userKind),
            formData: JSON.parse(JSON.stringify(this.fields?.filter(field => field.user_kind === this.userKind)))
        }
    },
    watch: {
        formData: {
            handler: function (val) {
                this.handleChangeFields(val)
            },
            deep: true,
        }
    }
}
</script>

<style scoped>

</style>
