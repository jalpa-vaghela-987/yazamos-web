<template>
  <div class="container py-4">
    <h3 class="mb-4 fw-bold">Project Messages</h3>

    <div v-for="message in messages" :key="message.id" class="card shadow-sm mb-3 border-0 message-card"
      @click="openMessage(message)">
      <div class="card-body d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
            {{ message.sender?.name?.charAt(0) || '?' }}
          </div>
          <div>
            <h6 class="mb-1">{{ message.subject }}</h6>
            <small class="text-muted">{{ message.sender?.name || 'Unknown Sender' }}</small>
          </div>
        </div>
        <small class="text-muted">{{ formatDate(message.created_at) }}</small>
      </div>
    </div>

    <div ref="scrollAnchor" style="height: 1px"></div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ selectedMessage?.subject }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
          </div>
          <div class="modal-body">
            <p>
              <strong>From:</strong> {{ selectedMessage?.sender?.name || 'Unknown' }} <br />
              <strong>Date:</strong> {{ formatDate(selectedMessage?.created_at) }}
            </p>
            <hr />
            <p class="mb-3">{{ selectedMessage?.message }}</p>

            <div v-if="selectedMessage?.file" class="mt-3">
              <strong>Attached File:</strong><br />

              <template v-if="isImage(selectedMessage.file)">
                <a :href="selectedMessage.file" target="_blank">
                  <img :src="selectedMessage.file" alt="Attachment" class="img-fluid rounded border"
                    style="max-height: 300px" @error="handleImageError($event)" />
                </a>
              </template>

              <template v-else>
                <a :href="selectedMessage.file" target="_blank" class="btn btn-outline-primary" download>
                 Download Attachment
                </a>
              </template>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { request } from '@/Util/Request';
import { Modal } from 'bootstrap';

export default {
  data() {
    return {
      messages: [],
      page: 1,
      loading: false,
      selectedMessage: null,
      observer: null,
    };
  },
  mounted() {
    this.loadMessages();
    this.observer = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting && !this.loading) {
        this.page++;
        this.loadMessages();
      }
    });
    this.observer.observe(this.$refs.scrollAnchor);
  },
  beforeUnmount() {
    if (this.observer) this.observer.disconnect();
  },
  methods: {
    handleImageError(event) {
      event.target.src = require('@/assets/default.png');
    },
    async loadMessages() {
      this.loading = true;
      try {
        const res = await request({
          method: 'get',
          url: `/project/messages/${this.$route.params.id}?page=${this.page}`,
        });

        const newMessages = res.data.messages;

        if (this.page === 1) {
          this.messages = newMessages; // Initial load
        } else {
          // Only add if not already present
          const existingIds = new Set(this.messages.map((m) => m.id));
          const filtered = newMessages.filter((m) => !existingIds.has(m.id));
          this.messages.push(...filtered);
        }

        // Optional: stop loading more if there's no more data
        if (!newMessages.length) {
          this.observer.disconnect();
        }

      } catch (e) {
        console.error('Error loading messages', e);
      } finally {
        this.loading = false;
      }
    },
    openMessage(msg) {
      this.selectedMessage = msg;
      const modal = new Modal(document.getElementById('messageModal'));
      modal.show();
    },
    formatDate(date) {
      return new Date(date).toLocaleString();
    },
    isImage(fileUrl) {
      const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
      const ext = fileUrl.split('.').pop().toLowerCase();
      return imageExtensions.includes(ext);
    },
  },
};
</script>

<style scoped>
.message-card {
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.message-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.avatar {
  width: 40px;
  height: 40px;
  font-weight: bold;
  font-size: 1.2rem;
}
</style>
