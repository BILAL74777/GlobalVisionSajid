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
                        <li class="breadcrumb-item active">Family Visas</li>
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
                    <h5 class="card-title theme-text-color">Family Records</h5>
                    <div>
                        <!-- Search Input -->
                        <input
                            type="text"
                            v-model="searchQuery"
                            class="form-control mb-3"
                            placeholder="Search by Family Name, Phone Number, or Date..."
                        />

                        <div class="accordion" id="visaAccordion">
                            <div
                                class="accordion-item"
                                v-for="(visarecord, index) in filteredRecords"
                                :key="visarecord.id"
                            >
                                <!-- Accordion Header -->
                                <h2 class="accordion-header">
                                    <button
                                        class="accordion-button"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        :data-bs-target="
                                            '#collapse' + visarecord.id
                                        "
                                        aria-expanded="true"
                                        :aria-controls="
                                            'collapse' + visarecord.id
                                        "
                                    >
                                        <strong>
                                            {{ index + 1 }} -
                                            {{
                                                visarecord.family_name || "N/A"
                                            }}
                                            -
                                            {{
                                                visarecord.phone_number || "N/A"
                                            }}
                                            ({{ visarecord.date }})
                                        </strong>
                                    </button>
                                </h2>

                                <!-- Accordion Body -->
                                <div
                                    :id="'collapse' + visarecord.id"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#visaAccordion"
                                >
                                    <div class="accordion-body">
                                        <table class="table table-striped">
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
                                                <!-- Primary Visa Record -->
                                                <tr>
                                                    <th>1</th>
                                                    <td>
                                                        <a
                                                            href="#"
                                                            class="theme-text-color"
                                                            @click="
                                                                openModal(
                                                                    visarecord
                                                                )
                                                            "
                                                        >
                                                            {{
                                                                visarecord.full_name
                                                            }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {{
                                                            visarecord.phone_number
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{ visarecord.status }}
                                                    </td>
                                                    <td>
                                                        {{ visarecord.amount }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            visarecord.tracking_id
                                                        }}
                                                    </td>
                                                    <td>
                                                        <button
                                                            class="btn btn-sm btn-warning me-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editRecordModalForm"
                                                            @click="
                                                                editRecord(
                                                                    visarecord
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="bi bi-pencil"
                                                            ></i>
                                                        </button>
                                                    </td>
                                                </tr>

                                                <!-- Family Members -->
                                                <tr
                                                    v-for="(
                                                        member, memberIndex
                                                    ) in visarecord.family_members"
                                                    :key="member.id"
                                                >
                                                    <th>
                                                        {{ memberIndex + 2 }}
                                                    </th>
                                                    <td>
                                                        <a
                                                            href="#"
                                                            class="theme-text-color"
                                                            @click.prevent="
                                                                openModal(
                                                                    member
                                                                )
                                                            "
                                                        >
                                                            {{
                                                                member.full_name
                                                            }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {{
                                                            member.phone_number
                                                        }}
                                                    </td>
                                                    <td>{{ member.status }}</td>
                                                    <td>{{ member.amount }}</td>
                                                    <td>
                                                        {{ member.tracking_id }}
                                                    </td>
                                                    <td>
                                                        <button
                                                            class="btn btn-sm btn-warning me-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editRecordModalForm"
                                                            @click="
                                                                editRecord(
                                                                    member
                                                                )
                                                            "
                                                        >
                                                            <i
                                                                class="bi bi-pencil"
                                                            ></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bootstrap Modal -->
                    <div
                        class="modal fade"
                        id="visaModal"
                        tabindex="-1"
                        aria-labelledby="visaModalLabel"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        {{ selectedVisa.full_name }}
                                    </h5>
                                    <button
                                        ref="closeThisModal"
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
                                                <th scope="row">
                                                    Phone Number
                                                </th>
                                                <td>
                                                    {{
                                                        selectedVisa.phone_number
                                                    }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Status</th>
                                                <td>
                                                    {{ selectedVisa.status }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Amount</th>
                                                <td>
                                                    {{ selectedVisa.amount }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Visa Fee</th>
                                                <td>
                                                    {{ selectedVisa.visa_fee }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Tracking ID</th>
                                                <td>
                                                    {{
                                                        selectedVisa.tracking_id
                                                    }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Email</th>
                                                <td>
                                                    {{ selectedVisa.gmail }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Gender</th>
                                                <td>
                                                    {{ selectedVisa.gender }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Date</th>
                                                <td>{{ selectedVisa.date }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                    Pak Visa Password
                                                </th>
                                                <td>
                                                    {{
                                                        selectedVisa.pak_visa_password
                                                    }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                    Gmail Password
                                                </th>
                                                <td>
                                                    {{
                                                        selectedVisa.gmail_password
                                                    }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Bootstrap Modal -->
                </div>
            </div>

            <div
                class="modal fade"
                id="visarecordmodal"
                tabindex="-1"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="section-title mt-1">
                                <h5 class="c-theme-red">
                                    Visas Records Registration form
                                </h5>
                            </div>
                            <button
                                ref="closeVisaFamilyModal"
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <!-- Number of Family Members -->
                            <div class="row g-3">
                                <!-- referral -->
                                <div class="col-md-6 col-12">
                                    <label for="referral"
                                        >Referral if Any (Optional)</label
                                    >
                                    <Multiselect
                                        v-model="familyForm.referral"
                                        :options="pluckedReferrals"
                                        :searchable="true"
                                        :class="{
                                            'invalid-bg':
                                                familyFormErrors.referral,
                                        }"
                                    />

                                    <div
                                        v-if="familyFormErrors.referral"
                                        class="invalid-feedback"
                                    >
                                        {{ familyFormErrors.referral }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label
                                        >{{ "Family Name"
                                        }}<i class="text-danger">*</i></label
                                    >

                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="familyForm.family_name"
                                        :class="{
                                            'invalid-bg':
                                                familyFormErrors.family_name,
                                        }"
                                    />

                                    <div
                                        class="invalid-feedback d-block"
                                        v-if="familyFormErrors.family_name"
                                    >
                                        {{ familyFormErrors.family_name[0] }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label
                                        >{{ "Phone Number"
                                        }}<i class="text-danger">*</i></label
                                    >

                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="familyForm.phone_number"
                                        :class="{
                                            'invalid-bg':
                                                familyFormErrors.phone_number,
                                        }"
                                    />

                                    <div
                                        class="invalid-feedback d-block"
                                        v-if="familyFormErrors.phone_number"
                                    >
                                        {{ familyFormErrors.phone_number[0] }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label
                                        >{{ "Number of Family Members"
                                        }}<i class="text-danger">*</i></label
                                    >

                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="familyForm.family_members"
                                        :class="{
                                            'invalid-bg':
                                                familyFormErrors.family_members,
                                        }"
                                    />

                                    <div
                                        class="invalid-feedback d-block"
                                        v-if="familyFormErrors.family_members"
                                    >
                                        {{ familyFormErrors.family_members[0] }}
                                    </div>
                                </div>
                            </div>

                            <!-- Family Members Loop -->
                            <div
                                v-for="(
                                    member, index
                                ) in familyForm.family_forms"
                                :key="index"
                            >
                                <div class="row g-3 p-3 mt-3 border">
                                    <b>
                                        {{ "Family Member" }}
                                        {{ index + 1 }}
                                    </b>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label"
                                            >{{ "Date"
                                            }}<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="date"
                                            :max="today"
                                            id="date"
                                            class="form-control"
                                            v-model="member.date"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_date`
                                                    ],
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_date`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_date`
                                                ][0]
                                            }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label"
                                            >{{ "Full Name"
                                            }}<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="member.full_name"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_full_name`
                                                    ],
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_full_name`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_full_name`
                                                ][0]
                                            }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label"
                                            >{{ "Phone Number"
                                            }}<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model="member.phone_number"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_phone_number`
                                                    ],
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_phone_number`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_phone_number`
                                                ][0]
                                            }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label"
                                            >{{ "Status"
                                            }}<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <Multiselect
                                            v-model="member.status"
                                            :options="statusOptions"
                                            :searchable="true"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_status`
                                                    ],
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_status`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_status`
                                                ][0]
                                            }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label"
                                            >{{ "Amount"
                                            }}<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model="member.amount"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_amount`
                                                    ],
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_amount`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_amount`
                                                ][0]
                                            }}
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label"
                                            >{{ "Visa fee"
                                            }}<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model="member.visa_fee"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_visa_fee`
                                                    ],
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_visa_fee`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_visa_fee`
                                                ][0]
                                            }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label"
                                            >{{ "Tracking ID"
                                            }}<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="member.tracking_id"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_tracking_id`
                                                    ],
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_tracking_id`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_tracking_id`
                                                ][0]
                                            }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label"
                                            >{{ "Gmail"
                                            }}<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="email"
                                            class="form-control"
                                            v-model="member.gmail"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_gmail`
                                                    ],
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_gmail`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_gmail`
                                                ][0]
                                            }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label"
                                            >{{ "Pak Visa Password"
                                            }}<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="member.pak_visa_password"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_pak_visa_password`
                                                    ],
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_pak_visa_password`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_pak_visa_password`
                                                ][0]
                                            }}
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label"
                                            >{{ "Gmail Password"
                                            }}<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="member.gmail_password"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_gmail_password`
                                                    ],
                                            }"
                                        />
                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_gmail_password`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_gmail_password`
                                                ][0]
                                            }}
                                        </div>
                                    </div>
                                    <!-- Gender -->
                                    <div class="col-12 col-md-4">
                                        <label
                                            for="individual_gender"
                                            class="form-label"
                                            >Gender<i class="text-danger"
                                                >*</i
                                            ></label
                                        >
                                        <Multiselect
                                            v-model="member.gender"
                                            :options="genderOptions"
                                            :searchable="true"
                                            :class="{
                                                'invalid-bg':
                                                    familyFormErrors[
                                                        `family_${index}_gender`
                                                    ],
                                            }"
                                        />

                                        <div
                                            class="invalid-feedback d-block"
                                            v-if="
                                                familyFormErrors[
                                                    `family_${index}_gender`
                                                ]
                                            "
                                        >
                                            {{
                                                familyFormErrors[
                                                    `family_${index}_gender`
                                                ][0]
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <button
                                v-if="familyForm.family_members"
                                type="button"
                                class="btn btn-success mt-3"
                                @click="submitFamilyForm"
                                :disabled="familyFormStatus === 0"
                            >
                                {{
                                    familyFormStatus === 1
                                        ? "Fam. Save"
                                        : "Saving..."
                                }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="modal fade"
                id="editRecordModalForm"
                tabindex="-1"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="section-title">
                                <h5 class="c-theme-red">Update Record</h5>
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
                            <div class="tab-content">
                                <!-- Single Form Tab -->

                                <div>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label"
                                                >{{ "Date"
                                                }}<i class="text-danger"
                                                    >*</i
                                                ></label
                                            >
                                            <input
                                                type="date"
                                                :max="today"
                                                id="date"
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
                                                >Full Name<i class="text-danger"
                                                    >*</i
                                                ></label
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
                                                >Phone Number<i
                                                    class="text-danger"
                                                    >*</i
                                                ></label
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
                                                >Status<i class="text-danger"
                                                    >*</i
                                                ></label
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
                                                >Amount<i class="text-danger"
                                                    >*</i
                                                ></label
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
                                                >Tracking ID<i
                                                    class="text-danger"
                                                    >*</i
                                                ></label
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
                                                >Visa Fee<i class="text-danger"
                                                    >*</i
                                                ></label
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
                                                >Gmail<i class="text-danger"
                                                    >*</i
                                                ></label
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
                                                >Pak Visa Password<i
                                                    class="text-danger"
                                                    >*</i
                                                ></label
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
                                                >Gmail Password<i
                                                    class="text-danger"
                                                    >*</i
                                                ></label
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
                                                >Gender<i class="text-danger"
                                                    >*</i
                                                ></label
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
                                                ? "Update"
                                                : "Updating..."
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
    },
    data() {
        return {
            searchQuery: "",
            today: this.getPakistanDate(),
            VisasRecords: [], // Populate this with actual data
            selectedVisa: {},
            pluckedReferrals: [],
            tab: "single",
            familyForm: {
                full_name: "",
                phone_number: "",
                status: "",
                amount: "",
                tracking_id: "",
                gmail: "",
                pak_visa_password: "",
                gmail_password: "",
                gender: "",
                date: "",
                referral: "",
                family_name: "",
                phone_number: "",
                family_members: "",
                family_forms: [], // Holds dynamic family member form data
            },

            // familyForm: {
            //     family_forms: [], // Holds dynamic family member form data
            // },

            familyFormErrors: {},
            formErrors: {},

            familyFormStatus: 1, // or 0 (1 = active, 0 = inactive)

            statusOptions: [
                "Initial",
                "Applied",
                "Cancel",
                "Rejected",
                "Approved",
            ], // Status options
            genderOptions: ["Male", "Female", "Other"],

            // Code for editing the existing records..
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
                referral: "",

                employee: [],
            },

            individualFormErrors: {},

            formErrors: {},
            individualFormStatus: 1,
        };
    },
    computed: {
        filteredRecords() {
            if (!this.searchQuery) return this.VisasRecords;

            return this.VisasRecords.filter((record) => {
                const query = this.searchQuery.toLowerCase();

                // Check if primary visa record matches
                const matchesVisaRecord =
                    (record.family_name &&
                        record.family_name.toLowerCase().includes(query)) ||
                    (record.phone_number &&
                        record.phone_number.toLowerCase().includes(query)) ||
                    (record.date && record.date.toLowerCase().includes(query));

                // Check if any family member matches
                const matchesFamilyMember = record.family_members?.some(
                    (member) =>
                        (member.full_name &&
                            member.full_name.toLowerCase().includes(query)) ||
                        (member.phone_number &&
                            member.phone_number
                                .toLowerCase()
                                .includes(query)) ||
                        (member.date &&
                            member.date.toLowerCase().includes(query))
                );

                return matchesVisaRecord || matchesFamilyMember;
            });
        },
    },
    mounted() {
        this.fetchRecords();
    },

    watch: {
        "familyForm.family_members"(newCount) {
            this.initializeFamilyForms(newCount);
        },
    },
    methods: {
        getPakistanDate() {
            // Get the current date in Pakistan Standard Time (Asia/Karachi)
            let formatter = new Intl.DateTimeFormat("en-CA", {
                timeZone: "Asia/Karachi",
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
            });

            // Format it correctly for the <input type="date">
            let parts = formatter.formatToParts(new Date());

            let year = parts.find((p) => p.type === "year").value;
            let month = parts.find((p) => p.type === "month").value;
            let day = parts.find((p) => p.type === "day").value;

            return `${year}-${month}-${day}`; // YYYY-MM-DD format
        },
        openModal(visa) {
            this.selectedVisa = visa;
            const modal = new bootstrap.Modal(
                document.getElementById("visaModal")
            );
            modal.show();
        },

        closeModal() {
            this.$refs.closeThisModal.click();
            this.fetchRecords();
            // this.selectedVisa = null;
        },
        editRecord(visarecord) {
            this.individualForm = { ...visarecord };
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
        // Initialize family forms based on the number of family members
        initializeFamilyForms(count) {
            this.familyForm.family_forms = [];
            for (let i = 0; i < count; i++) {
                this.familyForm.family_forms.push({
                    full_name: "",
                    phone_number: "",
                    status: "",
                    tracking_id: "",
                    gmail: "",
                    pak_visa_password: "",
                    gmail_password: "",
                    visa_fee: "",
                });
            }
        },

        clearFields() {
            this.familyForm = {
                full_name: "",
                phone_number: "",
                status: "",
                amount: "",
                tracking_id: "",
                gmail: "",
                pak_visa_password: "",
                gmail_password: "",
                gender: "",
                date: "",
                referral: "",
                family_name: "",
                family_members: "",
                family_forms: [], // Resets dynamic family member form data
            };

            this.familyFormErrors = {};
            this.formErrors = {};
        },
        submitFamilyForm() {
            // alert("test");
            // this.$refs.closeVisaFamilyModal.click();
            this.validateFamilyForm(true);
            if (Object.keys(this.familyFormErrors).length > 0) {
                return; // Stop if there are validation errors
            }

            this.familyFormStatus = 0; // Mark as saving

            axios
                .post(route("api.family.members.store"), this.familyForm, {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    toastr.success(response.data);
                    this.$refs.closeVisaFamilyModal.click();
                    // this.$refs.closeThisModal.click();
                    // this.fetchRecords();
                    this.closeModal();

                    this.familyFormStatus = 1;
                })
                .catch((error) => {
                    this.familyFormStatus = 1;
                    toastr.error(error.response.data.message);

                    if (error.response.data.errors) {
                        this.formErrors = error.response.data.errors;
                    }
                });
        },
        validateFamilyForm() {
            this.familyFormErrors = {}; // Reset errors

            this.familyForm.family_forms.forEach((member, index) => {
                let fields = [
                    "full_name",
                    "phone_number",
                    "status",
                    "amount",
                    "visa_fee",
                    "tracking_id",
                    "gmail",
                    "pak_visa_password",
                    "gmail_password",
                    "gender",
                    "date",
                ];

                fields.forEach((field) => {
                    if (!member[field]) {
                        // Initialize array if not set
                        if (
                            !this.familyFormErrors[`family_${index}_${field}`]
                        ) {
                            this.familyFormErrors[`family_${index}_${field}`] =
                                [];
                        }
                        this.familyFormErrors[`family_${index}_${field}`].push(
                            `${field.replace(/_/g, " ")} is required`
                        );
                    }
                });

                // Ensure amount is greater than visa_fee
                if (
                    member.amount &&
                    member.visa_fee &&
                    parseFloat(member.amount) <= parseFloat(member.visa_fee)
                ) {
                    if (!this.familyFormErrors[`family_${index}_amount`]) {
                        this.familyFormErrors[`family_${index}_amount`] = [];
                    }
                    this.familyFormErrors[`family_${index}_amount`].push(
                        "Amount must be greater than Visa Fee"
                    );
                }
            });
        },

        fetchRecords() {
            axios
                .get(route("api.family.members.fetch"), {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    this.VisasRecords = response.data;
                })
                .catch((error) => {
                    toastr.error(error.response.data.message);
                });
        },
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
        submitIndividualForm() {
            this.validateIndividualForm();
            if (Object.keys(this.individualFormErrors).length > 0) {
                return; // Stop if there are errors
            }

            this.individualFormStatus = 0; // Mark as saving

            axios
                .post(route("api.family.member.update"), this.individualForm, {
                    headers: {
                        Authorization: "Bearer " + this.$page.props.auth_token,
                    },
                })
                .then((response) => {
                    toastr.success(response.data);
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
    border-radius: 0.45rem;
    border-color: #3c444d !important;
}
</style>
