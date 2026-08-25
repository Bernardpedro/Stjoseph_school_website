<template>
  <footer class="bg-white dark:bg-gray-900">
    <div class="mx-auto w-full max-w-screen-xl">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 px-4 py-6 lg:py-8">
        <div>
          <h2 class="mb-6 text-sm font-semibold text-[#1D4ED8] uppercase dark:text-blue-400">
            {{ $t('footer.collaborators') }}
          </h2>
          <ul class="text-[#1D4ED8] dark:text-blue-400 font-medium">
            <li class="mb-4">
              <a href="https://www.diocesekabgayi.org/" target="_blank" class="hover:underline">DIOCESE DE KABGAYI - ORATE IN VERITATE</a>
            </li>
            <li class="mb-4">
              <a href="https://www.bbs-lahnstein.de/" target="_blank" class="hover:underline">Berufsbildende Schule Lahnstein</a>
            </li>
            <li class="mb-4">
              <a href="http://rwa.rlp-ruanda.de/de/home/" target="_blank" class="hover:underline">Partnerschaftsverein Rheinland-Pfalz/Ruanda e. V.</a>
            </li>
            <li class="mb-4">
              <a href="https://x.com/Fr_Ramon_K_TVET?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor" target="_blank" class="hover:underline">Fr Ramon Kabuga TSS </a>
            </li>
          </ul>
        </div>

        <div>
          <h2 class="mb-6 text-sm font-semibold text-[#1D4ED8] uppercase dark:text-blue-400">
            {{ $t('footer.mapLocation') }}
          </h2>
          <ul class="text-[#1D4ED8] dark:text-blue-400 font-medium">
            <li class="mb-4">
              <button
                @click="openGoogleMaps"
                class="hover:underline text-left cursor-pointer bg-transparent border-none p-0 text-[#1D4ED8] dark:text-blue-400"
              >
                {{ $t('footer.googleMaps') }}
              </button>
            </li>
            <li class="mb-4">
              <button
                @click="openOpenStreetMap"
                class="hover:underline text-left cursor-pointer bg-transparent border-none p-0 text-[#1D4ED8] dark:text-blue-400"
              >
                {{ $t('footer.openStreetMap') }}
              </button>
            </li>
            <li class="mb-4">
              <button
                @click="toggleMapModal"
                class="hover:underline text-left cursor-pointer bg-transparent border-none p-0 text-[#1D4ED8] dark:text-blue-400"
              >
                {{ $t('footer.viewEmbeddedMap') }}
              </button>
            </li>
          </ul>
        </div>

        <div>
          <h2 class="mb-6 text-sm font-semibold text-[#1D4ED8] uppercase dark:text-blue-400">
            {{ $t('footer.ourLocation') }}
          </h2>
          <div class="map-container">
            <iframe
              :src="embedMapUrl"
              width="100%"
              height="200"
              style="border:0;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              class="rounded-lg"
            ></iframe>
          </div>
        </div>
      </div>

      <div class="px-4 py-6 bg-gray-100 dark:bg-gray-700 md:flex md:items-center md:justify-between">
        <span class="text-sm text-[#1D4ED8] dark:text-blue-400 sm:text-center">
          © {{ year }}
          <a href="/" class="hover:underline">St Joseph TSS Nzuki</a>
          . All Rights Reserved.
        </span>
        <div class="flex mt-4 sm:justify-center md:mt-0 space-x-5 rtl:space-x-reverse">
          <a href="https://signal.me/#p/+250783138446" class="social-link" target="_blank">
            <img src="https://res.cloudinary.com/dck2vzccq/image/upload/v1752079087/signal_g9aabs.jpg" alt="Signal" class="social-icon" />
          </a>
          <a href="https://x.com/@tssnzuki" target="_blank" class="social-link">
            <img src="https://res.cloudinary.com/dck2vzccq/image/upload/v1752774382/xIcon_gzkqse.png" alt="X (Twitter)" class="social-icon" />
          </a>
          <a href="https://threema.id/UNHZY9DX" target="_blank" class="social-link">
            <img src="https://res.cloudinary.com/dck2vzccq/image/upload/v1752079103/Threema_iwilfe.png" alt="Threema" class="social-icon" />
          </a>
          <a href="https://wa.me/250783138446" target="_blank" class="social-link">
            <img src="https://res.cloudinary.com/dck2vzccq/image/upload/v1752774382/whatsApp_m6nmnz.png" alt="WhatsApp" class="social-icon" />
          </a>
        </div>
      </div>
    </div>

    <div
      v-if="showMapModal"
      class="modal-overlay"
      @click="toggleMapModal"
    >
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Nzuki Vocational Training Centre</h3>
          <button @click="toggleMapModal" class="modal-close">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <iframe
            :src="embedMapUrl"
            width="100%"
            height="400"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            class="rounded-lg"
          ></iframe>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'FooterWithMaps',
  data() {
    return {
      showMapModal: false,
      year: new Date().getFullYear(),
      latitude: -2.223788,
      longitude: 29.613161,
      embedMapUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.123456789!2d29.613161!3d-2.223788!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMsKwMTMnMjUuNiJTIDI5wrAzNic0Ny40IkU!5e0!3m2!1sen!2srw!4v1234567890123!5m2!1sen!2srw',
    }
  },
  methods: {
    openGoogleMaps() {
      const googleMapsUrl = `https://www.google.com/maps/place/Nzuki+Vocational+Training+Centre/@${this.latitude},${this.longitude},17z/data=!3m1!4b1!4m6!3m5!1s0x0:0x0!8m2!3d${this.latitude}!4d${this.longitude}!16s%2Fg%2F11p0866ed7`;
      window.open(googleMapsUrl, '_blank');
    },
    openOpenStreetMap() {
      const osmUrl = `https://www.openstreetmap.org/?mlat=${this.latitude}&mlon=${this.longitude}&zoom=17`;
      window.open(osmUrl, '_blank');
    },
    toggleMapModal() {
      this.showMapModal = !this.showMapModal;
    },
    getDirections() {
      const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${this.latitude},${this.longitude}`;
      window.open(directionsUrl, '_blank');
    }
  },
}
</script>

<style scoped>
.map-container {
  border-radius: 0.5rem;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.social-icon {
  height: 3rem;
  width: 3rem;
  border-radius: 50%;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.social-link:hover .social-icon {
  transform: scale(1.1);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background-color: white;
  border-radius: 0.5rem;
  max-width: 800px;
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1d4ed8;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  color: #1d4ed8;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 0.25rem;
}

.modal-close:hover {
  color: #1e40af;
}

.modal-body {
  padding: 1.5rem;
}

:global(html.dark) .modal-content {
  background-color: #1f2937;
}

:global(html.dark) .modal-header {
  border-bottom-color: #374151;
}

:global(html.dark) .modal-title,
:global(html.dark) .modal-close {
  color: #60a5fa;
}

@media (max-width: 768px) {
  .modal-overlay {
    padding: 0.5rem;
  }

  .modal-content {
    max-height: 95vh;
  }

  .modal-body iframe {
    height: 300px;
  }
}

button {
  font-family: inherit;
  font-size: inherit;
  line-height: inherit;
}
</style>
