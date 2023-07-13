<template>
    <tr>
        <td>
            <input type="checkbox" @click="checkBoxActivated">
        </td>
        <td>
            <a v-bind:href="link"
               @click.prevent="openUpdating"
            >
                {{ title }}
            </a>
        </td>
    </tr>
</template>

<script>
export default {
    name: "SingleProject",
    props: {
        id: Number,
        title: String,
        description: String,
        image: String,
    },
    data() {
        return {
            link: "/project/" + this.id
        }
    },
    methods: {
        checkBoxActivated() {
            this.$emit('checkBox', {"id": this.id})
        },
        openUpdating() {
            this.$emit('openUpdating', {"id": this.id})
        },
    }
}
</script>

<style scoped>

</style>
