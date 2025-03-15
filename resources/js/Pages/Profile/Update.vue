<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1>{{ "Update Profile" }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <Link :href="route('dashboard')">{{
                                $page.props.website_name
                            }}</Link>
                        </li>
                        <li class="breadcrumb-item">
                            {{ "Profile" }}
                        </li>
                        <li class="breadcrumb-item active">
                            {{ "Update Profile" }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- End Page Title -->
        <section class="section">
            <div class="card">
                <div class="card-body pt-4">
                    <!-- <h5 class="card-title">Add New Client</h5> -->

                    <!-- Floating Labels Form -->
                    <form @submit.prevent="submit" autocomplete="off">
                        <!-- Fake fields -->
                        <input
                            type="text"
                            name="fakeuser"
                            style="display: none"
                        />
                        <input
                            type="password"
                            name="fakepass"
                            style="display: none"
                        />

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="first_name">{{
                                    "First name"
                                }}</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="first_name"
                                    autocomplete="off"
                                    :class="{
                                        'invalid-bg': formErrors.first_name,
                                    }"
                                    v-model="form.first_name"
                                />

                                <div
                                    class="invalid-feedback animated fadeIn"
                                    v-if="formErrors.first_name"
                                >
                                    {{ formErrors.first_name[0] }}
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="last_name">{{ "Last name" }}</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="last_name"
                                    autocomplete="off"
                                    :class="{
                                        'invalid-bg': formErrors.last_name,
                                    }"
                                    v-model="form.last_name"
                                />

                                <div
                                    class="invalid-feedback animated fadeIn"
                                    v-if="formErrors.last_name"
                                >
                                    {{ formErrors.last_name[0] }}
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email">{{ "Email" }}</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    :class="{ 'invalid-bg': formErrors.email }"
                                    v-model="form.email"
                                    autocomplete="off"
                                    readonly
                                />

                                <div
                                    class="invalid-feedback animated fadeIn"
                                    v-if="formErrors.email"
                                >
                                    {{ formErrors.email[0] }}
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="current_password">{{
                                    "Old Password"
                                }}</label>
                                <div class="input-group">
                                    <input
                                        :type="
                                            oldPasswordVisible
                                                ? 'text'
                                                : 'password'
                                        "
                                        class="form-control"
                                        autocomplete="new-password"
                                        :class="{
                                            'invalid-bg':
                                                formErrors.current_password,
                                        }"
                                        v-model="form.current_password"
                                    />
                                    <span
                                        class="input-group-text"
                                        @click="toggleOldPasswordVisibility"
                                    >
                                        <i
                                            :class="
                                                toggleOldPasswordVisibility
                                                    ? 'bi bi-eye-slash'
                                                    : 'bi bi-eye'
                                            "
                                        ></i>
                                    </span>
                                </div>
                                <div
                                    class="invalid-feedback animated fadeIn"
                                    v-if="formErrors.current_password"
                                >
                                    {{ formErrors.current_password[0] }}
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="new_password">{{
                                    "New Password"
                                }}</label>
                                <div class="input-group">
                                    <input
                                        :type="
                                            passwordVisible
                                                ? 'text'
                                                : 'password'
                                        "
                                        class="form-control"
                                        :class="{
                                            'invalid-bg':
                                                formErrors.new_password,
                                        }"
                                        v-model="form.new_password"
                                        autocomplete="off"
                                    />
                                    <span
                                        class="input-group-text"
                                        @click="togglePasswordVisibility"
                                    >
                                        <i
                                            :class="
                                                passwordVisible
                                                    ? 'bi bi-eye-slash'
                                                    : 'bi bi-eye'
                                            "
                                        ></i>
                                    </span>
                                </div>
                                <div
                                    class="invalid-feedback animated fadeIn"
                                    v-if="formErrors.new_password"
                                >
                                    {{ formErrors.new_password[0] }}
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="confirm_password">{{
                                    "Confirm Password"
                                }}</label>
                                <div class="input-group">
                                    <input
                                        :type="
                                            confirmPasswordVisible
                                                ? 'text'
                                                : 'password'
                                        "
                                        class="form-control"
                                        id="confirm_password"
                                        :class="{
                                            'invalid-bg':
                                                formErrors.confirm_password,
                                        }"
                                        v-model="form.confirm_password"
                                        autocomplete="off"
                                    />
                                    <span
                                        class="input-group-text"
                                        @click="toggleConfirmPasswordVisibility"
                                    >
                                        <i
                                            :class="
                                                confirmPasswordVisible
                                                    ? 'bi bi-eye-slash'
                                                    : 'bi bi-eye'
                                            "
                                        ></i>
                                    </span>
                                </div>

                                <div
                                    class="invalid-feedback animated fadeIn"
                                    v-if="formErrors.confirm_password"
                                >
                                    {{ formErrors.confirm_password[0] }}
                                </div>
                            </div>

                            <!-- <div class="col-12 col-md-6 mt-3">
                                <div class="c-files">
                                    <span class="col-6 col-form-label mt-1">{{
                                        "Image"
                                    }}</span>
                                    <br />

                                    <ImageZooming
                                        v-if="form.image"
                                        :file="form.image"
                                        :width="120"
                                    />
                                    <ImageZooming
                                        v-else
                                        :file="existing_image"
                                        :width="120"
                                    />
                                    <br />
                                    <Cropper
                                        @croppedImg="croppedImgSubmit"
                                        :ratio="1"
                                    />
                                </div>
                                <div
                                    class="invalid-feedback animated fadeIn"
                                    v-if="formErrors.image"
                                >
                                    {{ formErrors.image[0] }}
                                </div>
                            </div> -->
                        </div>
                        <div class="mt-5">
                            <button
                                type="submit"
                                class="btn btn-success"
                                v-if="formStatus == 'Yes'"
                            >
                                {{ "Save" }}
                            </button>
                            <button
                                class="btn btn-success"
                                type="button"
                                disabled
                                v-else
                            >
                                {{ "Save" }}
                                <span
                                    class="spinner-border spinner-border-sm"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                            </button>
                        </div>
                    </form>
                    <!-- End floating Labels Form -->
                </div>
            </div>
        </section>
    </main>
    <!-- End #main -->
</template>

<script>
import Master from "../Layout/Master.vue";
import axios from "axios";
import Multiselect from "@vueform/multiselect";

export default {
    layout: Master,
    props: ["userId"],
    created() { 
        this.fetchCustomerRecord();
    },

    components: {
        Multiselect,
    },
    data() {
        return {
            form: {
                first_name: "",
                last_name: "",
                email: "",
                current_password: "",
                new_password: "",
                confirm_password: "",
                // image: "",
            },
            // existing_image: "",
            formErrors: [],
            formStatus: "Yes",
            oldPasswordVisible: false,
            passwordVisible: false,
            confirmPasswordVisible: false,
        };
    },
    methods: {
        fetchCustomerRecord() {
            axios
                .get(route("api.profile.show", this.userId), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.form.first_name = response.data.first_name;
                    this.form.last_name = response.data.last_name;
                    this.form.email = response.data.email;
                    this.existing_image = response.data.image;
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },

        submit() {
            this.formStatus = "No";
            let formData = new FormData();
            formData.append("id", this.userId);
            formData.append("first_name", this.form.first_name);
            formData.append("last_name", this.form.last_name);
            formData.append("email", this.form.email);
            formData.append("new_password", this.form.new_password);
            formData.append("current_password", this.form.current_password);
            formData.append("confirm_password", this.form.confirm_password);
            formData.append("image", this.form.image);

            axios
                .post(route("api.profile.update"), formData, {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((data) => {
                    toastr.success("Profile updated successfully");
                    this.$inertia.get(route("login"));
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);

                    if (error.response.data.errors) {
                        this.formStatus = "Yes";
                        this.formErrors = error.response.data.errors;
                    }
                });
        },

        croppedImgSubmit(img) {
            this.form.image = img;
        },

        toggleOldPasswordVisibility() {
            this.oldPasswordVisible = !this.oldPasswordVisible;
        },
        togglePasswordVisibility() {
            this.passwordVisible = !this.passwordVisible;
        },
        toggleConfirmPasswordVisibility() {
            this.confirmPasswordVisible = !this.confirmPasswordVisible;
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
.invalid-bg {
    border-color: #f8d4d4 !important;
    background-color: #f8d4d4 !important;
}
</style>
