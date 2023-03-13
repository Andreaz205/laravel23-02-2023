<template>
    <div class="modal fade" id="createAccentPropertyModal" tabindex="-1" role="dialog" aria-labelledby="createAccentPropertyModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить специальное свойство</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">
                        Закрыть
                    </button>
                    <button
                        class="btn btn-primary"
                        @click="submit"
                    >
                        Сохранить
                    </button>
                    <!--                        <button type="button" class="btn btn-primary bg-blue-500" @click="saveOption">Сохранить-->
                    <!--                        </button>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "EditAccentPropertiesModal"
}
</script>

<style scoped>

</style>
