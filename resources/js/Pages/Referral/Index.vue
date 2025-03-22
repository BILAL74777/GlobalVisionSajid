<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1 class="theme-text-color">Referrals</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="dashboard">TTC Global</a>
                        </li>
                        <li class="breadcrumb-item active">Referrals</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button
                    class="btn btn-success mt-3"
                    data-bs-toggle="modal"
                    data-bs-target="#referralModal"
                    @click="clearFields"
                >
                    <i class="bi bi-plus-lg"></i> Add New Referral
                </button>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title theme-text-color">
                        Referral Records
                    </h5>

                    <!-- Table with stripped rows -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Commission</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Address</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(referral, index) in referrals" :key="referral.id">
                                <th scope="row">{{ index + 1 }}</th>
                                <td>
                                    <a
                                        class="theme-text-color"
                                        :href="
                                            route(
                                                'referral.details',
                                                referral.id
                                            )
                                        "
                                    >
                                        {{ referral.name }}
                                    </a>
                                     
                                </td>
                                <td>{{ referral.email }}</td>
                                <td>{{ referral.commission }}%</td>
                                <td>{{ referral.phone || 'N/A' }}</td>
                                <td>{{ referral.address || 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" @click="editReferral(referral)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <!-- <DeleteModal :deleteId="referral.id" @deleteThis="deleteReferral"></DeleteModal> -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Referral Modal -->
            <div class="modal fade" id="referralModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Referral Registration</h5>
                            <button type="button" ref="closeModal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="submitReferral">
                                <div class="row g-3">
                                    <!-- Name -->
                                    <div class="col-12">
                                        <label for="name" class="form-label">Name<i class="text-danger">*</i></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="referralForm.name"
                                            :class="{ 'is-invalid': referralErrors.name }"
                                        />
                                        <div v-if="referralErrors.name" class="invalid-feedback">
                                            {{ referralErrors.name }}
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label"
                                            >Commission<i class="text-danger">*</i></label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="referralForm.commission"
                                            :class="{
                                                'is-invalid':
                                                    referralErrors.commission,
                                            }"
                                        />
                                        <div
                                            v-if="referralErrors.commission"
                                            class="invalid-feedback"
                                        >
                                            {{ referralErrors.commission }}
                                        </div>
                                    </div>

                                     <!-- Phone (Optional) -->
                                     <div class="col-12">
                                        <label for="phone" class="form-label">Phone<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" v-model="referralForm.phone" :class="{ 'is-invalid': referralErrors.phone }"/>
                                        <div v-if="referralErrors.phone" class="invalid-feedback">
                                            {{ referralErrors.phone }}
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email <i class="text-danger">*</i></label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            v-model="referralForm.email"
                                            
                                        />
                                        <div v-if="referralErrors.email" class="invalid-feedback">
                                            {{ referralErrors.email }}
                                        </div>
                                       
                                    </div>

                                   
 
                                    <!-- Address (Optional) -->
                                    <div class="col-12">
                                        <label for="address" class="form-label">Address (Optional)</label>
                                        <input type="text" class="form-control" v-model="referralForm.address" />
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-success mt-3" :disabled="referralStatus === 0">
                                    {{ referralStatus === 1 ? "Save Referral" : "Saving..." }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>

<script>
import axios from "axios";
import Master from "../Layout/Master.vue";

export default {
    layout: Master,
    props: ["country"],
    data() {
        return {
            referrals: [],
            referralForm: {
                name: "",
                email: "",
                phone: "",
                address: "",
                id: "",
                commission: "",
            },
            referralErrors: {},
            referralStatus: 1,
        };
    },
    created() {
        this.fetchReferrals();
    },
    methods: {
        openModal(referral = null) {
        if (referral) {
            this.referralForm = { ...referral }; // Load data for editing
        } else {
            this.clearFields(); // Reset form for a new entry
        }
        
        // Open Bootstrap modal
        const modalElement = document.getElementById("referralModal");
        const modalInstance = new bootstrap.Modal(modalElement);
        modalInstance.show();
    },
    clearFields() {
        this.referralForm = { name: "", email: "", phone: "", address: "", id: null ,commission:""};
        this.referralErrors = {};
    },
    editReferral(referral) {
        this.openModal(referral); // Use openModal instead
    },
    fetchReferrals() {
        axios
            .get(route("api.referrals.fetch"), {
                headers: { Authorization: "Bearer " + this.$page.props.auth_token },
            })
            .then((response) => {
                this.referrals = response.data;
            })
            .catch((error) => {
                console.error(error);
            });
    },
    validateReferralForm() {
        this.referralErrors = {};
        if (!this.referralForm.name) this.referralErrors.name = "Name is required.";
        if (!this.referralForm.email) this.referralErrors.email = "Email is required.";
        if (!this.referralForm.phone) this.referralErrors.phone = "Phone Number is required.";
        if (!this.referralForm.commission) this.referralErrors.commission = "Commission is required.";
    },
    submitReferral() {
        this.validateReferralForm();
        if (Object.keys(this.referralErrors).length > 0) return;

        this.referralStatus = 0;
        const url = this.referralForm.id
            ? route("api.referrals.store") // Update existing referral
            : route("api.referrals.store"); // Create new referral

        const method = "post";

        axios({
            method,
            url,
            data: this.referralForm,
            headers: { Authorization: "Bearer " + this.$page.props.auth_token },
        })
            .then(() => {
                toastr.success("Referral saved successfully.");
                this.fetchReferrals();
                this.referralStatus = 1;
                this.clearFields();
                this.$refs.closeModal.click();
            })
            .catch((error) => {
                this.referralStatus = 1;
                toastr.error(error.response.data.message);
                if (error.response.data.errors) {
                    this.referralErrors = error.response.data.errors;
                }
            });
    },
    deleteReferral(id) {
        axios
            .delete(route("api.referrals.delete", id))
            .then(() => {
                toastr.success("Referral deleted successfully.");
                this.fetchReferrals();
            })
            .catch((error) => {
                console.error(error);
            });
    },
},

};
</script>

<style lang="scss">
.invalid-feedback {
    display: block !important;
}
</style>
