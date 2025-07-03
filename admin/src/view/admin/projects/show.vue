<template>
  <div>
    <Breadcrumbs />

    <div class="container">
      <div class="card mb-4 shadow rounded overflow-hidden">
        <div class="card-body p-0">
          <div
            v-if="project.images.length"
            id="projectImageCarousel"
            class="carousel slide"
            data-bs-ride="carousel"
          >
            <div class="carousel-inner">
              <div
                class="carousel-item"
                v-for="(img, index) in project.images"
                :key="img.id"
                :class="{ active: index === 0 }"
              >
                <img
                  :src="img.url"
                  class="d-block w-100"
                  style="height: 300px; object-fit: cover"
                  alt="Project Image"
                  @error="handleImageError($event)"
                />
              </div>
            </div>
            <button
              class="carousel-control-prev"
              type="button"
              data-bs-target="#projectImageCarousel"
              data-bs-slide="prev"
            >
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button
              class="carousel-control-next"
              type="button"
              data-bs-target="#projectImageCarousel"
              data-bs-slide="next"
            >
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>

          <!-- Project Info -->
          <div class="p-4">
              <div class="d-flex justify-content-between">
                  <h2 class="h5 font-weight-semibold mb-2">{{ project.name }}</h2>
                  <button
                      v-if="userRole === 'entrepreneur'"
                      class="btn btn-primary"
                      @click="showInviteModal = true"
                  >
                      {{ $t("title.InviteUser") }}
                  </button>
              </div>

            <p class="text-muted mb-1">{{ project.description }}</p>
            <p class="mb-3">
              <strong>{{ $t("title.AssetType") }}:</strong> {{ project.asset_type }} |
              <strong>{{ $t("title.Location") }}:</strong> {{ project.location }}
            </p>

            <div class="row row-cols-2 row-cols-md-4 text-muted">
              <div class="bg-light text-center rounded p-1 border-right">
                <div>{{ $t("title.CurrentValue") }}</div>
                <div class="fw-semibold text-primary">${{ project.current_value }}</div>
              </div>
              <div class="bg-light text-center rounded p-2 border-right">
                <div>{{ $t("title.PurchasePrice") }}</div>
                <div class="fw-semibold">${{ project.purchase_price }}</div>
              </div>
              <div class="bg-light text-center rounded p-2 border-right">
                <div>{{ $t("title.RenovationCost") }}</div>
                <div class="fw-semibold text-danger">${{ project.renovation_cost }}</div>
              </div>
              <div class="bg-light text-center rounded p-2">
                <div>{{ $t("title.Wedge") }}</div>
                <div class="fw-semibold">
                  {{ project.wedge !== null ? "$" + project.wedge : "—" }}
                </div>
              </div>
            </div>

            <!-- Entrepreneur Info -->
            <div v-if="project.entrepreneur" class="mt-4 border-top pt-3">
              <h6>{{ $t("title.Entrepreneur") }}</h6>
              <p class="mb-0">
                <strong>{{ $t("title.Name") }}:</strong>
                {{ project.entrepreneur.name || "—" }}
              </p>
              <p class="mb-0">
                <strong>{{ $t("title.Email") }}:</strong>
                {{ project.entrepreneur.email || "—" }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <div v-if="project.project_timeline" class="mt-5">
        <h5 class="d-flex justify-content-between align-items-center">
          {{ $t("title.ProjectTimeline") }}
          <button
            class="btn btn-sm btn-outline-primary"
            @click="isTimelineExpanded = !isTimelineExpanded"
          >
            <i
              :class="isTimelineExpanded ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"
            ></i>
          </button>
        </h5>
        <div v-show="isTimelineExpanded" class="timeline-chart mt-3">
          <div class="timeline-header d-flex">
            <div class="timeline-labels" style="width: 150px">
              <div class="timeline-month-header" style="height: 52px"></div>
              <template v-for="(phase, index) in project.project_timeline.timeline">
                <!-- Parent Phase -->
                <div
                  :key="'phase-label-' + index"
                  class="timeline-row d-flex align-items-center"
                  style="height: 40px"
                >
                  <div class="d-flex align-items-center w-100">
                    <span class="phase-title">{{ phase.title }}</span>
                    <div class="ms-2">
                      <button class="btn btn-sm me-1" @click.stop="openEditModal(phase)">
                        <i class="fas fa-edit"></i>
                      </button>
                      <i
                        v-if="phase.children?.length"
                        class="fas fa-angle-down phase-toggle"
                        :class="{ expanded: expandedPhases.has(phase.id) }"
                        @click.stop="togglePhase(phase)"
                      ></i>
                    </div>
                  </div>
                </div>
                <!-- Child Phases -->
                <template v-if="phase.children?.length && expandedPhases.has(phase.id)">
                  <div
                    v-for="(child, childIndex) in phase.children"
                    :key="'child-label-' + index + '-' + childIndex"
                    class="timeline-row d-flex align-items-center child-row"
                    style="height: 40px"
                  >
                    <div class="d-flex align-items-center w-100">
                      <span class="phase-title child-title">{{ child.title }}</span>
                    </div>
                  </div>
                </template>
              </template>
            </div>
            <div class="timeline-grid-wrapper" style="overflow-x: auto">
              <div
                class="timeline-grid flex-grow-1 position-relative"
                :style="{ minWidth: timelineMonths.length * 100 + 'px' }"
              >
                <div class="d-flex timeline-months">
                  <div
                    v-for="(month, index) in timelineMonths"
                    :key="'month-' + index"
                    class="timeline-month-cell text-center"
                    style="width: 100px"
                  >
                    {{ month }}
                  </div>
                </div>
                <div class="timeline-grid-lines">
                  <div
                    v-for="(month, index) in timelineMonths"
                    :key="'grid-' + index"
                    class="timeline-grid-line"
                    :style="{ left: index * 100 + 'px' }"
                  ></div>
                </div>
                <template
                  v-for="(phase, phaseIndex) in project.project_timeline.timeline"
                >
                  <!-- Parent Phase -->
                  <div
                    :key="'phase-bar-' + phaseIndex"
                    class="timeline-row"
                    style="height: 40px"
                  >
                    <div
                      v-if="phase.start_date && phase.end_date"
                      class="timeline-bar"
                      :style="getPhaseBarStyle(phase)"
                      :title="phase.title"
                    >
                      <!-- {{ phase.title }} -->
                    </div>
                  </div>
                  <!-- Child Phases -->
                  <template v-if="phase.children?.length && expandedPhases.has(phase.id)">
                    <div
                      v-for="(child, childIndex) in phase.children"
                      :key="'child-bar-' + phaseIndex + '-' + childIndex"
                      class="timeline-row child-row"
                      style="height: 40px"
                    >
                      <div
                        v-if="child.start_date && child.end_date"
                        class="timeline-bar child-phase"
                        :style="getPhaseBarStyle(child)"
                        :title="child.title"
                      >
                        <!-- {{ child.title }} -->
                      </div>
                    </div>
                  </template>
                </template>
                <!-- Milestones -->
                <div class="timeline-milestones">
                  <div
                    v-for="(milestone, index) in filteredMilestones"
                    :key="'milestone-' + index"
                    class="timeline-milestone"
                    :style="getMilestoneStyle(milestone)"
                  >
                    <div class="d-flex align-items-center justify-content-between">
                      <div
                        class="milestone-title"
                        style="cursor: pointer"
                        @click="openMilestonePhaseModal(milestone)"
                      >
                        {{ milestone.title }}
                        <i class="fas fa-edit"></i>
                      </div>
                    </div>
                    <div class="milestone-line"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4 border-top pt-3">
        <h5 class="mb-3">{{ $t("title.BudgetBreakdown") }}</h5>
        <div class="table-responsive">
          <table class="table table-borderless">
            <thead>
              <tr>
                <th></th>
                <th class="text-end">{{ $t("title.PlannedCost") }}</th>
                <th class="text-end">{{ $t("title.ActualCost") }}</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="(phase, index) in project.budget_breakdown.phases">
                <!-- Parent Phase -->
                <tr :key="'budget-' + index" class="border-top">
                  <td>
                    <div class="d-flex align-items-center">
                      <span>{{ phase.title }}</span>
                      <i
                        v-if="phase.children?.length"
                        class="fas fa-angle-down ms-2 budget-toggle"
                        :class="{ expanded: expandedBudgetPhases.has(phase.id) }"
                        @click="toggleBudgetPhase(phase)"
                      ></i>
                    </div>
                  </td>
                  <td class="text-end">${{ formatCurrency(phase.planned_cost) }}</td>
                  <td class="text-end">${{ formatCurrency(phase.actual_cost) }}</td>
                </tr>
                <!-- Child Phases -->
                <template
                  v-if="phase.children?.length && expandedBudgetPhases.has(phase.id)"
                >
                  <tr
                    v-for="(child, childIndex) in phase.children"
                    :key="'budget-child-' + index + '-' + childIndex"
                    class="child-budget-row"
                  >
                    <td class="ps-4">
                      <div class="d-flex align-items-center">
                        <span class="child-title">{{ child.title }}</span>
                      </div>
                    </td>
                    <td class="text-end">${{ formatCurrency(child.planned_cost) }}</td>
                    <td class="text-end">${{ formatCurrency(child.actual_cost) }}</td>
                  </tr>
                </template>
              </template>
              <tr class="border-top">
                <td>
                  <strong>{{ $t("title.Total") }}</strong>
                </td>
                <td class="text-end">
                  <strong
                    >${{
                      formatCurrency(project.budget_breakdown.total_planned_cost)
                    }}</strong
                  >
                </td>
                <td class="text-end">
                  <strong
                    >${{
                      formatCurrency(project.budget_breakdown.total_actual_cost)
                    }}</strong
                  >
                </td>
              </tr>
              <tr>
                <td>
                  <strong>{{ $t("title.ExtraValues") }}</strong>
                </td>
                <td class="text-end" colspan="2">
                  <strong
                    >${{
                      formatCurrency(project.budget_breakdown.total_extra_values)
                    }}</strong
                  >
                </td>
              </tr>
              <tr class="border-top">
                <td>
                  <strong>{{ $t("title.GrandTotal") }}</strong>
                </td>
                <td class="text-end" colspan="2">
                  <strong
                    >${{ formatCurrency(project.budget_breakdown.grand_total) }}</strong
                  >
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Value Over Time Graph -->
      <div class="mt-5">
        <h5>{{ $t("title.ValueOverTime") }}</h5>
        <apexchart
          v-if="valueSeries[0].data.length"
          type="line"
          height="300"
          :options="chartOptions"
          :series="valueSeries"
          class="bg-white"
        />
      </div>

      <div class="mt-4">
        <div class="d-flex justify-content-between">
          <h5>{{ $t("title.ProjectDocuments") }}</h5>
          <button
            class="btn btn-primary mb-3"
            @click="showModal = true"
            :class="{ 'd-none': !(this.userRole == 'entrepreneur' || this.userRole =='admin') }"
          >
            {{ $t("title.AddDocument") }}
          </button>
        </div>

        <div v-if="documents.length" class="list-group">
          <div
            v-for="doc in documents"
            :key="doc.id"
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <div>
              <strong>{{ doc.name }}</strong
              ><br />
              <small class="text-muted">{{ doc.file_type }}</small>
            </div>
            <div>
              <a
                :href="doc.url"
                target="_blank"
                class="btn btn-sm btn-outline-primary me-2"
              >
                {{ $t("title.View") }}
              </a>
              <button
                @click="deleteDocument(doc.id)"
                class="btn btn-sm btn-outline-danger"
              >
                {{ $t("title.Delete") }}
              </button>
            </div>
          </div>
        </div>

        <div v-else class="text-muted">{{ $t("title.NoDocumentsFound") }}</div>
      </div>

      <!-- Project Messages -->
      <div class="mt-5">
        <!-- Role Selector -->
        <div class="mb-3">
          <div class="d-flex gap-2">
            <h5>{{ $t("title.Messages") }}</h5>
            <button
              :class="{
                'btn-primary': true,
                rounded: true,
                'rounded-circle': true,
                'd-none': !(this.userRole == 'entrepreneur'
                || this.userRole =='investor'
                || this.userRole =='tenant' ),
              }"
                            @click="showMessageModal = true"
                        >
                            <b-icon icon="plus" font-scale="1.5"/>
                        </button>
                        <!-- Only show role selector if NOT investor or tenant -->
                        <div class="btn-group" role="group" aria-label="Role selector"
                             v-if="!(this.userRole =='investor' || this.userRole =='tenant')">
                            <button
                                type="button"
                                class="btn"
                                :class="{
                                  'btn-primary': selectedRole === 'tenant',
                                  'btn-outline-primary': selectedRole !== 'tenant',
                                }"
                                @click="selectedRole = 'tenant'"
                            >
                                {{ $t("title.Tenant") }}
                            </button>
                            <button
                                type="button"
                                class="btn"
                                :class="{
                                  'btn-primary': selectedRole === 'investor',
                                  'btn-outline-primary': selectedRole !== 'investor',
                                }"
                                @click="selectedRole = 'investor'"
                            >
                                {{ $t("title.Investor") }}
                            </button>
                            <button
                                type="button"
                                class="btn"
                                :class="{
                                  'btn-primary': selectedRole === 'all',
                                  'btn-outline-primary': selectedRole !== 'all',
                                }"
                                @click="showAllMessages"
                            >
                                {{ $t("title.All") }}
                            </button>
                        </div>
                        <!-- Only show 'All Messages' button for investor or tenant -->
                        <div v-else>
                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                @click="showAllMessages"
                            >
                                {{ $t("title.AllMessages") }}
                            </button>
                        </div>
                        <!-- Sent Messages Button -->
                        <button
                            type="button"
                            class="btn ms-2"
                            :class="{
                                'btn-primary': isSentMessagesActive,
                                'btn-outline-primary': !isSentMessagesActive
                            }"
                            @click="showSentMessages"
                        >
                            {{ $t("title.SentMessages") }}
                        </button>
                    </div>
                </div>

        <!-- Messages List -->
        <div v-if="messages.length" class="list-group">
          <div class="list-group-item" v-for="msg in messages.slice(0, 4)" :key="msg.id">
            <div>
              <small class="text-muted">{{ formatDate(msg.created_at) }}</small>
              <strong>{{ msg.sender_name }}</strong> ({{ msg.sender.role }})
              <p class="mb-1">{{ msg.subject }}</p>
            </div>
          </div>
        </div>

        <div v-else class="text-muted">
          {{ $t("title.NoMessagesToShow") }}
        </div>
      </div>
    </div>
    <b-modal
      v-model="showModal"
      :title="$t('title.UploadDocument')"
      @hide="resetForm"
      hide-footer
    >
      <b-form @submit.prevent="uploadDocument">
        <div class="modal-body">
          <b-form-group :label="$t('title.DocumentName')" label-for="doc-name">
            <b-form-input
              id="doc-name"
              v-model="form.name"
              required
              :placeholder="$t('title.EnterDocumentName')"
            ></b-form-input>
          </b-form-group>

          <b-form-group :label="$t('title.DocumentFile')" label-for="doc-file">
            <b-form-file
              id="doc-file"
              @change="handleFileChange"
              required
              :browse-text="$t('title.Browse')"
            ></b-form-file>
          </b-form-group>

          <div v-if="error" class="text-danger">{{ error }}</div>
        </div>

        <div class="modal-footer">
          <b-button variant="secondary" @click="resetForm"
            >{{ $t("title.Cancel") }}
          </b-button>
          <b-button type="submit" variant="primary" :disabled="loading">
            {{ loading ? $t("title.Uploading") : $t("title.Upload") }}
          </b-button>
        </div>
      </b-form>
    </b-modal>

        <b-modal
            v-model="showMessageModal"
            :title="$t('title.SendMessage')"
            @hide="resetMessageForm"
            hide-footer
        >
            <b-form @submit.prevent="sendMessage">
                <b-form-group :label="$t('title.ReceiverType')" v-if="!(this.userRole =='investor' ||this.userRole =='tenant')">
                    <b-form-select
                        v-model="messageForm.receiver_type"
                        :options="receivers"
                    ></b-form-select>
                </b-form-group>

        <b-form-group :label="$t('title.PhaseOptional')">
          <b-form-select
            v-model="messageForm.phase_id"
            :options="phases"
            :placeholder="$t('title.SelectPhase')"
          >
            <template #first>
              <b-form-select-option :value="null" disabled>
                {{ $t("title.SelectPhase") }}
              </b-form-select-option>
            </template>
          </b-form-select>
        </b-form-group>

        <b-form-group :label="$t('title.Subject')">
          <b-form-input v-model="messageForm.subject" required></b-form-input>
        </b-form-group>

        <b-form-group :label="$t('title.Message')">
          <b-form-textarea
            v-model="messageForm.message"
            rows="3"
            required
          ></b-form-textarea>
        </b-form-group>

        <b-form-group :label="$t('title.File')">
          <b-form-file
            @change="(e) => (messageForm.file = e.target.files[0])"
            accept=".jpg,.jpeg,.png,.pdf,.docx"
          ></b-form-file>
        </b-form-group>

        <div v-if="error" class="text-danger mb-2">{{ error }}</div>

        <b-button variant="secondary" @click="resetMessageForm"
          >{{ $t("title.Cancel") }}
        </b-button>
        <b-button type="submit" variant="primary" :disabled="loading">
          {{ loading ? $t("title.Sending") : $t("title.Send") }}
        </b-button>
      </b-form>
    </b-modal>

    <!-- Edit Phase Modal -->
    <div
      v-if="showEditModal"
      class="modal fade show"
      style="display: block"
      tabindex="-1"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t("title.EditPhase") }}</h5>
            <button type="button" class="btn-close" @click="closeEditModal"></button>
          </div>
          <div class="modal-body" style="max-height: 80vh; overflow-y: auto">
            <div class="mb-3">
              <label class="form-label">{{ $t("title.Title") }}</label>
              <input type="text" class="form-control" v-model="editingPhase.title" />
            </div>
            <div class="mb-3">
              <label class="form-label">{{ $t("title.Description") }}</label>
              <input
                type="text"
                class="form-control"
                v-model="editingPhase.description"
              />
            </div>
            <div class="mb-3 row">
              <div class="col">
                <label class="form-label">{{ $t("title.StartDate") }}</label>
                <input
                  type="date"
                  class="form-control"
                  v-model="editingPhase.start_date"
                />
              </div>
              <div class="col">
                <label class="form-label">{{ $t("title.EndDate") }}</label>
                <input type="date" class="form-control" v-model="editingPhase.end_date" />
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">{{ $t("title.Milestone") }}</label>
              <select class="form-select" v-model="editingPhase.milestone_id">
                <option :value="null">{{ $t("title.SelectMilestone") }}</option>
                <option
                  v-for="m in project.project_timeline.milestones"
                  :key="m.id"
                  :value="m.id"
                >
                  {{ m.title }}
                </option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">{{ $t("title.PlannedCost") }}</label>
              <input
                type="number"
                class="form-control"
                v-model="editingPhase.planned_cost"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">{{ $t("title.ActualCost") }}</label>
              <input
                type="number"
                class="form-control"
                v-model="editingPhase.actual_cost"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">{{ $t("title.DateOfExpense") }}</label>
              <input
                type="date"
                class="form-control"
                v-model="editingPhase.date_of_expense"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">{{ $t("title.Attachment") }}</label>
              <input type="file" class="form-control" @change="handlePhaseFileChange" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeEditModal">
              {{ $t("title.Cancel") }}
            </button>
            <button type="button" class="btn btn-primary" @click="savePhase">
              {{ $t("title.Update") }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showEditModal || showMilestonePhaseModal"
      class="modal-backdrop fade show"
    ></div>

    <!-- Milestone Phase Modal -->
    <div
      v-if="showMilestonePhaseModal"
      class="modal fade show"
      style="display: block"
      tabindex="-1"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t("title.EditPhase") }}</h5>
            <button
              type="button"
              class="btn-close"
              @click="closeMilestonePhaseModal"
            ></button>
          </div>
          <div class="modal-body">
            <!-- Title -->
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" class="form-control" v-model="milestonePhase.title" />
            </div>

            <!-- Colors -->
            <div class="mb-3">
              <label class="form-label">Colors</label>
              <select class="form-select" v-model="milestonePhase.color">
                <option disabled value="">Select stage</option>
                <option v-for="opt in stageColors" :key="opt.value" :value="opt.value">
                  {{ opt.text }}
                </option>
              </select>
            </div>

            <!-- Date -->
            <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" v-model="milestonePhase.date" />
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="closeMilestonePhaseModal"
            >
              {{ $t("title.Cancel") }}
            </button>
            <button type="button" class="btn btn-primary" @click="saveMilestonePhase">
              {{ $t("title.Update") }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Invite User Modal -->
    <b-modal
      v-model="showInviteModal"
      :title="$t('title.InviteUser')"
      @hide="resetInviteForm"
      hide-footer
    >
      <b-form @submit.prevent="sendInvite">
        <b-form-group :label="$t('title.Role')" label-for="role">

            <treeselect
                placeholder="select"
                v-model="inviteForm.role_id"
                class="form-input"
                :options="roles"
                required
            />

        </b-form-group>

        <b-form-group :label="$t('title.PhoneNumber')" label-for="phone">

            <vue-tel-input
                :class="formErrors.has('phone_number') ? `error-input` : ``"
                v-model="inviteForm.phone_number"
                @focus="resetError"
                @input="onPhoneInput"
            />
        </b-form-group>

        <div v-if="inviteError" class="text-danger mb-2">{{ inviteError }}</div>

        <div class="modal-footer">
          <b-button variant="secondary" @click="resetInviteForm">
            {{ $t("title.Cancel") }}
          </b-button>
          <b-button type="submit" variant="primary" :disabled="inviteLoading ">
            {{ inviteLoading ? $t("title.Sending") : $t("title.SendInvite") }}
          </b-button>
        </div>
      </b-form>
    </b-modal>
  </div>
</template>

<script>
import { request } from "@/Util/Request";
import VueApexCharts from "vue-apexcharts";
import Breadcrumbs from "@/components/Breadcrumbs.vue";
import moment from "moment";
import { VueTelInput } from "vue-tel-input";
import "vue-tel-input/dist/vue-tel-input.css";
import Error from "@/Util/Error";

export default {
    components: {
        apexchart: VueApexCharts,
        Breadcrumbs,
        VueTelInput,
    },
    data() {
        return {
            formErrors: new Error({}),
            stageColors: [],
            showMilestonePhaseModal: false,
            milestonePhase: {
                title: "",
                description: "",
                start_date: "",
                end_date: "",
                milestone_id: null,
                planned_cost: 0,
                actual_cost: 0,
                date_of_expense: "",
                attachment: null,
            },
            phone: {
                inputValue: "",
            },
            userRole: null,
            projectId: null,
            selectedRole: "tenant",
            messages: [],
            isSentMessagesActive: false,
            project: {
                name: "",
                description: "",
                asset_type: "",
                location: "—",
                current_value: 0,
                purchase_price: 0,
                renovation_cost: 0,
                wedge: null,
                images: [],
                entrepreneur: null,
                user:null,
                milestones: [],
                value_over_time: [],
                budget_breakdown: {
                    phases: [],
                    total_actual_cost: 0,
                    total_planned_cost: 0,
                    total_extra_values: 0,
                    grand_total: 0,
                },
                project_timeline: {
                    timeline: [],
                    milestones: [],
                },
            },
            chartOptions: {
                chart: {
                    id: "value-over-time",
                    toolbar: {show: false},
                },
                xaxis: {
                    type: "datetime",
                    labels: {format: "yyyy-MM-dd"},
                },
                stroke: {curve: "smooth"},
                dataLabels: {enabled: false},
                colors: ["#4e73df"],
            },
            isTimelineExpanded: true,
            timelineMonths: [],
            project_timeline: [],
            expandedPhases: new Set(),
            expandedBudgetPhases: new Set(),
            showModal: false,
            loading: false,
            successMessage: "",
            error: "",
            form: {
                project_id: "",
                name: "",
                document: null,
            },
            messageForm: {
                receiver_id: null,
                receiver_type: null,
                subject: "",
                message: "",
                project_id: null,
                phase_id: null,
                file: null,
            },
            phases: [],
            showMessageModal: false,
            documents: [],
            receivers: [
                {text: "Select Receivers Type", value: null, disabled: true},
                {value: 0, text: "Admin"},
                {value: 1, text: "Investor"},
                {value: 2, text: "Tenant"},
                {value: 3, text: "Other"},
            ],
            showEditModal: false,
            editingPhase: {
                id: null,
                title: "",
                description: "",
                start_date: "",
                end_date: "",
                milestone_id: null,
                planned_cost: "",
                actual_cost: "",
                date_of_expense: "",
                attachment: null,
            },
            isUpdatingRole: false,
            showInviteModal: false,
            inviteLoading: false,
            inviteError: "",
            inviteForm: {
                role_id: null,
                phone_number: "",
                project_id: null,
                country_code: ""
            },
            roles: [],
            isValidPhone: false,
        };
    },
    computed: {
        valueSeries() {
            if (!Array.isArray(this.project.value_over_time))
                return [{name: "Value", data: []}];
            return [
                {
                    name: "Value",
                    data: this.project.value_over_time.map((point) => ({
                        x: new Date(point.date).toISOString(),
                        y: point.current_property_value,
                    })),
                },
            ];
        },
        totalBudgetCost() {
            return this.project.budget_items.reduce(
                (sum, item) => sum + (parseFloat(item.cost) || 0),
                0
            );
        },
        totalPlannedCost() {
            return this.project.budget_items.reduce(
                (sum, item) => sum + (parseFloat(item.planned_cost) || 0),
                0
            );
        },
        totalActualCost() {
            return this.project.budget_items.reduce(
                (sum, item) => sum + (parseFloat(item.actual_cost) || 0),
                0
            );
        },
        filteredMilestones() {
            return (this.project.project_timeline?.milestones || []).filter(
                (milestone) => milestone.date
            );
        },
    },
    methods: {
        async openMilestonePhaseModal(milestone) {
            this.showMilestonePhaseModal = true;
            this.milestonePhase.id = milestone.id;

      try {
        // Fetch milestone details
        const { data: msResponse } = await request({
          method: "get",
          url: `/milestones/${milestone.id}`,
        });
        const ms = msResponse;
        // Fetch stage colors
        const { data: colorResponse } = await request({
          method: "get",
          url: "/milestones-stage-colors",
        });
        this.stageColors = colorResponse;
        this.milestonePhase.title = ms.title || "";
        this.milestonePhase.date = ms.updated_at ? ms.updated_at.slice(0, 10) : "";

        // Convert stage_color (hex) to corresponding key
        const matchingColor = this.stageColors.find((sc) => sc.color === ms.stage_color);
        this.milestonePhase.color = matchingColor ? matchingColor.value : "";
      } catch (err) {
        console.error("Failed to load milestone or colors:", err);
      }
    },
    // Close the modal
    closeMilestonePhaseModal() {
      this.showMilestonePhaseModal = false;
      this.milestonePhase = {
        id: null,
        title: "",
        color: "",
        date: "",
      };
    },

    // Save the milestone phase update
    async saveMilestonePhase() {
      try {
        const payload = {
          title: this.milestonePhase.title,
          stage_color: this.milestonePhase.color,
          updated_at: this.milestonePhase.date,
        };

        await request({
          method: "post",
          url: `/milestones/update/${this.milestonePhase.id}`,
          data: payload,
        });

                // Optionally reload milestones/timeline
                this.closeMilestonePhaseModal();
                this.fetchProjectData();
            } catch (err) {
                console.error("Error saving milestone", err);
                // Optionally show error to user
            }
        },
        handleImageError(event) {
            event.target.src = require("@/assets/default.png");
        },
        async fetchMessages(options = {}) {
            try {
                let params = {};
                if (this.userRole =='investor'){
                    this.selectedRole = 'investor';
                }
                let receiverType = 0; // Default to All
                if (this.selectedRole === "investor") receiverType = 1;
                else if (this.selectedRole === "tenant") receiverType = 2;

        // Only add receiver_type if we're not fetching sent messages
        if (!options.sentOnly) {
          params.receiver_type = receiverType;
        }

        if (options.sentOnly) {
          params.is_sender = options.sentOnly;
          this.isUpdatingRole = true;
          this.selectedRole = null;
          this.isSentMessagesActive = true;
        } else {
          this.isSentMessagesActive = false;
        }

        const response = await request({
          method: "get",
          url: `/project/messages/${this.projectId}`,
          params,
        });
        this.messages = response.data.messages || [];
      } catch (err) {
        console.error("Failed to load messages:", err);
      } finally {
        this.isUpdatingRole = false;
      }
    },

    showAllMessages() {
      this.$router.push(`/projects/${this.projectId}/messages`);
    },
    formatDate(dateStr) {
      return new Date(dateStr).toLocaleString();
    },
    formatCurrency(value) {
      if (!value) return "0";
      return parseFloat(value).toLocaleString(undefined, {
        style: "decimal",
        minimumFractionDigits: 0,
      });
    },
    async fetchProjectData() {
      try {
        const id = this.$route.params.id;
        const { data } = await request({
          method: "get",
          url: `/superadmin/projects/${id}`,
        });

        console.log("Project Timeline Data:", data.project_timeline.timeline);
        console.log("All Data:", data);

                const proj = data.project;
                this.project_timeline = data.project_timeline.timeline;
                this.userRole = data.assignedUser?.role || proj.user.role;
                const assetType = proj.asset_type;
                const entrepreneur = proj.asset_type?.user || null;

                console.log('role',this.userRole);

                this.phases = this.project_timeline.map((item) => {
                    return {
                        value: item.id,
                        text: `${item.title}`, // or just item.name if you don't need user
                    };
                });

        this.project = {
          name: proj.name,
          description: proj.description,
          asset_type: assetType,
          location: proj.location || "—",
          current_value: this.formatCurrency(proj.current_property_value),
          purchase_price: this.formatCurrency(proj.purchase_price),
          renovation_cost: this.formatCurrency(proj.renovation_cost),
          wedge: proj.wedge !== null ? this.formatCurrency(proj.wedge) : null,
          images:
            proj.images?.map((img) => ({
              id: img.id,
              url: img.url,
            })) || [],
          entrepreneur: entrepreneur,
          user: proj.user,
          milestones: data.project_timeline.milestones || [],
          value_over_time: data.value_over_time || [],
          budget_breakdown: data.budget_breakdown || {
            phases: [],
            total_actual_cost: 0,
            total_planned_cost: 0,
            total_extra_values: 0,
            grand_total: 0,
          },
          project_timeline: {
            timeline: data.project_timeline?.timeline || [],
            milestones: data.project_timeline?.milestones || [],
          },
        };
        this.initializeTimeline();

        console.log("Filtered Milestones:", this.filteredMilestones);
      } catch (err) {
        console.error("Failed to fetch project:", err);
      }
    },
    initializeTimeline() {
      // Find the earliest start date and latest end date from all phases
      let earliestDate = moment();
      let latestDate = moment();

      const phases = this.project_timeline;
      phases.forEach((phase) => {
        if (phase.start_date) {
          const startDate = moment(phase.start_date);
          if (startDate.isBefore(earliestDate)) {
            earliestDate = startDate;
          }
        }
        if (phase.end_date) {
          const endDate = moment(phase.end_date);
          if (endDate.isAfter(latestDate)) {
            latestDate = endDate;
          }
        }
        (phase.children || []).forEach((child) => {
          if (child.start_date) {
            const startDate = moment(child.start_date);
            if (startDate.isBefore(earliestDate)) {
              earliestDate = startDate;
            }
          }
          if (child.end_date) {
            const endDate = moment(child.end_date);
            if (endDate.isAfter(latestDate)) {
              latestDate = endDate;
            }
          }
        });
      });

      // Generate months between earliest and latest date
      const currentDate = moment();
      const startDate = moment().subtract(1, "month").startOf("month");
      const endDate = moment().add(10, "months").endOf("month");

      this.timelineMonths = [];
      let currentMonth = startDate.clone();

      while (currentMonth.isSameOrBefore(endDate)) {
        this.timelineMonths.push(currentMonth.format("MMM YYYY"));
        currentMonth.add(1, "month");
      }
    },
    getPhaseBarStyle(phase) {
      if (!phase.start_date || !phase.end_date) return {};

      const start = moment(phase.start_date);
      const end = moment(phase.end_date);
      const timelineStart = moment(this.timelineMonths[0], "MMM YYYY");

      const left = start.diff(timelineStart, "days") * (100 / 30);
      const width = Math.max(100, end.diff(start, "days") * (100 / 30)); // Minimum width of 100px

      return {
        left: left + "px",
        width: width + "px",
        position: "absolute",
        height: "30px",
        top: "5px",
        backgroundColor: phase.milestone?.stage_color || "#007bff",
        borderRadius: "4px",
        color: "#fff",
        display: "flex",
        alignItems: "center",
        padding: "0 8px",
        fontSize: "12px",
        whiteSpace: "nowrap",
        overflow: "hidden",
        textOverflow: "ellipsis",
        opacity: phase.parent_id ? 0.8 : 1, // Make child phases slightly transparent
      };
    },
    getMilestoneStyle(milestone) {
      if (!milestone.date) return {};

      // Calculate position for milestone marker
      const date = moment(milestone.date);
      const timelineStart = moment(this.timelineMonths[0], "MMM YYYY");
      const left = date.diff(timelineStart, "days") * (100 / 30);

      return {
        left: left + "px",
        position: "absolute",
        top: "0",
        zIndex: "1",
        color: "#dc3545",
        fontSize: "20px",
        cursor: "pointer",
      };
    },
    togglePhase(phase) {
      if (!phase.children?.length) return;

      if (this.expandedPhases.has(phase.id)) {
        this.expandedPhases.delete(phase.id);
      } else {
        this.expandedPhases.add(phase.id);
      }

      this.$forceUpdate();
    },
    toggleBudgetPhase(phase) {
      if (!phase.children?.length) return;

      if (this.expandedBudgetPhases.has(phase.id)) {
        this.expandedBudgetPhases.delete(phase.id);
      } else {
        this.expandedBudgetPhases.add(phase.id);
      }

      this.$forceUpdate();
    },
    handleFileChange(event) {
      this.form.document = event.target.files[0];
    },
    async fetchDocuments() {
      if (!this.projectId) return;

      try {
        const response = await request({
          method: "get",
          url: `/projects/${this.projectId}/documents`,
        });
        this.documents = response.data; // assuming you're using API resources
      } catch (err) {
        console.error("Failed to load documents", err);
      }
    },
    async deleteDocument(id) {
      const confirmDelete = confirm("Are you sure you want to delete this document?");
      if (!confirmDelete) return;

      try {
        await request({
          method: "delete",
          url: `/projects/documents/${id}`,
        });

        this.documents = this.documents.filter((doc) => doc.id !== id);
        await this.fetchDocuments();
      } catch (error) {
        console.error("Delete failed:", error);
        this.error = "Failed to delete the document.";
      }
    },
    async uploadDocument() {
      this.loading = true;
      this.error = "";
      const fd = new FormData();
      fd.append("project_id", this.projectId);
      fd.append("name", this.form.name);
      fd.append("document", this.form.document);

      console.log(fd);
      try {
        await request({
          method: "post",
          url: "/projects/documents",
          data: fd,
          headers: { "Content-Type": "multipart/form-data" },
        });

        this.successMessage = "Document uploaded successfully!";
        this.resetForm(true);
        await this.fetchDocuments();
      } catch (err) {
        this.error = err.response?.data?.message || "Upload failed.";
      } finally {
        this.loading = false;
      }
    },
    resetForm(success = false) {
      this.showModal = false;
      this.form = {
        project_id: "",
        name: "",
        document: null,
      };
      if (!success) this.error = "";
    },
    resetMessageForm() {
      this.messageForm = {
        receiver_id: null,
        receiver_type: null,
        subject: "",
        message: "",
        project_id: this.projectId,
        phase_id: null,
        file: null,
      };
      this.error = "";
      this.showMessageModal = false;
    },
    async sendMessage() {
      this.loading = true;
      this.error = "";
      const formData = new FormData();

      if (this.$global.hasRole("investor") || this.$global.hasRole("tenant")) {
        this.messageForm.receiver_id = this.project.user.id;
      }

      Object.entries(this.messageForm).forEach(([key, value]) => {
        if (value !== null) formData.append(key, value);
      });

      formData.append("project_id", this.projectId);

      try {
        await request({
          method: "post",
          url: "/chat/send",
          data: formData,
          headers: { "Content-Type": "multipart/form-data" },
        });
        this.successMessage = "Message sent successfully!";
        this.resetMessageForm();
      } catch (err) {
        this.error = err.response?.data?.errors
          ? Object.values(err.response.data.errors).flat().join(", ")
          : err.response?.data?.message || "Send failed.";
      } finally {
        this.loading = false;
      }
    },
    openEditModal(phase) {
      this.editingPhase = {
        id: phase.id,
        title: phase.title || "",
        description: phase.description || "",
        start_date: phase.start_date || "",
        end_date: phase.end_date || "",
        milestone_id: phase.milestone_id || null,
        planned_cost: phase.planned_cost || "",
        actual_cost: phase.actual_cost || "",
        date_of_expense: phase.date_of_expense || "",
        attachment: null, // always reset file input
      };
      this.showEditModal = true;
    },
    closeEditModal() {
      this.showEditModal = false;
      this.editingPhase = {
        id: null,
        title: "",
        description: "",
        start_date: "",
        end_date: "",
        milestone_id: null,
        planned_cost: "",
        actual_cost: "",
        date_of_expense: "",
        attachment: null,
      };
    },
    async savePhase() {
      try {
        let formData = new FormData();
        formData.append("title", this.editingPhase.title);
        formData.append("description", this.editingPhase.description);
        formData.append("start_date", this.editingPhase.start_date);
        formData.append("end_date", this.editingPhase.end_date);
        formData.append("milestone_id", this.editingPhase.milestone_id);
        formData.append("planned_cost", this.editingPhase.planned_cost);
        formData.append("actual_cost", this.editingPhase.actual_cost);
        formData.append("date_of_expense", this.editingPhase.date_of_expense);
        if (this.editingPhase.attachment) {
          formData.append("attachment", this.editingPhase.attachment);
        }

        await request({
          method: "post",
          url: `/phase/timeline/${this.editingPhase.id}`,
          data: formData,
          headers: { "Content-Type": "multipart/form-data" },
        });

        this.closeEditModal();
        this.fetchProjectData();
        this.successMessage = this.$t("messages.PhaseUpdated");
      } catch (error) {
        console.error("Error updating phase:", error);
        this.error = this.$t("messages.ErrorUpdatingPhase");
      }
    },
    handlePhaseFileChange(event) {
      this.editingPhase.attachment = event.target.files[0];
    },
    showSentMessages() {
        this.fetchMessages({ sentOnly: true });
    },
    resetInviteForm() {
        this.showInviteModal = false;
        this.inviteForm = {
            role_id: null,
            phone_number: "",
            project_id: this.projectId,
            country_code: ""
        };
        this.inviteError = "";
        this.isValidPhone = false;
    },
    async sendInvite() {
        this.inviteLoading = true;
        this.inviteError = "";

        try {
            await request({
                method: "post",
                url: `/users`,
                data: {
                    role_id: this.inviteForm.role_id,
                    phone_number: this.inviteForm.phone_number,
                    country_code: this.inviteForm.country_code,
                    project_ids: [this.projectId]
                }
            });

            this.$bvToast.toast(this.$t("messages.InviteSentSuccessfully"), {
                title: this.$t("title.Success"),
                variant: "success",
                solid: true
            });

            this.resetInviteForm();
        } catch (err) {
            this.inviteError = err.response?.data?.message || this.$t("messages.InviteFailed");
            if (err.response?.data?.errors) {
                this.formErrors = new Error(err.response.data.errors);
            }
        } finally {
            this.inviteLoading = false;
        }
    },
    resetError() {
        this.formErrors = new Error({});
    },
    onPhoneInput(formattedNumber, input) {
        this.phone.inputValue = input?.nationalNumber || "";
        if (input && input.nationalNumber) {
            setTimeout(() => {
                if (formattedNumber.startsWith("+61")) {
                    input.countryCallingCode = 61;
                    input.countryCode = "AU";
                }
                this.inviteForm.phone_number = input?.nationalNumber || "";
                this.inviteForm.country_code = `+${input.countryCallingCode}`;
            }, 0);
        }
    },
    async fetchRoles() {
        try {
            const response = await request({
                method: "get",
                url: `/user/roles`,
            });
            const { data } = response;
            this.roles = data.filter(role => role.name !== 'entrepreneur');

            console.log(this.roles);
        } catch (error) {
            console.error("Failed to fetch roles:", error);
        }
    },
},
  created() {
    this.projectId = this.$route.params.id;
    this.fetchProjectData();
    this.fetchMessages();
    this.fetchDocuments();
    this.fetchRoles();
  },
  watch: {
    selectedRole(newVal) {
      if (!this.isUpdatingRole) {
        this.fetchMessages();
      }
    },
  },
};
</script>

<style scoped>
.card {
  background: white;
  border: none;
}

.project-meta .badge {
  font-weight: 500;
  padding: 0.5em 1em;
}

.card {
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-2px);
}

.bg-light {
  background-color: #f8f9fa !important;
}

.timeline-chart {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.timeline-grid-wrapper {
  overflow-x: auto;
  width: 100%;
}

.timeline-grid {
  position: relative;
  min-height: 200px;
}

.timeline-months {
  border-bottom: 1px solid #eee;
  position: sticky;
  top: 0;
  background: white;
  z-index: 1;
}

.timeline-month-cell {
  padding: 10px;
  font-size: 0.9em;
  color: #666;
  min-width: 100px;
}

.timeline-grid-line {
  position: absolute;
  top: 40px;
  bottom: 0;
  width: 1px;
  background: #eee;
}

.timeline-labels {
  position: sticky;
  left: 0;
  background: white;
  z-index: 2;
  border-right: 1px solid #eee;
  padding-right: 10px;
}

.phase-title {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.child-title {
  padding-left: 20px;
  color: #6c757d;
  font-size: 0.9em;
}

.child-row {
  background-color: #f8f9fa;
}

.timeline-bar.child-phase {
  opacity: 0.8;
  margin-left: 20px;
}

.phase-toggle {
  cursor: pointer;
  color: #6c757d;
  transition: all 0.2s ease;
  font-size: 14px;
}

.phase-toggle:hover {
  color: #007bff;
  transform: scale(1.1);
}

.phase-toggle.expanded {
  transform: rotate(180deg);
  color: #007bff;
}

.timeline-row {
  position: relative;
  border-bottom: 1px solid #f5f5f5;
}

.timeline-bar {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.2s;
  z-index: 1;
}

.timeline-bar.has-children {
  cursor: pointer;
  position: relative;
}

.timeline-bar.has-children::after {
  content: "▼";
  position: absolute;
  right: 8px;
  font-size: 10px;
  opacity: 0.7;
}

.timeline-bar.has-children.expanded::after {
  content: "▲";
}

.timeline-bar:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}

.main-phase {
  background-color: #007bff !important;
}

.child-phase {
  /*background-color: #6c757d !important;*/
  margin-left: 20px;
  opacity: 0.8;
}

.timeline-grid {
  padding-bottom: 60px;
}

.timeline-milestones {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 100%;
  pointer-events: none;
}

.timeline-milestone {
  position: absolute;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 20px;
  pointer-events: auto;
}

.milestone-title {
  position: absolute;
  top: -6px;
  white-space: nowrap;
  background: rgba(255, 255, 255, 0.9);
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
  color: #333;
  transform: translateX(-50%);
  z-index: 2;
}

.timeline-milestone:hover .milestone-title {
  font-weight: bold;
}

.timeline-phase {
  position: relative;
  height: 40px;
  border-bottom: 1px solid #f5f5f5;
}

.budget-toggle {
  cursor: pointer;
  color: #6c757d;
  transition: all 0.2s ease;
  font-size: 14px;
}

.budget-toggle:hover {
  color: #007bff;
  transform: scale(1.1);
}

.budget-toggle.expanded {
  transform: rotate(180deg);
  color: #007bff;
}

.child-budget-row {
  background-color: #f8f9fa;
}

.child-budget-row .child-title {
  color: #6c757d;
  font-size: 0.9em;
}

.table > :not(caption) > * > * {
  padding: 0.75rem;
}

.table tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.02);
}

.child-budget-row:hover {
  background-color: #f1f3f5;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1040;
}

.modal {
  z-index: 1050;
}

.phase-toggle {
  cursor: pointer;
  transition: transform 0.2s;
}

.phase-toggle.expanded {
  transform: rotate(180deg);
}

.btn-outline-primary {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>
