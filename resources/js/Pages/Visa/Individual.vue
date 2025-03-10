<template>
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1 class="theme-text-color">{{ country }}</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="dashboard">TTC Global</a>
                        </li>
                        <li class="breadcrumb-item">{{ country }}</li>
                        <li class="breadcrumb-item active">Index</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button
                    class="btn btn-success mt-3"
                    data-bs-toggle="modal"
                    data-bs-target="#visarecordmodal"
                    @click="clearFields"
                >
                    <i class="bi bi-plus-lg"></i> Add New Record
                </button>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title theme-text-color">
                        Individual Records
                    </h5>

                    

                    <!-- Table -->
                    <table  id="ledgerTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone #</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Tracking ID</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(visarecord, index) in VisasRecords"
                                :key="visarecord.id"
                            >
                                <td>{{ index + 1 }}</td>
                                <td>
                                    <a
                                        href="#"
                                        class="theme-text-color"
                                        @click="openModal(visarecord)"
                                    >
                                        {{ visarecord.full_name }}
                                    </a>
                                </td>
                                <td>{{ visarecord.phone_number }}</td>
                                <td>{{ visarecord.status }}</td>
                                <td>{{ visarecord.amount }}</td>
                                <td>{{ visarecord.tracking_id }}</td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#visarecordmodal"
                                        @click="editRecord(visarecord)"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bootstrap Modal -->
            <div
                class="modal fade"
                id="visaModal"
                tabindex="-1"
                aria-labelledby="modalTitle"
                aria-hidden="true"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitle">
                                {{ selectedVisa.full_name }}
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th scope="row">Phone Number</th>
                                        <td>{{ selectedVisa.phone_number }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Status</th>
                                        <td>{{ selectedVisa.status }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Amount</th>
                                        <td>{{ selectedVisa.amount }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Visa Fee</th>
                                        <td>{{ selectedVisa.visa_fee }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Tracking ID</th>
                                        <td>{{ selectedVisa.tracking_id }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Email</th>
                                        <td>{{ selectedVisa.gmail }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Gender</th>
                                        <td>{{ selectedVisa.gender }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Date</th>
                                        <td>{{ selectedVisa.date }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Pak Visa Password</th>
                                        <td>
                                            {{ selectedVisa.pak_visa_password }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Gmail Password</th>
                                        <td>
                                            {{ selectedVisa.gmail_password }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Referral</th>
                                        <td>
                                            {{ selectedVisa.referral_name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Referral Commission</th>
                                        <td>
                                            {{
                                                selectedVisa.referral_commission
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="modal fade"
                id="visarecordmodal"
                tabindex="-1"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="section-title mt-1">
                                <h5 class="c-theme-red">
                                    Visas Records Registration form
                                </h5>
                            </div>
                            <button
                                ref="closeThisModal"
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <!-- Tab Content -->
                            <div class="tab-content mt-3">
                                <!-- Single Form Tab -->

                                <div>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label">{{
                                                "Date"
                                            }}</label>
                                            <input
                                                type="date"
                                                class="form-control"
                                                v-model="individualForm.date"
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.date,
                                                }"
                                            />
                                            <div
                                                v-if="individualFormErrors.date"
                                                class="invalid-feedback"
                                            >
                                                {{ individualFormErrors.date }}
                                            </div>
                                        </div>

                                        <!-- Full Name -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_full_name"
                                                class="form-label"
                                                >Full Name</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    individualForm.full_name
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.full_name,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.full_name
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.full_name
                                                }}
                                            </div>
                                        </div>

                                        <!-- Phone Number -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_phone_number"
                                                class="form-label"
                                                >Phone Number</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    individualForm.phone_number
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.phone_number,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.phone_number
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.phone_number
                                                }}
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_status"
                                                class="form-label"
                                                >Status</label
                                            >

                                            <Multiselect
                                                v-model="individualForm.status"
                                                :options="statusOptions"
                                                :searchable="true"
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.status,
                                                }"
                                            />

                                            <div
                                                v-if="
                                                    individualFormErrors.status
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.status
                                                }}
                                            </div>
                                        </div>

                                        <!-- Amount -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_amount"
                                                class="form-label"
                                                >Amount</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="individualForm.amount"
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.amount,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.amount
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.amount
                                                }}
                                            </div>
                                        </div>

                                        <!-- Tracking ID -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_tracking_id"
                                                class="form-label"
                                                >Tracking ID</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    individualForm.tracking_id
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.tracking_id,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.tracking_id
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.tracking_id
                                                }}
                                            </div>
                                        </div>
                                        <!-- Visa Fee -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="visa_fee"
                                                class="form-label"
                                                >Visa Fee</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    individualForm.visa_fee
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.visa_fee,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.visa_fee
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.visa_fee
                                                }}
                                            </div>
                                        </div>

                                        <!-- Gmail -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_gmail"
                                                class="form-label"
                                                >Gmail</label
                                            >
                                            <input
                                                type="email"
                                                class="form-control"
                                                v-model="individualForm.gmail"
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.gmail,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.gmail
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{ individualFormErrors.gmail }}
                                            </div>
                                        </div>

                                        <!-- Pak Visa Password -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_pak_visa_password"
                                                class="form-label"
                                                >Pak Visa Password</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    individualForm.pak_visa_password
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.pak_visa_password,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.pak_visa_password
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.pak_visa_password
                                                }}
                                            </div>
                                        </div>

                                        <!-- Gmail Password -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_gmail_password"
                                                class="form-label"
                                                >Gmail Password</label
                                            >
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="
                                                    individualForm.gmail_password
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.gmail_password,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    individualFormErrors.gmail_password
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.gmail_password
                                                }}
                                            </div>
                                        </div>

                                        <!-- Gender -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="individual_gender"
                                                class="form-label"
                                                >Gender</label
                                            >
                                            <Multiselect
                                                v-model="individualForm.gender"
                                                :options="genderOptions"
                                                :searchable="true"
                                                :class="{
                                                    'invalid-bg':
                                                        individualFormErrors.gender,
                                                }"
                                            />

                                            <div
                                                v-if="
                                                    individualFormErrors.gender
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.gender
                                                }}
                                            </div>
                                        </div>

                                        <!-- referral -->
                                        <div class="col-md-6 col-12">
                                            <label
                                                for="referral"
                                                class="form-label"
                                                >Referrals</label
                                            >
                                            <Multiselect
                                                v-model="
                                                    individualForm.referral
                                                "
                                                :options="pluckedReferrals"
                                                :searchable="true"
                                                :class="{
                                                    'invalid-bg':
                                                        individualFormErrors.referral,
                                                }"
                                            />

                                            <div
                                                v-if="
                                                    individualFormErrors.referral
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.referral
                                                }}
                                            </div>
                                        </div>
                                        <div
                                            class="col-md-6 col-12"
                                            v-if="individualForm.referral"
                                        >
                                            <label
                                                for="referral"
                                                class="form-label"
                                                >Referral's Commission</label
                                            >
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-model="
                                                    individualForm.referral_commission
                                                "
                                                :class="{
                                                    'is-invalid':
                                                        individualFormErrors.referral_commission,
                                                }"
                                            />

                                            <div
                                                v-if="
                                                    individualFormErrors.referral_commission
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    individualFormErrors.referral_commission
                                                }}
                                            </div>
                                        </div>
                                        <!-- employee -->
                                         
                                    </div>
                                    <!-- Submit Button -->
                                    <button
                                        type="button"
                                        class="btn btn-success mt-3"
                                        @click="submitIndividualForm"
                                        :disabled="individualFormStatus === 0"
                                    >
                                        {{
                                            individualFormStatus === 1
                                                ? "Save"
                                                : "Saving..."
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>

<script>
import Master from "../Layout/Master.vue";
import Multiselect from "@vueform/multiselect";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import axios from "axios";

export default {
    layout: Master,
    components: {
        Multiselect,
        Datepicker,
    },

    props: ["country"],
    created() {
        this.fetchRecords();
        this.referralsPluck();
        this.employeesPluck();
    },
    data() {
        return {
            dataTable: null,
            selectedVisa: null,
            selectedVisa: {},
            pluckedReferrals: [],
            pluckedEmployees: [],
            tab: "single",
            individualForm: {
                id: "",
                full_name: "",
                phone_number: "",
                status: "",
                amount: "",
                tracking_id: "",
                visa_fee: "",
                gmail: "",
                pak_visa_password: "",
                gmail_password: "",
                gender: "",
                date: "",
                family_members: 1,
                referral: "",
                referral_commission: "",
                employee: [],
            },

            individualFormErrors: {},

            formErrors: {},
            individualFormStatus: 1, // or 0 (1 = active, 0 = inactive)

            statusOptions: [
                "Initial",
                "Applied",
                "Cancel",
                "Rejected",
                "Approved",
            ], // Status options
            genderOptions: ["Male", "Female", "Other"],
            VisasRecords: [],
            filters: {
                full_name: "",
                tracking_id: "",
                gmail: "",
                phone_number: "",
            },
        };
    },
    
     
     
    methods: {
        openModal(visa) {
            this.selectedVisa = visa;
            const modal = new bootstrap.Modal(
                document.getElementById("visaModal")
            );
            modal.show();
        },
        editRecord(visarecord) {
            this.individualForm = { ...visarecord };
        },
        // openModal(visa) {
        //     this.selectedVisa = visa;
        // },
        closeModal() {
            this.$refs.closeThisModal.click();
            this.fetchRecords();
            // this.selectedVisa = null;
        },
        editVisa(visa) {
            console.log("Edit visa:", visa);
            // Implement edit logic here
        },
        deleteThis(id) {
            axios
                .delete(route("api.customer.visa.record.delete", id))
                .then(() => {
                    this.fetchRecords();
                    toastr.success("Transaction entry deleted successfully.");
                })
                .catch((error) => {
                    console.error(error);
                });
        },

        submitIndividualForm() {
            this.validateIndividualForm();
            if (Object.keys(this.individualFormErrors).length > 0) {
                return; // Stop if there are errors
            }

            this.individualFormStatus = 0; // Mark as saving

            axios
                .post(
                    route("api.individual.customer.store"),
                    this.individualForm,
                    {
                        headers: {
                            Authorization:
                                "Bearer " + this.$page.props.auth_token,
                        },
                    }
                )
                .then(() => {
                    toastr.success("Record saved successfully.");
                    this.closeModal();

                    this.individualFormStatus = 1;
                })
                .catch((error) => {
                    this.individualFormStatus = 1;
                    toastr.error(error.response.data.message);

                    if (error.response.data.errors) {
                        this.formErrors = error.response.data.errors;
                    }
                });
        },

        validateIndividualForm() {
            this.individualFormErrors = {}; // Reset errors

            let fields = [
                "full_name",
                "phone_number",
                "status",
                "amount",
                "tracking_id",
                "gmail",
                "pak_visa_password",
                "gmail_password",
                "gender",
                "date",
            ];

            fields.forEach((field) => {
                if (!this.individualForm[field]) {
                    this.individualFormErrors[field] = `${field.replace(
                        /_/g,
                        " "
                    )} is required`;
                }
            });
        },

        fetchRecords() {
            axios
                .get(route("api.individual.customer.fetch"), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.VisasRecords = response.data;
                    this.$nextTick(() => {
                        this.initializeDataTable();
                    });
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },initializeDataTable() {
            if (this.dataTable) {
                this.dataTable.destroy(); // Destroy previous instance if it exists
            }
            this.$nextTick(() => {
                this.dataTable = $("#ledgerTable").DataTable({
                    responsive: true,
                    autoWidth: false,
                    paging: true,
                    searching: true,
                    ordering: true,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous",
                        },
                    },
                });
            });
        }
    ,
        referralsPluck() {
            axios
                .get(route("api.referrals.pluck"), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.pluckedReferrals = response.data;
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },
        employeesPluck() {
            axios
                .get(route("api.employees.pluck"), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.pluckedEmployees = response.data;
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },
    },
};
</script>

<style lang="scss">
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
.border {
    border-radius: 0.25rem;
    border-color: #3c444d !important;
}
</style>
