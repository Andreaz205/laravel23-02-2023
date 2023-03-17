<template>
    <div>
        Hello world!!!
        <button class='btn btn-primary' @click="handlePut">
            Положить
        </button>
        <button class="btn btn-default" @click="handleGet">
            Получить
        </button>
        <button class="btn btn-default" @click="send">
            Отправить
        </button>
        <button @click="getRecaptchaToken" class="btn btn-default">
            Посмотреть Recaptcha
        </button>
<!--        <button>-->
<!--            Инициализировать токен для recptcha-->
<!--        </button>-->
    </div>
</template>

<script>
export default {
    name: "Test",
    methods: {
        async handlePut() {
            let response = await axios.get('/put')
        },
        async handleGet() {
            let response = await axios.get('/get')
        },
        async send() {
            let response = await axios.get(`https://sms.ru/sms/send?api_id=650E3AE5-B44F-7436-93E4-F03DF2D4239C&to=89124046267&msg=hello+world&json=1`)
        },
        async getRecaptchaToken() {
            let response = await axios.get(`/api/recaptcha`)
            alert(response.data)
        }


    },
    mounted() {
        let reCaptcha = new Promise((resolve, reject) => {
            const $script = document.createElement('script')
            $script.src = 'https://www.google.com/recaptcha/api.js?render=6Ld6fQolAAAAAFZ9jfo8-dDBmQb0CoeTUEmb7PQU'
            resolve(document.head.appendChild($script));
            setTimeout(() => reject(new Error("Google reCaptcha не инициализирована")), 3000);
        });
        reCaptcha
            .then(
                result => {
                    setTimeout(() => {
                        grecaptcha.ready(function() {
                            grecaptcha.execute('6Ld6fQolAAAAAFZ9jfo8-dDBmQb0CoeTUEmb7PQU', {action: 'homepage'}).then(function(token) {
                                axios
                                    .post('/recaptcha', {
                                        token: token
                                    })
                                    .catch(() => {
                                        console.log('POST-запрос в API Google не был отправлен.');
                                    })

                            });
                        });
                    }, 1000)
                },
                error => {
                    console.log(error);
                });
    }

}
</script>

<style scoped>

</style>
