<template>
    <div class="updating">
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
</template>

<script>
import axios from "axios";

export default {
    name: "UpdatingProject",
    props: {
        id: Number,
        title: String,
        description: String,
        image: String,
    },
    data() {
        return {
            redacted: {
                "id": this.id,
                "title": this.title,
                "description": this.description,
                "image": this.image,
                "file": null,
            },
        }
    },
    methods: {
        updateFileChange(event) {
            this.redacted.file = event.target.files[0];
        },
        async modifyProject() {
            const formData = new FormData();
            formData.append('title', this.redacted.title);
            formData.append('description', this.redacted.description);
            formData.append('image', this.redacted.file);
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
                        const data = response.data['data']
                        this.redacted.id = data.id
                        this.redacted.title = data.title
                        this.redacted.description = data.description
                        this.redacted.image = data.image
                    })
                    .catch(error => {
                        console.error(error);
                    })
                    .finally(async () => {
                        await this.updateProjects({'id': this.redacted.id})
                    });
            } catch (error) {
                console.error(error);
            }
        },
        closeUpdating() {
            this.$emit('closeUpdating')
        },
        updateProjects(data) {
            this.$emit('updateProjects', data)
        }
    }
}
</script>

<style scoped>

</style>
