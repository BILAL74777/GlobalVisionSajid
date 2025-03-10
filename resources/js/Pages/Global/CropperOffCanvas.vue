<template>
    <button
        @click="openImageSelector"
        type="button"
        class="btn btn-sm btn-success fs-6"
    >
        {{ "Change Picture" }}
    </button>

    <button
        style="display: none"
        type="button"
        class="btn btn-sm btn-success fs-6"
        data-bs-toggle="offcanvas"
        :data-bs-target="'#offcanvasTop-' + randId"
        :title="'Open'"
        aria-controls="offcanvasTop"
        ref="openOffCanvas"
    >
        {{ "Modal opener" }}
    </button>
    <input
        ref="imageInput"
        type="file"
        name="image"
        accept="image/*"
        @change="handleImageChange"
        style="display: none"
    />

    <!-- delete offcanvas -->
    <div
        class="offcanvas offcanvas-start"
        tabindex="-1"
        :id="'offcanvasTop-' + randId"
        aria-labelledby="offcanvasTopLabel"
    >
        <div class="offcanvas-body">
            <div class="block-content text-center p-4">
                <div>
                    <section class="cropper-area">
                        <div class="img-cropper row">
                            <div class="col-md-8">
                                <vue-cropper
                                    ref="cropper"
                                    :aspect-ratio="200 / 200"
                                    :src="imgSrc"
                                    preview=".preview"
                                />
                            </div>
                            <div class="col-md-4">
                                <p>{{ "Preview" }}</p>
                                <div class="preview" />
                            </div>
                        </div>
                        <div class="actions">
                            <a
                                href="#"
                                class="btn btn-sm btn-info"
                                role="button"
                                @click.prevent="zoom(0.2)"
                            >
                                {{ "Zoom In" }}
                            </a>
                            <a
                                href="#"
                                class="btn btn-sm btn-info"
                                role="button"
                                @click.prevent="zoom(-0.2)"
                            >
                                {{ "Zoom Out" }}
                            </a>
                            <a
                                href="#"
                                class="btn btn-sm btn-info"
                                role="button"
                                @click.prevent="move(-10, 0)"
                            >
                                {{ "Move Left" }}
                            </a>
                            <a
                                href="#"
                                class="btn btn-sm btn-info"
                                role="button"
                                @click.prevent="move(10, 0)"
                            >
                                {{ "Move Right" }}
                            </a>
                            <a
                                href="#"
                                class="btn btn-sm btn-info"
                                role="button"
                                @click.prevent="move(0, -10)"
                            >
                                {{ "Move Up" }}
                            </a>
                            <a
                                href="#"
                                class="btn btn-sm btn-info"
                                role="button"
                                @click.prevent="move(0, 10)"
                            >
                                {{ "Move Down" }}
                            </a>
                            <a
                                href="#"
                                class="btn btn-sm btn-info"
                                role="button"
                                @click.prevent="rotate(90)"
                            >
                                {{ "Rotate +90deg" }}
                            </a>
                            <a
                                href="#"
                                class="btn btn-sm btn-info"
                                role="button"
                                @click.prevent="rotate(-90)"
                            >
                                {{ "Rotate -90deg" }}
                            </a>
                            <a
                                ref="flipX"
                                class="btn btn-sm btn-info"
                                href="#"
                                role="button"
                                @click.prevent="flipX"
                            >
                                {{ "Flip X" }}
                            </a>
                            <a
                                ref="flipY"
                                class="btn btn-sm btn-info"
                                href="#"
                                role="button"
                                @click.prevent="flipY"
                            >
                                {{ "Flip Y" }}
                            </a>

                            <a
                                href="#"
                                class="btn btn-sm btn-info"
                                role="button"
                                @click.prevent="reset"
                            >
                                {{ "Reset" }}
                            </a>
                            <!-- <a href="#" class="btn btn-sm btn-info" role="button" @click.prevent="getData">
                                    Get Data
                                </a> -->
                            <!-- <a href="#" class="btn btn-sm btn-info" role="button" @click.prevent="setData">
                                    Set Data
                                </a> -->
                            <!-- <a href="#" class="btn btn-sm btn-info" role="button" @click.prevent="getCropBoxData">
                                    Get CropBox Data
                                </a> -->
                            <!-- <a href="#" class="btn btn-sm btn-info" role="button" @click.prevent="setCropBoxData">
                                    Set CropBox Data
                                </a> -->
                            <br /><br />
                            <button
                                type="button"
                                ref="closeOffCanvas"
                                class="btn btn-sm btn-outline-success"
                                data-bs-dismiss="offcanvas"
                            >
                                {{ "Close" }}
                            </button>

                            <a
                                href="#"
                                class="btn btn-sm btn-success"
                                role="button"
                                @click.prevent="cropImage"
                            >
                                {{ "Save" }}
                            </a>
                        </div>
                    </section>
                    <!-- <section class="preview-area">

                            <p>Cropped Image</p>
                            <div class="cropped-image">
                                <img v-if="cropImg" :src="cropImg" alt="Cropped Image" />
                                <div v-else class="crop-placeholder" />
                            </div>
                        </section> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";
export default {
    components: {
        VueCropper,
    },
    emits: ["croppedImg"],
    data() {
        return {
            randId: Math.ceil(Math.random() * 1000000),
            imgSrc: "/images/default.jpg",
            cropImg: "",
            data: null,
        };
    },
    created() {},
    methods: {
        openImageSelector() {
            // Reset the file input value before triggering the click event
            this.$refs.imageInput.value = null;

            // Trigger the file input click event
            this.imgSrc = "/images/default.jpg";
            this.$refs.imageInput.click();
        },

        handleImageChange(e) {
            this.$refs.openOffCanvas.click();
            const file = e.target.files[0];

            if (file.type.indexOf("image/") === -1) {
                toastr.error("Please select an image file");
                return;
            }

            if (typeof FileReader === "function") {
                const reader = new FileReader();

                reader.onload = (event) => {
                    this.imgSrc = event.target.result;
                    // rebuild cropperjs with the updated source
                    this.$refs.cropper.replace(event.target.result);
                };

                reader.readAsDataURL(file);
            } else {
                toastr.error("Sorry, FileReader API not supported");
            }
        },
        cropImage() {
            // event.stopPropagation();
            // get image data for post processing, e.g. upload or setting image src
            this.cropImg = this.$refs.cropper
                .getCroppedCanvas()
                .toDataURL("image/webp");
            // console.log(this.cropImg);
            this.$emit("croppedImg", this.cropImg);
            this.$refs.closeOffCanvas.click();
        },
        flipX() {
            const dom = this.$refs.flipX;
            let scale = dom.getAttribute("data-scale");
            scale = scale ? -scale : -1;
            this.$refs.cropper.scaleX(scale);
            dom.setAttribute("data-scale", scale);
        },
        flipY() {
            const dom = this.$refs.flipY;
            let scale = dom.getAttribute("data-scale");
            scale = scale ? -scale : -1;
            this.$refs.cropper.scaleY(scale);
            dom.setAttribute("data-scale", scale);
        },
        getCropBoxData() {
            this.data = JSON.stringify(
                this.$refs.cropper.getCropBoxData(),
                null,
                4
            );
        },
        getData() {
            this.data = JSON.stringify(this.$refs.cropper.getData(), null, 4);
        },
        move(offsetX, offsetY) {
            this.$refs.cropper.move(offsetX, offsetY);
        },
        reset() {
            this.$refs.cropper.reset();
        },
        rotate(deg) {
            this.$refs.cropper.rotate(deg);
        },
        setCropBoxData() {
            if (!this.data) return;

            this.$refs.cropper.setCropBoxData(JSON.parse(this.data));
        },
        setData() {
            if (!this.data) return;

            this.$refs.cropper.setData(JSON.parse(this.data));
        },
        // setImage(e) {
        //     const file = e.target.files[0];

        //     if (file.type.indexOf("image/") === -1) {
        //         alert(this.("Please select an image file"));
        //         return;
        //     }

        //     if (typeof FileReader === "function") {
        //         const reader = new FileReader();

        //         reader.onload = (event) => {
        //             this.imgSrc = event.target.result;
        //             // rebuild cropperjs with the updated source
        //             this.$refs.cropper.replace(event.target.result);
        //         };

        //         reader.readAsDataURL(file);
        //     } else {
        //         alert(this.("Sorry, FileReader API not supported"));
        //     }
        // },
        // showFileChooser() {
        //     this.$refs.input.click();
        // },
        zoom(percent) {
            this.$refs.cropper.relativeZoom(percent);
        },
    },
};
</script>
<style scoped>
/* body {
  font-family: Arial, Helvetica, sans-serif;
  width: 1024px;
  margin: 0 auto;
} */

input[type="file"] {
    display: none;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0 5px 0;
}

.header h2 {
    margin: 0;
}

.header a {
    text-decoration: none;
    color: black;
}

.content {
    display: flex;
    justify-content: space-between;
}
.actions {
    margin-top: 1rem;
}

.actions a,
button {
    /* display: inline-block;
    padding: 5px 15px;
    background: #0062CC;
    color: white;
    text-decoration: none;
    border-radius: 3px; */
    margin-right: 1rem;
    margin-bottom: 1rem;
}

textarea {
    width: 100%;
    height: 100px;
}

.preview-area p {
    font-size: 1.25rem;
    margin: 0;
    margin-bottom: 1rem;
}

.preview-area p:last-of-type {
    margin-top: 1rem;
}

.preview {
    width: 100%;
    height: calc(372px * (9 / 16));
    overflow: hidden;
}

.crop-placeholder {
    width: 100%;
    height: 200px;
    background: #ccc;
}

.cropped-image img {
    max-width: 100%;
}
.offcanvas {
    width: auto; /* Increase the width */
    height: 400px; /* Increase the height */
}
.btn-outline-success:hover {
    color: white !important;
}

.btn-zoom-in {
    font-weight: bold;
}
.btn-zoom-out {
    font-weight: bold;
}
</style>
