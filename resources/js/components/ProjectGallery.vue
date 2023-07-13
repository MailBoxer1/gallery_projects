<template>

    <CreatingProject v-if="Creating"
    @closeCreating = "closeCreating"
    ></CreatingProject>

    <UpdatingProject  v-else-if="Updating"
    @closeUpdating = "closeUpdating"
    @updateProjects = "openUpdating"
    :id="id"
    :title="title"
    :description="description"
    :image="image"
    ></UpdatingProject>

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
                        @openUpdating="openUpdating"
                    ></SingleProject>
                </table>
            </div>
        </main>
    </div>
</template>

<script>

import axios from 'axios';

import SingleProject from './SingleProject.vue'
import CreatingProject from "./CreatingProject.vue";
import UpdatingProject from "./UpdatingProject.vue";

export default {
    components: {
        SingleProject, CreatingProject, UpdatingProject
    },
    name: "ProjectGallery",
    data() {
        return {
            Creating: false,
            Updating: false,
            projects: [],
            checkbox: [],
            id: "",
            title: "",
            description: "",
            image: "",
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
        openUpdating(data) {
            this.id = data['id']

            const obj = this.projects.find(obj => obj.id === this['id']);
            this.title = obj.title
            this.description = obj.description
            this.image = obj.image

            this.Updating = true
        },
        closeUpdating() {
            this.Updating = false
            this.getProjects()
        },

    },
    mounted() {
        this.getProjects()
    }
}
</script>

<style scoped>

</style>
