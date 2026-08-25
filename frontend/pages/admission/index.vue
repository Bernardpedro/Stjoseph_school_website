<template>
  <div class="admission-page bg-[#F4F7FB] text-[#0B1F3A] dark:bg-gray-900 dark:text-slate-100">
    <!-- SUCCESS: confirmation + school contact only -->
    <section v-if="success" class="min-h-[60vh] py-12 md:py-16 px-4 sm:px-6">
      <div class="max-w-3xl mx-auto animate-[fadeIn_0.4s_ease-out]">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-[0_20px_50px_-20px_rgba(11,31,58,0.35)] ring-1 ring-blue-100 dark:ring-gray-700 overflow-hidden">
          <div class="h-1.5 bg-gradient-to-r from-[#0B1F3A] via-[#1D4ED8] to-[#FBBF24]" />
          <div class="p-8 sm:p-10 text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-blue-50 dark:bg-blue-900/40 text-[#1D4ED8] flex items-center justify-center mb-4 ring-4 ring-blue-50/60 dark:ring-blue-900/20">
              <span class="material-symbols-outlined text-4xl">verified</span>
            </div>
            <h1 class="font-display text-2xl md:text-3xl font-bold text-[#0B1F3A] dark:text-white mb-2">
              {{ $t('admission.successTitle') }}
            </h1>
            <p class="text-slate-600 dark:text-slate-300 max-w-md mx-auto">{{ success }}</p>
            <div class="mt-6 max-w-md mx-auto text-left rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 px-4 py-3">
              <p class="font-semibold text-[#0B1F3A] dark:text-white">{{ $t('admission.payFeesTitle') }}</p>
              <p class="mt-2 text-sm text-slate-700 dark:text-slate-200">{{ $t('admission.payFeesBody') }}</p>
              <p class="mt-2 text-sm text-[#0B1F3A] dark:text-white">
                <span class="font-semibold">{{ $t('admission.payFeesAmount') }}</span>
              </p>
              <p class="mt-1 text-sm">
                <a href="tel:+250787425383" class="font-semibold text-[#1D4ED8] hover:underline">0787 425 383</a>
                <span class="text-slate-600 dark:text-slate-300"> — {{ $t('admission.payFeesName') }}</span>
              </p>
              <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $t('admission.payFeesCallFirst') }}</p>
              <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $t('admission.payFeesCheckAccepted') }}</p>
            </div>
            <div v-if="submittedNumber" class="mt-6 max-w-md mx-auto text-left">
              <p class="text-xs font-semibold tracking-widest uppercase text-[#1D4ED8] mb-1">
                {{ $t('admission.registrationNumber') }}
              </p>
              <div class="flex items-center gap-2 rounded-xl border border-blue-100 dark:border-gray-700 bg-[#F8FBFF] dark:bg-gray-900 px-3 py-2">
                <code class="flex-1 font-mono text-base sm:text-lg font-bold text-[#0B1F3A] dark:text-white break-all">
                  {{ submittedNumber }}
                </code>
                <button type="button" class="shrink-0 text-sm font-semibold text-[#1D4ED8]" @click="copyNumber(submittedNumber)">
                  {{ copied ? $t('admission.copied') : $t('admission.copyNumber') }}
                </button>
              </div>
              <p class="mt-2 text-sm text-slate-500">{{ $t('admission.saveNumberHint') }}</p>
            </div>
          </div>
        </div>

        <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-blue-100 dark:ring-gray-700 p-6 sm:p-8">
          <h2 class="font-display text-xl font-bold text-[#0B1F3A] dark:text-white mb-1">
            {{ $t('contact.contactDetails') }}
          </h2>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">{{ $t('admission.successContact') }}</p>

          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-[#1D4ED8] mt-0.5 flex-shrink-0">location_on</span>
              <div>
                <p class="font-semibold text-[#0B1F3A] dark:text-white">{{ $t('contact.physicalAddress') }}</p>
                <p class="text-slate-600 dark:text-slate-300">Kirwa Village, Bihembe Cell</p>
                <p class="text-slate-600 dark:text-slate-300">Kabagali Sector, Ruhango District</p>
                <p class="text-slate-600 dark:text-slate-300">Southern Province, Rwanda</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-[#1D4ED8] mt-0.5 flex-shrink-0">call</span>
              <div>
                <p class="font-semibold text-[#0B1F3A] dark:text-white">{{ $t('contact.phoneNumbers') }}</p>
                <a href="tel:+250783138446" class="text-[#1D4ED8] hover:underline">+250 783 138 446</a>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-[#1D4ED8] mt-0.5 flex-shrink-0">mail</span>
              <div>
                <p class="font-semibold text-[#0B1F3A] dark:text-white">{{ $t('contact.emailAddress') }}</p>
                <a href="mailto:tssnzuki@gmail.com" class="text-[#1D4ED8] hover:underline">tssnzuki@gmail.com</a>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span class="material-symbols-outlined text-[#1D4ED8] mt-0.5 flex-shrink-0">schedule</span>
              <div>
                <p class="font-semibold text-[#0B1F3A] dark:text-white">{{ $t('contact.officeHours') }}</p>
                <p class="text-slate-600 dark:text-slate-300">{{ $t('contact.weekdays') }}</p>
                <p class="text-slate-600 dark:text-slate-300">{{ $t('contact.saturday') }}</p>
                <p class="text-slate-600 dark:text-slate-300">{{ $t('contact.sunday') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <template v-else>
    <!-- HERO -->
    <section class="relative overflow-hidden min-h-[480px] md:min-h-[560px] flex items-center">
      <div class="absolute inset-0">
        <img
          :src="images.campus"
          alt=""
          class="w-full h-full object-cover scale-105"
        />
        <div class="absolute inset-0 bg-gradient-to-br from-[#0B1F3A]/95 via-[#123A6B]/88 to-[#1D4ED8]/75" />
        <div class="hero-pattern absolute inset-0 opacity-30" />
      </div>

      <div class="relative z-10 max-w-7xl mx-auto w-full px-4 sm:px-6 py-12 md:py-16">
        <div class="flex flex-col md:flex-row items-center md:items-start md:justify-between gap-8">
          <div class="max-w-2xl text-center md:text-left">
            <span class="inline-flex items-center gap-2 bg-[#FBBF24] text-[#0B1F3A] font-semibold tracking-[0.14em] uppercase text-[11px] px-4 py-1.5 rounded-full mb-4 shadow-sm">
              <span class="w-1.5 h-1.5 rounded-full bg-[#1D4ED8] animate-pulse" />
              {{ $t('admission.heroBadge') }}
            </span>
            <h1 class="font-display text-3xl sm:text-4xl md:text-5xl lg:text-[52px] font-extrabold text-white leading-[1.1] tracking-tight mb-4">
              {{ $t('admission.heroTitle') }}
            </h1>
            <p class="text-base md:text-lg text-blue-100/90 mb-7 max-w-xl mx-auto md:mx-0 leading-relaxed">
              {{ $t('admission.heroSubtitle') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
              <button v-if="applicationsOpen" type="button" class="btn-gold" @click="scrollToForm">
                <span class="material-symbols-outlined text-[20px]">edit_document</span>
                {{ $t('admission.applyNow') }}
              </button>
              <button type="button" class="btn-blue" @click="scrollToTrack">
                <span class="material-symbols-outlined text-[20px]">search</span>
                {{ $t('admission.trackApplication') }}
              </button>
            </div>
          </div>

          <div class="hidden md:block w-full max-w-md">
            <div class="relative rounded-2xl overflow-hidden shadow-2xl ring-2 ring-white/20 transition-transform duration-300 hover:scale-[1.02]">
              <img :src="images.workshop" alt="Students in a technical workshop" class="w-full h-72 object-cover" />
              <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#0B1F3A] to-transparent p-5">
                <p class="text-white font-display font-bold text-lg">Saint Joseph TSS Nzuki</p>
                <p class="text-blue-200 text-sm">{{ $t('contact.technicalSchool') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- LEVEL DOCUMENTS + APPLICATION FORM -->
    <section id="application-form" class="py-12 md:py-16 px-4 sm:px-6 bg-[#EEF3FA] dark:bg-gray-950 scroll-mt-20">
      <div class="max-w-6xl mx-auto">
        <div id="level-docs" class="scroll-mt-16">
          <SchoolRequirements compact />
        </div>

        <div class="max-w-3xl mx-auto mt-3">
        <div class="text-center mb-5">
          <p class="text-[11px] font-semibold tracking-[0.16em] uppercase text-[#1D4ED8] mb-1">
            {{ $t('admission.applicationForm') }}
          </p>
          <h2 class="font-display text-2xl md:text-3xl font-bold text-[#0B1F3A] dark:text-white leading-tight mb-2">
            {{ $t('admission.title') }}
          </h2>
          <p class="text-slate-600 dark:text-slate-300 max-w-xl mx-auto">{{ $t('admission.formIntro') }}</p>
          <p
            v-if="pageNotice"
            class="mt-4 text-sm text-[#0B1F3A] dark:text-blue-100 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3"
          >
            {{ pageNotice }}
          </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-[0_20px_50px_-20px_rgba(11,31,58,0.35)] ring-1 ring-blue-100 dark:ring-gray-700 overflow-hidden transition-shadow duration-300 hover:shadow-[0_24px_60px_-20px_rgba(11,31,58,0.4)]">
          <div class="h-1.5 bg-gradient-to-r from-[#0B1F3A] via-[#1D4ED8] to-[#FBBF24]" />

          <!-- Step tabs -->
          <div class="grid grid-cols-4 border-b border-blue-50 dark:border-gray-700">
            <button
              v-for="(label, i) in formSteps"
              :key="label"
              type="button"
              class="relative py-3.5 text-xs sm:text-sm font-semibold transition-colors duration-200"
              :class="formStep === i + 1 ? 'text-[#1D4ED8]' : formStep > i + 1 ? 'text-[#0B1F3A] dark:text-slate-200' : 'text-slate-400'"
              @click="goToStep(i + 1)"
            >
              <span class="inline-flex items-center justify-center gap-1.5">
                <span
                  class="hidden sm:inline-flex w-5 h-5 rounded-full text-[10px] items-center justify-center transition-colors duration-200"
                  :class="formStep === i + 1 ? 'bg-[#1D4ED8] text-white' : formStep > i + 1 ? 'bg-[#0B1F3A] text-white' : 'bg-slate-100 text-slate-400'"
                >{{ i + 1 }}</span>
                {{ label }}
              </span>
              <span
                v-if="formStep === i + 1"
                class="absolute bottom-0 left-4 right-4 h-0.5 bg-[#1D4ED8] rounded-full transition-all duration-200"
              />
            </button>
          </div>

          <form v-if="applicationsOpen" class="p-5 sm:p-7 space-y-4" @submit.prevent="submit">
            <p class="text-xs font-semibold tracking-widest uppercase text-slate-400">
              {{ $t('admission.stepOf', { current: formStep, total: 4 }) }}
            </p>

            <!-- Step 1: Student -->
            <div v-show="formStep === 1" class="space-y-4">
              <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                  <span class="label">{{ $t('admission.studentName') }}</span>
                  <input v-model="form.student_name" required type="text" class="field" :placeholder="$t('admission.studentName')" />
                </label>
                <label class="block">
                  <span class="label">{{ $t('admission.dateOfBirth') }}</span>
                  <input v-model="form.date_of_birth" type="date" class="field" />
                </label>
              </div>
              <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                  <span class="label">{{ $t('admission.gender') }}</span>
                  <select v-model="form.gender" class="field">
                    <option value="">{{ $t('admission.selectGender') }}</option>
                    <option value="Male">{{ $t('admission.male') }}</option>
                    <option value="Female">{{ $t('admission.female') }}</option>
                  </select>
                </label>
                <label class="block">
                  <span class="label">{{ $t('admission.province') }}</span>
                  <select v-model="form.province" class="field" :disabled="loadingLocations" @change="onProvinceChange">
                    <option value="">{{ $t('admission.selectProvince') }}</option>
                    <option v-for="p in rwandaLocations" :key="p.code" :value="p.name">{{ p.name }}</option>
                  </select>
                </label>
              </div>
              <label class="block">
                <span class="label">{{ $t('admission.district') }}</span>
                <select v-model="form.district" class="field" :disabled="!form.province">
                  <option value="">{{ form.province ? $t('admission.selectDistrict') : $t('admission.chooseProvinceFirst') }}</option>
                  <option v-for="d in availableDistricts" :key="d" :value="d">{{ d }}</option>
                </select>
              </label>
            </div>

            <!-- Step 2: Level then Program -->
            <div v-show="formStep === 2" class="space-y-4">
              <label class="block">
                <span class="label">{{ $t('admission.level') }}</span>
                <select v-model="form.level" class="field" @change="onLevelChange">
                  <option value="">{{ $t('admission.selectLevel') }}</option>
                  <option v-for="opt in levels" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
              </label>
              <label class="block">
                <span class="label">{{ $t('admission.program') }}</span>
                <select v-model="form.program" class="field" :disabled="!form.level">
                  <option value="">{{ form.level ? $t('admission.selectProgram') : $t('admission.chooseLevelFirst') }}</option>
                  <option v-for="opt in availablePrograms" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
              </label>
              <div class="rounded-xl border border-blue-100 dark:border-gray-700 bg-[#F8FBFF] dark:bg-gray-900 p-4 space-y-3">
                <label class="block">
                  <span class="label">{{ $t('admission.previousSchool') }}</span>
                  <input
                    v-model="form.previous_school"
                    type="text"
                    class="field"
                    :placeholder="$t('admission.schoolNameTransfer')"
                  />
                </label>
                <label class="block">
                  <span class="label">{{ $t('admission.uploadBulletin') }}</span>
                  <span class="upload-zone">
                    <span class="material-symbols-outlined text-[#1D4ED8]">upload_file</span>
                    <span class="text-sm text-slate-600 dark:text-slate-300">{{ $t('admission.uploadHint') }}</span>
                    <input type="file" multiple accept="image/*,application/pdf" class="sr-only" @change="onBulletinFiles" />
                  </span>
                  <p v-if="bulletinFiles.length" class="text-xs text-[#1D4ED8] mt-1 font-medium">
                    {{ $t('common.filesSelected', { count: bulletinFiles.length }) }}
                  </p>
                </label>
              </div>
            </div>

            <!-- Step 3: Family -->
            <div v-show="formStep === 3" class="space-y-4">
              <div class="grid sm:grid-cols-2 gap-4">
                <label class="block">
                  <span class="label">{{ $t('admission.parentName') }}</span>
                  <input v-model="form.parent_name" required type="text" class="field" :placeholder="$t('admission.parentName')" />
                </label>
                <label class="block">
                  <span class="label">{{ $t('admission.phone') }}</span>
                  <input v-model="form.parent_phone" required type="tel" class="field" :placeholder="$t('admission.phone')" />
                </label>
              </div>
            </div>

            <!-- Step 4: Review -->
            <div v-show="formStep === 4" class="space-y-3.5">
              <div>
                <h3 class="font-display text-lg font-bold text-[#0B1F3A] dark:text-white">{{ $t('admission.reviewTitle') }}</h3>
                <p class="text-sm text-slate-500 mt-0.5">{{ $t('admission.reviewHint') }}</p>
              </div>

              <div class="rounded-xl border border-blue-100 dark:border-gray-700 overflow-hidden">
                <div class="flex items-center justify-between bg-[#F8FBFF] dark:bg-gray-900 px-4 py-2.5 border-b border-blue-50 dark:border-gray-700">
                  <p class="text-xs font-semibold tracking-widest uppercase text-[#1D4ED8]">{{ $t('admission.stepStudent') }}</p>
                  <button type="button" class="text-xs font-semibold text-[#1D4ED8] hover:underline" @click="formStep = 1">{{ $t('admission.reviewChange') }}</button>
                </div>
                <dl class="grid sm:grid-cols-2 gap-x-4 gap-y-2.5 p-4 text-sm">
                  <div><dt class="text-slate-400">{{ $t('admission.studentName') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ display(form.student_name) }}</dd></div>
                  <div><dt class="text-slate-400">{{ $t('admission.dateOfBirth') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ display(form.date_of_birth) }}</dd></div>
                  <div><dt class="text-slate-400">{{ $t('admission.gender') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ genderLabel }}</dd></div>
                  <div><dt class="text-slate-400">{{ $t('admission.province') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ display(form.province) }}</dd></div>
                  <div class="sm:col-span-2"><dt class="text-slate-400">{{ $t('admission.district') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ display(form.district) }}</dd></div>
                </dl>
              </div>

              <div class="rounded-xl border border-blue-100 dark:border-gray-700 overflow-hidden">
                <div class="flex items-center justify-between bg-[#F8FBFF] dark:bg-gray-900 px-4 py-2.5 border-b border-blue-50 dark:border-gray-700">
                  <p class="text-xs font-semibold tracking-widest uppercase text-[#1D4ED8]">{{ $t('admission.stepProgram') }}</p>
                  <button type="button" class="text-xs font-semibold text-[#1D4ED8] hover:underline" @click="formStep = 2">{{ $t('admission.reviewChange') }}</button>
                </div>
                <dl class="grid sm:grid-cols-2 gap-x-4 gap-y-2.5 p-4 text-sm">
                  <div><dt class="text-slate-400">{{ $t('admission.level') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ levelLabel }}</dd></div>
                  <div><dt class="text-slate-400">{{ $t('admission.program') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ programLabel }}</dd></div>
                  <div class="sm:col-span-2"><dt class="text-slate-400">{{ $t('admission.previousSchool') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ display(form.previous_school) }}</dd></div>
                  <div class="sm:col-span-2"><dt class="text-slate-400">{{ $t('admission.uploadBulletin') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ fileNames(bulletinFiles) }}</dd></div>
                </dl>
              </div>

              <div class="rounded-xl border border-blue-100 dark:border-gray-700 overflow-hidden">
                <div class="flex items-center justify-between bg-[#F8FBFF] dark:bg-gray-900 px-4 py-2.5 border-b border-blue-50 dark:border-gray-700">
                  <p class="text-xs font-semibold tracking-widest uppercase text-[#1D4ED8]">{{ $t('admission.stepFamily') }}</p>
                  <button type="button" class="text-xs font-semibold text-[#1D4ED8] hover:underline" @click="formStep = 3">{{ $t('admission.reviewChange') }}</button>
                </div>
                <dl class="grid sm:grid-cols-2 gap-x-4 gap-y-2.5 p-4 text-sm">
                  <div><dt class="text-slate-400">{{ $t('admission.parentName') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ display(form.parent_name) }}</dd></div>
                  <div><dt class="text-slate-400">{{ $t('admission.phone') }}</dt><dd class="font-medium text-[#0B1F3A] dark:text-white">{{ display(form.parent_phone) }}</dd></div>
                </dl>
              </div>
            </div>

            <p v-if="error" class="text-sm text-red-600 bg-red-50 border border-red-100 rounded-lg px-3 py-2">{{ error }}</p>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
              <button
                v-if="formStep > 1"
                type="button"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-[#0B1F3A] dark:text-slate-200 border border-slate-200 dark:border-gray-600 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors"
                @click="formStep--"
              >
                {{ $t('admission.back') }}
              </button>
              <span v-else />
              <button
                v-if="formStep < 4"
                type="button"
                class="btn-blue sm:ml-auto"
                @click="nextStep"
              >
                {{ formStep === 3 ? $t('admission.reviewTitle') : $t('admission.next') }}
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
              </button>
              <button
                v-else
                type="submit"
                class="btn-gold sm:ml-auto disabled:opacity-60"
                :disabled="submitting"
              >
                {{ submitting ? $t('admission.sending') : $t('admission.submit') }}
              </button>
            </div>
          </form>
          <div v-else class="p-8 sm:p-10 text-center">
            <h3 class="font-display text-2xl font-bold text-[#0B1F3A] dark:text-white mb-2">Applications are closed</h3>
            <p class="text-slate-600 dark:text-slate-300">{{ pageNotice || 'Please check back later or contact the school for more information.' }}</p>
          </div>
        </div>
        </div>
      </div>
    </section>
    </template>

    <!-- TRACK APPLICATION -->
    <section id="track-application" class="py-12 md:py-16 px-4 sm:px-6 bg-[#F4F7FB] dark:bg-gray-950 scroll-mt-20">
      <div class="max-w-xl mx-auto">
        <div class="text-center mb-5">
          <p class="text-[11px] font-semibold tracking-[0.16em] uppercase text-[#1D4ED8] mb-1">
            {{ $t('admission.trackKicker') }}
          </p>
          <h2 class="font-display text-2xl md:text-3xl font-bold text-[#0B1F3A] dark:text-white leading-tight mb-2">
            {{ $t('admission.trackTitle') }}
          </h2>
          <p class="text-slate-600 dark:text-slate-300">{{ $t('admission.trackIntro') }}</p>
        </div>
        <form class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-blue-100 dark:ring-gray-700 p-5 sm:p-6 space-y-3" @submit.prevent="trackApplication">
          <label class="block">
            <span class="label">{{ $t('admission.registrationNumber') }}</span>
            <input
              v-model="trackNumber"
              type="text"
              autocomplete="off"
              spellcheck="false"
              class="field font-mono uppercase"
              :placeholder="$t('admission.trackPlaceholder')"
            />
          </label>
          <p v-if="trackError" class="text-sm text-red-600">{{ trackError }}</p>
          <button type="submit" class="btn-blue w-full sm:w-auto disabled:opacity-60" :disabled="tracking">
            {{ tracking ? $t('admission.tracking') : $t('admission.trackSubmit') }}
          </button>
          <div v-if="trackResult" class="rounded-xl border border-blue-100 dark:border-gray-700 bg-[#F8FBFF] dark:bg-gray-900 p-4 space-y-2 text-sm">
            <p class="font-mono font-bold text-[#0B1F3A] dark:text-white break-all">{{ trackResult.registrationNumber }}</p>
            <p class="text-[#1D4ED8] font-semibold">{{ trackStatusLabel(trackResult.status) }}</p>
            <p class="text-slate-600 dark:text-slate-300">{{ trackMessageLabel(trackResult.status) }}</p>
            <p v-if="trackResult.submittedAt" class="text-xs text-slate-400">{{ formatTrackDate(trackResult.submittedAt) }}</p>
          </div>
        </form>
      </div>
    </section>

    <!-- FAQ -->
    <section class="py-12 md:py-16 px-4 sm:px-6 bg-white dark:bg-gray-900">
      <div class="max-w-3xl mx-auto">
        <h2 class="font-display text-2xl md:text-3xl font-bold text-[#0B1F3A] dark:text-white mb-7 text-center">
          {{ $t('admission.faqTitle') }}
        </h2>
        <div class="space-y-2.5">
          <details
            v-for="item in faqs"
            :key="item.q"
            class="group bg-[#F4F7FB] dark:bg-gray-800 border border-blue-100 dark:border-gray-700 rounded-xl p-4 transition-colors hover:border-blue-200 dark:hover:border-gray-600"
          >
            <summary class="flex justify-between items-center font-bold text-[#0B1F3A] dark:text-white cursor-pointer list-none gap-3">
              {{ item.q }}
              <span class="material-symbols-outlined text-[#1D4ED8] transition-transform group-open:rotate-180 shrink-0">expand_more</span>
            </summary>
            <p class="mt-3 text-slate-600 dark:text-slate-300 text-sm leading-relaxed pr-8">{{ item.a }}</p>
          </details>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import {
  ADMISSION_LEVELS,
  PROGRAM_LABEL_KEYS,
  programsForLevel,
  encodeAdmissionChoice,
} from '~/composables/useAdmissionPrograms'
import { formatRwandaAddress, useRwandaLocations } from '~/composables/useRwandaLocations'

definePageMeta({
  layout: 'default',
})

const { t, locale } = useI18n()
const { apiFetch } = useApi()
const {
  locations: rwandaLocations,
  loading: loadingLocations,
  loadLocations,
  districtsFor,
} = useRwandaLocations()

useHead({
  title: 'Admissions | Saint Joseph TSS Nzuki',
  link: [
    {
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css2?family=Chivo:wght@700;800&family=Hanken+Grotesk:wght@400;500;600&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap',
    },
  ],
})

const images = {
  campus: 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140950/4._dz31vg.jpg',
  workshop: 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078864/2._uc8kz0.jpg',
}

const formSteps = computed(() => [
  t('admission.stepStudent'),
  t('admission.stepProgram'),
  t('admission.stepFamily'),
  t('admission.stepReview'),
])

const levels = computed(() =>
  ADMISSION_LEVELS.map((opt) => ({
    value: opt.value,
    label: t(opt.i18n),
  }))
)

const availablePrograms = computed(() =>
  programsForLevel(form.level).map((value) => ({
    value,
    label: t(PROGRAM_LABEL_KEYS[value] || value),
  }))
)

const faqs = computed(() => [
  { q: t('admission.faqDeadlineQ'), a: t('admission.faqDeadlineA') },
  { q: t('admission.faqTrackQ'), a: t('admission.faqTrackA') },
  { q: t('admission.faqBoardingQ'), a: t('admission.faqBoardingA') },
  { q: t('admission.faqAidQ'), a: t('admission.faqAidA') },
])

const applicationsOpen = ref(true)
const pageNotice = ref('')
const submitting = ref(false)
const error = ref('')
const success = ref('')
const submittedNumber = ref('')
const copied = ref(false)
const bulletinFiles = ref([])
const formStep = ref(1)
const trackNumber = ref('')
const tracking = ref(false)
const trackError = ref('')
const trackResult = ref(null)

const form = reactive({
  student_name: '',
  date_of_birth: '',
  gender: '',
  level: '',
  program: '',
  previous_school: '',
  parent_name: '',
  parent_phone: '',
  parent_email: '',
  province: '',
  district: '',
  address: '',
  message: '',
})

const availableDistricts = computed(() => districtsFor(form.province))

const loadPageSettings = async () => {
  try {
    const res = await apiFetch('/api/admissions/settings')
    applicationsOpen.value = res?.data?.applications_open !== false
    pageNotice.value = res?.data?.notice || ''
  } catch {
    applicationsOpen.value = true
  }
}

const onBulletinFiles = (e) => {
  bulletinFiles.value = Array.from(e.target.files || [])
}

const scrollToForm = () => {
  document.getElementById('application-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const scrollToTrack = () => {
  document.getElementById('track-application')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

const copyNumber = async (value) => {
  if (!value || !import.meta.client) return
  try {
    await navigator.clipboard.writeText(value)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  } catch {
    copied.value = false
  }
}

const DATE_LOCALES = {
  en: 'en-GB',
  rw: 'rw-RW',
  sw: 'sw-TZ',
  fr: 'fr-FR',
  de: 'de-DE',
}

const formatTrackDate = (value) => {
  if (!value) return ''
  try {
    return new Intl.DateTimeFormat(DATE_LOCALES[locale.value] || locale.value, {
      dateStyle: 'medium',
      timeStyle: 'short',
    }).format(new Date(value))
  } catch {
    return value
  }
}

const trackStatusLabel = (status) => {
  const key = `admission.trackStatus.${status}`
  const translated = t(key)
  return translated === key ? (status || '') : translated
}

const trackMessageLabel = (status) => {
  const key = `admission.trackMessage.${status}`
  const translated = t(key)
  return translated === key ? (trackResult.value?.message || '') : translated
}

const trackApplication = async () => {
  trackError.value = ''
  trackResult.value = null
  const number = String(trackNumber.value || '').trim()
  if (!number) {
    trackError.value = t('admission.trackRequired')
    return
  }

  tracking.value = true
  try {
    const res = await apiFetch('/api/admissions/track', {
      query: { number },
    })
    trackResult.value = {
      registrationNumber: res?.registrationNumber || '',
      status: res?.status || '',
      submittedAt: res?.submittedAt || '',
      message: res?.message || '',
    }
  } catch (e) {
    trackError.value = e?.data?.message || e?.message || t('admission.trackFailed')
  } finally {
    tracking.value = false
  }
}

const goToStep = (n) => {
  if (n < formStep.value) formStep.value = n
}

const onProvinceChange = () => {
  if (form.district && !districtsFor(form.province).includes(form.district)) {
    form.district = ''
  }
}

const onLevelChange = () => {
  if (form.program && !programsForLevel(form.level).includes(form.program)) {
    form.program = ''
  }
}

const nextStep = () => {
  error.value = ''
  if (formStep.value === 1 && !form.student_name.trim()) {
    error.value = t('admission.studentName')
    return
  }
  if (formStep.value === 2 && (!form.level || !form.program)) {
    error.value = form.level ? t('admission.selectProgram') : t('admission.selectLevel')
    return
  }
  if (formStep.value === 3 && (!form.parent_name.trim() || !form.parent_phone.trim())) {
    error.value = !form.parent_name.trim() ? t('admission.parentName') : t('admission.phone')
    return
  }
  if (formStep.value < 4) formStep.value += 1
}

const display = (value) => {
  const text = String(value || '').trim()
  return text || t('admission.reviewEmpty')
}

const fileNames = (list) => {
  if (!list?.length) return t('admission.reviewEmpty')
  return list.map((f) => f.name).join(', ')
}

const genderLabel = computed(() => {
  if (form.gender === 'Male') return t('admission.male')
  if (form.gender === 'Female') return t('admission.female')
  return t('admission.reviewEmpty')
})

const levelLabel = computed(() => {
  return levels.value.find((opt) => opt.value === form.level)?.label || display(form.level)
})

const programLabel = computed(() => {
  return availablePrograms.value.find((opt) => opt.value === form.program)?.label || display(form.program)
})

const resetForm = () => {
  Object.keys(form).forEach((k) => { form[k] = '' })
  bulletinFiles.value = []
  formStep.value = 1
}

const submit = async () => {
  error.value = ''
  success.value = ''
  submitting.value = true

  try {
    const fd = new FormData()
    Object.entries(form).forEach(([key, value]) => {
      if (key === 'program' || key === 'address') return
      fd.append(key, value || '')
    })
    fd.append('program', encodeAdmissionChoice(form.level, form.program))
    fd.append('address', formatRwandaAddress(form.district, form.province))
    bulletinFiles.value.forEach((f) => fd.append('bulletin[]', f))

    const res = await apiFetch('/api/admissions', {
      method: 'POST',
      body: fd,
    })

    success.value = res?.message || t('admission.successDefault')
    submittedNumber.value = res?.data?.registration_number || ''
    notifyContentChanged(['admissions', 'notifications'])
    resetForm()
    if (import.meta.client) {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Failed to submit application'
  } finally {
    submitting.value = false
  }
}

onContentChange(['admissions', 'settings'], () => {
  loadPageSettings()
})

onMounted(() => {
  loadPageSettings()
  loadLocations()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
.admission-page {
  font-family: 'Hanken Grotesk', system-ui, sans-serif;
}
.font-display,
.admission-page h1,
.admission-page h2,
.admission-page h3 {
  font-family: 'Chivo', 'Hanken Grotesk', sans-serif;
}
.hero-pattern {
  background-image:
    linear-gradient(135deg, rgba(255, 255, 255, 0.06) 25%, transparent 25%),
    linear-gradient(225deg, rgba(255, 255, 255, 0.06) 25%, transparent 25%),
    linear-gradient(45deg, rgba(255, 255, 255, 0.06) 25%, transparent 25%),
    linear-gradient(315deg, rgba(255, 255, 255, 0.06) 25%, transparent 25%);
  background-size: 28px 28px;
  background-position: 0 0, 14px 0, 14px -14px, 0 14px;
}
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  user-select: none;
}
.label {
  display: block;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: #334155;
  margin-bottom: 0.35rem;
}
.field {
  width: 100%;
  padding: 0.65rem 0.9rem;
  font-size: 0.95rem;
  border-radius: 0.75rem;
  border: 1px solid #c7d7ee;
  background: #f8fbff;
  color: #0b1f3a;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
}
.field:hover:not(:disabled) {
  border-color: #93c5fd;
}
.field:focus {
  border-color: #1d4ed8;
  box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.18);
  background: #fff;
}
.upload-zone {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  width: 100%;
  padding: 0.8rem 1rem;
  border-radius: 0.75rem;
  border: 1.5px dashed #93c5fd;
  background: #fff;
  cursor: pointer;
  transition: border-color 0.15s, background 0.15s;
}
.upload-zone:hover {
  border-color: #1d4ed8;
  background: #eff6ff;
}
:global(html.dark) .label {
  color: #cbd5e1;
}
:global(html.dark) .field {
  background: #0f172a;
  border-color: #334155;
  color: #f8fafc;
}
:global(html.dark) .field:focus {
  background: #1e293b;
}
:global(html.dark) .upload-zone {
  background: #0f172a;
  border-color: #1d4ed8;
  color: #e2e8f0;
}
:global(html.dark) .upload-zone:hover {
  background: #1e293b;
}
.btn-gold,
.btn-blue,
.btn-ghost {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  font-weight: 700;
  font-size: 0.9rem;
  padding: 0.75rem 1.4rem;
  border-radius: 0.75rem;
  transition: transform 0.12s, box-shadow 0.12s, background 0.15s;
}
.btn-gold {
  background: #fbbf24;
  color: #0b1f3a;
  box-shadow: 0 4px 0 0 #1e3a8a;
}
.btn-gold:hover {
  background: #f59e0b;
  transform: translateY(-1px);
}
.btn-gold:active,
.btn-blue:active {
  transform: translateY(3px);
  box-shadow: none;
}
.btn-blue {
  background: #1d4ed8;
  color: #fff;
  box-shadow: 0 4px 0 0 #0b1f3a;
}
.btn-blue:hover {
  background: #1e40af;
  transform: translateY(-1px);
}
.btn-ghost {
  background: transparent;
  color: #fff;
  border: 1.5px solid rgba(255, 255, 255, 0.45);
}
.btn-ghost:hover {
  background: rgba(255, 255, 255, 0.1);
}
details summary::-webkit-details-marker {
  display: none;
}
</style>