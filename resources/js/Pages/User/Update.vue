<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1>{{ translate("Edit User") }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <Link :href="route('dashboard')">{{
                                $page.props.website_name
                            }}</Link>
                        </li>
                        <li class="breadcrumb-item">
                            <Link :href="route('users')">
                                {{ translate("Users") }}
                            </Link>
                        </li>
                        <li class="breadcrumb-item">
                            <Link :href="route('users')">
                                {{ form.user_name }}
                            </Link>
                             
                        </li>
                        <li class="breadcrumb-item active">
                            {{ translate("Edit") }}
                        </li>
                    </ol>
                </nav>
            </div>
            <Link class="btn btn-success" :href="route('users')">{{
                translate("Go to Users")
            }}</Link>
        </div>

        <!-- End Page Title -->
        <section class="section">
            <div class="card">
                <div class="card-body pt-4">
                    <!-- <h5 class="card-title">Add New Client</h5> -->

                    <!-- Floating Labels Form -->
                    <form @submit.prevent="submit">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="user_name">{{
                                    translate("User name")
                                }}</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="user_name"
                                    :class="{
                                        'invalid-bg': formErrors.user_name,
                                    }"
                                    v-model="form.user_name"
                                />

                                <div
                                    class="invalid-feedback animated fadeIn"
                                    v-if="formErrors.user_name"
                                >
                                    {{ formErrors.user_name[0] }}
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email">{{
                                    translate("User email")
                                }}</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    :class="{ 'invalid-bg': formErrors.email }"
                                    v-model="form.email"
                                />

                                <div
                                    class="invalid-feedback animated fadeIn"
                                    v-if="formErrors.email"
                                >
                                    {{ formErrors.email[0] }}
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label>{{ translate("Role") }} </label>
                                <Multiselect
                                    id=""
                                    v-model="form.role"
                                    :options="RoleOptions"
                                    :searchable="true"
                                    :class="{
                                        'invalid-bg': formErrors.role,
                                    }"
                                />
                                <div
                                    class="invalid-feedback animated fadeIn"
                                    v-if="formErrors.role"
                                >
                                    {{ formErrors.role[0] }}
                                </div>
                            </div>

                            <!-- <div class="col-12 col-md-6">
                <label for="password">{{ translate("User password") }}</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  :class="{ 'invalid-bg': formErrors.password }"
                  v-model="form.password"
                />

                <div
                  class="invalid-feedback animated fadeIn"
                  v-if="formErrors.password"
                >
                  {{ formErrors.password[0] }}
                </div>
              </div> -->

                            <!-- <div class="col-12 col-md-6">
                <label for="confirm_password">{{ translate("Confirm password") }}</label>
                <input
                  type="password"
                  class="form-control"
                  id="confirm_password"
                  :class="{ 'invalid-bg': formErrors.confirm_password }"
                  v-model="form.confirm_password"
                />

                <div
                  class="invalid-feedback animated fadeIn"
                  v-if="formErrors.confirm_password"
                >
                  {{ formErrors.confirm_password[0] }}
                </div>
              </div> -->
                        </div>

                        <div class="mt-5">
                            <button
                                type="submit"
                                class="btn btn-success"
                                v-if="formStatus == 'Yes'"
                            >
                                {{ translate("Save") }}
                            </button>
                            <button
                                class="btn btn-success"
                                type="button"
                                disabled
                                v-else
                            >
                                {{ translate("Save") }}
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
import Master from "@/Pages/Backend/Layouts/Master";
import Multiselect from "@vueform/multiselect";

export default {
    layout: Master,
    props: ["userId"],

    components: {
        Multiselect,
    },
    created() {
        this.fetchUser();
    },

    data() {
        return {
            form: {
                user_name: "",
                email: "",
            },
            formErrors: [],
            formStatus: "Yes",
            RoleOptions: ["user", "admin", "super admin"],
        };
    },
    methods: {
        fetchUser() {
            axios
                .get(route("api.users.show", this.userId), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.form.user_name = response.data.name;
                    this.form.email = response.data.email;
                    this.form.role = response.data.role;
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },
        submit() {
            this.formStatus = "No";

            let formData = new FormData();
            formData.append("id", this.userId);
            formData.append("user_name", this.form.user_name);
            formData.append("email", this.form.email);
            formData.append("role", this.form.role);

            axios
                .post(route("api.users.update"), formData, {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((data) => {
                    toastr.success(this.translate("User updated successfully."));
                    this.$inertia.get(route("users"));
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);

                    if (error.response.data.errors) {
                        this.formStatus = "Yes";
                        this.formErrors = error.response.data.errors;
                    }
                });
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
</style>
