<template>
    <img
        :src="file"
        alt="File"
        class="img-fluid img-thumbnail rounded"
        :width="width"
        :height="height"
        @error="setAltImg"
        @click="ZoomImage"
    />
    <a
        type="hidden"
        ref="openModal"
        data-bs-toggle="modal"
        :data-bs-target="'#ZoomImage' + modelId"
    >
    </a>

    <div class="modal fade" :id="'ZoomImage' + modelId" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="block-content text-center">
                        <img
                            :src="file"
                            class="img-thumbnail"
                            @error="setAltImg"
                        />
                        <br />
                        <button
                            type="button"
                            class="btn btn-sm btn-dark mt-2"
                            data-bs-dismiss="modal"
                        >
                            {{ ("Close") }}
                        </button>
                        <a
                            class="btn btn-sm btn-dark mt-2 ms-2"
                            download
                            :href="file"
                        >
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// import Master from "@/Pages/Backend/Layouts/Master";
export default {
    // layout: Master,
    props: ["file", "width", "height"],
    // created()
    // {
    //   alert(this.file);
    // },
    data() {
        return {
            modelId: Math.floor(Math.random() * 1000),
        };
    },
    methods: {
        ZoomImage() {
            this.$refs.openModal.click();
        },

        setAltImg(event) {
            event.target.src = "/images/default.jpg";
        },
    },
};
</script>

<style>
@import "@vueform/multiselect/themes/default.css";

.c-file-padding {
    padding: 1rem 0.75rem !important;
}
.invalid-feedback {
    display: block !important;
}
/* .invalid-bg {
    border-color: #f8d4d4 !important;
    background-color: #f8d4d4 !important;
} */

#CodeSelect {
    min-height: 58px;
}
.card {
    overflow: visible;
}

.remove-status {
    margin-left: 15px !important;
    margin-top: 5px !important;
}
</style>
