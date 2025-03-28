<template>
    <main id="main" class="main">
      <div class="pagetitle d-flex justify-content-between">
        <div>
          <h1 class="theme-text-color">{{ referral.name }}</h1>
          <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="dashboard">TTC Global</a>
              </li>
              <li class="breadcrumb-item">Referral</li>
              <li class="breadcrumb-item active">Details</li>
            </ol>
          </nav>
        </div>
      </div>
  
      <section class="section">
        <div class="card p-3">
          <div class="d-flex gap-2 align-items-center">
            <!-- Year Multiselect -->
            <Multiselect
              v-model="selectedYear"
              :options="years"
              :searchable="true"
              placeholder="Select Year"
            />
  
            <!-- Month Multiselect -->
            <Multiselect
              v-model="selectedMonth"
              :options="months"
              :searchable="true"
              placeholder="Select Month"
            />
          </div>
        </div>
      </section>
  
      <!-- Transactions Section -->
      <section class="section">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title theme-text-color">Transactions</h5>
  
            <table id="ledgerTable" class="table table-striped">
              <thead class="bg-gray-200">
                <tr>
                  <th class="border p-2">Parent ID</th>
                  <th class="border p-2">Full Name</th>
                  <th class="border p-2">Phone</th>
                  <th class="border p-2">Tracking ID</th>
                  <th class="border p-2">Actual Amount</th>
                  <th class="border p-2">Net Amount</th>
                  <th class="border p-2">Amount Referral Will Receive</th>
                </tr>
              </thead>
              <tbody>
                <!-- Loop through groupedData -->
                <tr v-for="(group, groupIndex) in groupedData" :key="group.parent_id">
                  <td colspan="7" class="bg-gray-100 font-bold text-center">
                    Grouped by Parent ID: {{ group.parent_id }}
                    <br />
                    <b>Total Cash In:</b> {{ group.total_cash_in }}
                    <b>Total Cash Out:</b> {{ group.total_cash_out }}
                    <b>Total Commission:</b> {{ group.total_commission_amount }}
                  </td>
                </tr>
  
                <!-- Loop through transactions inside each group -->
                <!-- <tr v-for="(entry, index) in group.transactions" :key="index">
                  <td></td>  
                  <td>
                    <a href="#" class="theme-text-color" @click="openModal(group, entry)">
                      <b class="text-success">{{ entry.entry_type }} - Apply</b>
                      <br />
                      <b v-if="entry && entry.visa && entry.visa.full_name">1 - {{ entry.visa.full_name }}</b>
  
                    
                      <div v-if="entry.entry_type === 'Family'">
                        <span v-for="(familyMember, fmIndex) in entry.familyMembers" :key="fmIndex">
                          <b>{{ fmIndex + 1 + 1 }} - {{ familyMember.full_name ?? '' }}<br /></b>
                        </span>
                      </div>
                    </a>
                  </td>
                  <td class="border p-2" v-if="entry && entry.visa && entry.visa.phone_number">
                    {{ entry.visa.phone_number }}
                  </td>
                  <td class="border p-2" v-if="entry && entry.visa && entry.visa.tracking_id">
                    {{ entry.visa.tracking_id }}
                  </td>
                  <td class="border p-2">
                    <div v-if="entry.entry_type === 'Family'">
                      {{ sumFamilyActualAmount(entry.cash_in, entry.familyMembers) }}
                    </div>
                    <div v-else>
                      {{ entry.cash_in || "-" }}
                    </div>
                  </td>
                  <td class="border p-2 text-green-600">
                    <div v-if="entry.entry_type === 'Family'">
                      {{ calculateFamilyNetAmount(sumFamilyVisaFeeAmount(entry.visa, entry.familyMembers), sumFamilyActualAmount(entry.cash_in, entry.familyMembers)) }}
                    </div>
                    <div v-else>
                      {{ calculateNetAmount(entry) }}
                    </div>
                  </td>
                  <td class="border p-2 font-bold text-blue-600">
                    <div v-if="entry.entry_type === 'Family'">
                      {{ calculateReferralAmount(entry) }}
                    </div>
                    <div v-else>
                      {{ calculateAmountAfterCommission(entry) }}
                    </div>
                  </td>
                </tr> -->
              </tbody>
              <tfoot class="bg-gray-100 font-bold">
                <tr>
                  <td class="border p-2 text-center" colspan="3">
                    Total
                  </td>
                  <td class="border p-2">
                    {{ totalActualAmount }}
                  </td>
                  <td class="border p-2 text-green-600">
                    {{ totalNetAmount }}
                  </td>
                  <td class="border p-2 text-blue-600">
                    {{ totalAmountReceived }}
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </section>
  
      <!-- Bootstrap Modal for Visa Details -->
      <div class="modal fade" id="visaModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalTitle">
                {{ selectedVisa.family_name || selectedVisa.full_name }}
                {{ selectedVisa.phone_number }}
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th class="border p-2">#</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Date</th>
                    <th class="border p-2">Full Name</th>
                    <th class="border p-2">Phone Number</th>
                    <th class="border p-2">Amount</th>
                    <th class="border p-2">Visa Fee</th>
                    <th class="border p-2">Gender</th>
                  </tr>
                </thead>
                <tbody v-if="selectedVisa.entry_type === 'Family'">
                  <!-- Main Visa Holder -->
                  <tr>
                    <td class="border p-2">1</td>
                    <td class="border p-2">{{ selectedVisa.status }}</td>
                    <td class="border p-2">{{ selectedVisa.date }}</td>
                    <td class="border p-2">{{ selectedVisa.full_name }}</td>
                    <td class="border p-2">{{ selectedVisa.phone_number }}</td>
                    <td class="border p-2">{{ selectedVisa.amount || "N/A" }}</td>
                    <td class="border p-2">{{ selectedVisa.visa_fee }}</td>
                    <td class="border p-2">{{ selectedVisa.gender }}</td>
                  </tr>
                  <!-- Family Members -->
                  <tr v-if="selectedVisa.familyMembers.length === 0">
                    <td colspan="7" class="border p-2 text-center">
                      No family members available
                    </td>
                  </tr>
                  <tr v-for="(familyMember, index) in selectedVisa.familyMembers" :key="familyMember.id">
                    <td class="border p-2">{{ index + 2 }}</td>
                    <td class="border p-2">{{ familyMember.status }}</td>
                    <td class="border p-2">{{ familyMember.date }}</td>
                    <td class="border p-2">{{ familyMember.full_name }}</td>
                    <td class="border p-2">{{ familyMember.phone_number }}</td>
                    <td class="border p-2">{{ familyMember.amount || "N/A" }}</td>
                    <td class="border p-2">{{ familyMember.visa_fee }}</td>
                    <td class="border p-2">{{ familyMember.gender }}</td>
                  </tr>
                </tbody>
                <!-- Non-Family Visa -->
                <tbody v-else>
                  <tr>
                    <td class="border p-2">1</td>
                    <td class="border p-2">{{ selectedVisa.status }}</td>
                    <td class="border p-2">{{ selectedVisa.date }}</td>
                    <td class="border p-2">{{ selectedVisa.full_name }}</td>
                    <td class="border p-2">{{ selectedVisa.phone_number }}</td>
                    <td class="border p-2">{{ selectedVisa.amount || "N/A" }}</td>
                    <td class="border p-2">{{ selectedVisa.visa_fee }}</td>
                    <td class="border p-2">{{ selectedVisa.gender }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </template>
  
  <script>
  import Master from "../Layout/Master.vue";
  import Multiselect from "@vueform/multiselect";
  import axios from "axios";
  
  export default {
  layout: Master,
  components: { Multiselect },
  props: ["referral", "transactions", "groupedData"],

  mounted() {
    console.log('Referral:', this.referral);
    console.log('Transactions:', this.transactions);
    console.log('Grouped Data:', this.localGroupedData);  // Using the renamed local property
  },

  data() {
    return {
      selectedYear: new Date().getFullYear(),
      selectedMonth: new Date().getMonth() + 1,
      years: Array.from(
        { length: 10 },
        (_, i) => new Date().getFullYear() - i
      ),
      months: [
        { value: 1, label: "January" },
        { value: 2, label: "February" },
        { value: 3, label: "March" },
        { value: 4, label: "April" },
        { value: 5, label: "May" },
        { value: 6, label: "June" },
        { value: 7, label: "July" },
        { value: 8, label: "August" },
        { value: 9, label: "September" },
        { value: 10, label: "October" },
        { value: 11, label: "November" },
        { value: 12, label: "December" },
      ],
      localGroupedData: [],  // Renamed to avoid prop conflict
      selectedVisa: {},
      showModal: false,
      FamilyNetAmount: 0,
      FamilytotalVisaFee: 0,
      totalReferralAmount: 0,
    };
  },

  created() {
    console.log("Created Hook Triggered");
  },

  methods: {
    // Your methods here
  },

  computed: {
    // Your computed properties here
  },

  watch: {
    groupedData: {
      handler(newGroupedData) {
        this.localGroupedData = newGroupedData;  // Use prop to populate local state
      },
      immediate: true,
    },
  }
};

  </script>
  