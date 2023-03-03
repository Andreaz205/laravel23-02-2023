<template>
    <div class="fixed top-0 left-0 w-full h-full spinner">
        <div class="relative w-full h-full ">
            <div class="bg-black opacity-25 w-full h-full absolute top-0 left-0"></div>
            <div class="absolute h-full w-full top-0 left-0 flex justify-center items-center z-10">
                <div class="center">
                    <div class="wave"></div>
                    <div class="wave"></div>
                    <div class="wave"></div>
                    <div class="wave"></div>
                    <div class="wave"></div>
                    <div class="wave"></div>
                    <div class="wave"></div>
                    <div class="wave"></div>
                    <div class="wave"></div>
                    <div class="wave"></div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    name: "Spinner"
}
</script>

<style scoped>
.spinner{
    z-index: 100000;
}
.center {
    height: 200px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.wave {
    width: 10px;
    height: 100px;
    background: linear-gradient(45deg, cyan, #fff);
    margin: 10px;
    animation: wave 1s linear infinite;
    border-radius: 20px;
}
.wave:nth-child(2) {
    animation-delay: 0.1s;
}
.wave:nth-child(3) {
    animation-delay: 0.2s;
}
.wave:nth-child(4) {
    animation-delay: 0.3s;
}
.wave:nth-child(5) {
    animation-delay: 0.4s;
}
.wave:nth-child(6) {
    animation-delay: 0.5s;
}
.wave:nth-child(7) {
    animation-delay: 0.6s;
}
.wave:nth-child(8) {
    animation-delay: 0.7s;
}
.wave:nth-child(9) {
    animation-delay: 0.8s;
}
.wave:nth-child(10) {
    animation-delay: 0.9s;
}

@keyframes wave {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1);
    }
    100% {
        transform: scale(0);
    }
}

</style>
