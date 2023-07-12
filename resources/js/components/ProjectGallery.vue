<template>
    <div class="creating" v-if="Creating">
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
    <div class="updating" v-else-if="Updating">
        <form class="decor">
            <div class="form-left-decoration"></div>
            <div class="form-right-decoration"></div>
            <div class="circle"></div>
            <div class="form-inner">
                <img alt="Изображение проекта" class="" :src="redacted.image">
                <input type="text" v-model="redacted.title">
                <textarea placeholder="Описание" rows="5" v-model="redacted.description"></textarea>
                <input type="file" id="" name="image" accept="image/*" v-on:change="updateFileChange">
                <input type="submit" value="Вернуться" v-on:click.prevent="closeUpdating">
                <input type="submit" value="Сохранить" v-on:click.prevent="modifyProject">
            </div>
        </form>
    </div>
    <div class="wrapper" v-else>
        <main class="main">
            <div class="main__container">
                <div class="main__options options">
                    <a href="" class="options__inactive">
                        <button v-on:click.prevent="deleteSelected" class="glow-button">
                            Удалить
                        </button>
                    </a>
                    <a href="" class="options__inactive">
                        <button v-on:click.prevent="viewCreating" class="glow-button">
                            Создать
                        </button>
                    </a>
                </div>
                <table class="main__list">
                    <tr>
                        <th>checkbox</th>
                        <th>Название проекта</th>
                    </tr>
                    <SingleProject
                        v-for="project in projects"
                        v-bind:id="project.id"
                        v-bind:title="project.title"
                        v-bind:description="project.description"
                        v-bind:image="project.image"
                        v-bind:key="project.id"
                        @checkBox="selecting"
                        @changeInfo="updateProject"
                    ></SingleProject>
                </table>
            </div>
        </main>
    </div>
</template>

<script>

import axios from 'axios';

import SingleProject from './SingleProject.vue'

export default {
    components: {
        SingleProject
    },
    name: "ProjectGallery",
    data() {
        return {
            Creating: false,
            Updating: false,
            newProject: {
                "title": '',
                "description": '',
                "image": null,
                "file": null,
            },
            redacted: {
                "id": '',
                "title": '',
                "description": '',
                "image": '',
                "file": '',
            },
            projects: [],
            checkbox: [],
            tempFile: null
        }
    },
    methods: {
        //////////
        selecting(data) {
            if (this.checkbox.includes(data['id'])) {
                this.checkbox = this.checkbox.filter(item => item !==data['id'])
            } else {
                this.checkbox.push(data['id'])
            }
        },
        async getProjects() {
            try {
                const response = await fetch('api/v1/projects');
                const result = await response.json();
                this.projects = result.data;
            } catch (error) {
                this.errors.push(error);
                console.log(this.errors)
            }
        },
        viewCreating() {
            this.Creating = true
        },
        closeCreating() {
            this.tempFile = null
            this.Creating = false
            this.getProjects()
        },
        deleteSelected() {
            if (this.checkbox.length > 0) {
                try {
                    this.checkbox.forEach(function (element) {
                        const url = 'http://127.0.0.1:8000/api/v1/projects/' + element
                        axios.delete(url)
                            .then(response => {})
                            .catch(error => {
                                console.error(error);
                            });
                    })
                } catch (error) {
                    console.error(error);
                }
                this.checkbox = [];
                this.getProjects();
            }
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
                    this.newProject['title'] = ''
                    this.newProject['description'] = ''
                    this.tempFile = null
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
        updateProject(data) {
            this.redacted['id'] = data['id']

            const obj = this.projects.find(obj => obj.id === this.redacted['id']);
            this.redacted.title = obj.title
            this.redacted.description = obj.description
            this.redacted.image = obj.image

            this.Updating = true
        },
        closeUpdating() {
            this.tempFile = null
            this.Updating = false
            this.getProjects()
        },
        async modifyProject() {
            const formData = new FormData();
            formData.append('title', this.redacted['title']);
            formData.append('description', this.redacted['description']);
            formData.append('image', this.redacted['file']);
            // Господи, величайший баг современности
            formData.append('_method', 'PATCH');

            let url = 'http://127.0.0.1:8000/api/v1/projects/' + this.redacted['id'];

            try {
                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                    .then(response => {
                        console.log(response.data)
                    })
                    .catch(error => {
                        console.error(error);
                    })
                    .finally(async () => {
                        await this.getProjects()
                        this.updateProject({'id': this.redacted['id']})
                    });
            } catch (error) {
                console.error(error);
            }
        },
        createFileChange(event) {
            this.newProject.file = event.target.files[0];
        },
        updateFileChange(event) {
            this.redacted.file = event.target.files[0];
        },
    },
    mounted() {
        this.getProjects()
    }
}
</script>

<style scoped>

</style>
