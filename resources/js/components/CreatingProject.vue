<template>
    <div class="creating">
        <form class="decor" enctype="multipart/form-data">
            <div class="form-left-decoration"></div>
            <div class="form-right-decoration"></div>
            <div class="circle"></div>
            <div class="form-inner">
                <img src="" alt="Изображение проекта" class="" v-if="newProject.image">
                <input type="text" placeholder="Название" v-model="newProject.title">
                <textarea placeholder="Описание" rows="5" v-model="newProject.description"></textarea>
                <input type="file" id="" name="image" accept="image/*" v-on:change="createFileChange">
                <input type="submit" value="Вернуться" v-on:click.prevent="closeCreating">
                <input type="submit" value="Сохранить" v-on:click.prevent="saveProject">
            </div>
        </form>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "CreatingProject",
    props: [],
    data() {
        return {
            newProject: {
                "title": '',
                "description": '',
                "image": null,
                "file": null,
            },
        }
    },
    methods: {
        createFileChange(event) {
            this.newProject.file = event.target.files[0];
        },
        async saveProject() {
            const formData = new FormData();
            formData.append('title', this.newProject['title']);
            formData.append('description', this.newProject['description']);
            formData.append('image', this.newProject['file']);

            const url = 'http://127.0.0.1:8000/api/v1/projects';

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                    .then(response => {
                        this.newProject.title = ''
                        this.newProject.description = ''
                        this.newProject.file = null
                    })
                    .catch(error => {
                        console.error(error);
                    })
                    .finally(() => {
                        this.getProjects()
                    });
            } catch (error) {
                console.error(error);
            }
        },
        closeCreating() {
            this.$emit('closeCreating')
        }
    }
}
</script>

<style scoped>

</style>
