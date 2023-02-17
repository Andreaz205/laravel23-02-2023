<template>
    <div v-if="isOpenProp" class="fixed h-full w-full top-0 left-0 z-50" @click="clickCallback">
        <div class="relative h-full w-full">
            <div class="absolute top-0 left-0 h-full w-full bg-black opacity-25 z-20"></div>
            <div class="absolute top-0 left-0 h-full w-full z-30 flex justify-center items-center">
                <div class="w-96 h-96 bg-white" ref="popup">
                    <div class="relative">
                        <i class="fas fa-times absolute top-0 right-0"
                           @click="$emit('close')"></i>
                        <div class="m-3">

                            <div class="popup-field">
                                <div>Придумайте почту</div>
                                <div>
                                    <input type="email" v-model="managerEmailForCreate" placeholder="Введите Email">
                                </div>
                            </div>

                            <div class="popup-field">
                                <div>Придумайте имя</div>
                                <div>
                                    <input type="text" v-model="managerNameForCreate" placeholder="Введите имя">
                                </div>
                            </div>

                            <div class="popup-field">
                                <div>Придумайте пароль</div>
                                <div>
                                    <input type="password" v-model="managerPasswordForCreate" placeholder="Введите пароль">
                                </div>
                            </div>

                            <div class="popup-field">
                                <div>Подтвердите пароль</div>
                                <div>
                                    <input type="password" v-model="managerPasswordConfirmationForCreate" placeholder="Подтвердите пароль">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="m-3">
                        <button
                            @click="createManager(
                                managerNameForCreate,
                                managerEmailForCreate,
                                managerPasswordForCreate,
                                managerPasswordConfirmationForCreate
                            )" class="btn btn-primary"
                        >
                            Создать
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Popup",
    props: [
        'isOpenProp',
        'createManager'
    ],
    data () {
        return {
            managerEmailForCreate: null,
            managerNameForCreate: null,
            managerPasswordForCreate: null,
            managerPasswordConfirmationForCreate: null,
        }
    },
    methods: {
        clickCallback(event) {
            if (!this.$refs.popup.contains(event.target)) {
                this.$emit('close')
            }
        }
    }

}
</script>

<style scoped>

</style>
