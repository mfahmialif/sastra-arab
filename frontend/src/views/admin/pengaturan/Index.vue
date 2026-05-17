<template>
  <div class="flex flex-col gap-6">
    <div class="settings-card rounded-xl overflow-hidden">
      <div class="card-header px-6 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-accent text-[20px]">tune</span>
          <h3 class="font-bold" style="color: var(--text-heading)">Pengaturan Umum</h3>
        </div>
        <button type="button" class="section-save-btn" :disabled="savingGeneral" @click="saveGeneralSettings">
          <span class="material-symbols-outlined text-[16px]">save</span>
          Simpan Umum
        </button>
      </div>
      <div class="px-6 py-5 space-y-6">
        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Nama Sistem</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Nama yang ditampilkan di halaman publik dan admin</p>
          </div>
          <div class="setting-control">
            <input v-model="systemName" class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent" />
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Favicon</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Ikon kecil yang tampil di tab browser. Format: ico, png, jpg, svg, atau webp</p>
          </div>
          <div class="setting-control">
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 rounded-xl flex items-center justify-center overflow-hidden border-2 border-dashed border-accent/40" style="background: var(--bg-input)">
                <img v-if="faviconPreview" :src="faviconPreview" alt="Favicon preview" class="h-10 w-10 object-contain" />
                <span v-else class="material-symbols-outlined text-accent text-3xl">web_asset</span>
              </div>
              <label class="upload-btn flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold cursor-pointer transition-all">
                <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                Upload Favicon
                <input class="sr-only" type="file" accept=".ico,.png,.jpg,.jpeg,.svg,.webp,image/*" @change="handleFaviconChange" />
              </label>
            </div>
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Brand Navbar Publik</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Pilih tampilan teks seperti sekarang atau logo upload untuk navbar landing page.</p>
          </div>
          <div class="setting-control">
            <div class="navbar-brand-control">
              <div class="brand-mode-toggle">
                <button type="button" :class="{ active: navbarBrandMode === 'text' }" @click="navbarBrandMode = 'text'">
                  <span class="material-symbols-outlined text-[16px]">text_fields</span>
                  Teks
                </button>
                <button type="button" :class="{ active: navbarBrandMode === 'logo' }" @click="navbarBrandMode = 'logo'">
                  <span class="material-symbols-outlined text-[16px]">image</span>
                  Logo
                </button>
              </div>
              <div v-if="navbarBrandMode === 'text'" class="navbar-text-fields">
                <label>
                  <span>Icon Navbar</span>
                  <VueMultiselect
                    ref="navbarIconSelect"
                    v-model="navbarTextIcon"
                    :options="materialIconOptions"
                    :searchable="true"
                    :taggable="true"
                    :close-on-select="true"
                    :allow-empty="false"
                    :show-labels="false"
                    class="navbar-icon-select"
                    placeholder="Pilih icon"
                    @select="closeNavbarIconDropdown"
                    @tag="addNavbarIcon"
                  >
                    <template #singleLabel="{ option }">
                      <span class="icon-select-option">
                        <span class="material-symbols-outlined">{{ option }}</span>
                        <span>{{ option }}</span>
                      </span>
                    </template>
                    <template #option="{ option }">
                      <span class="icon-select-option">
                        <span class="material-symbols-outlined">{{ option }}</span>
                        <span>{{ option }}</span>
                      </span>
                    </template>
                  </VueMultiselect>
                </label>
                <label>
                  <span>Judul Navbar</span>
                  <input v-model="navbarTextTitle" class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent" />
                </label>
                <label>
                  <span>Subtitle Navbar</span>
                  <input v-model="navbarTextSubtitle" class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent" />
                </label>
              </div>
              <div class="flex items-center gap-4">
                <div class="navbar-logo-preview">
                  <img v-if="navbarBrandMode === 'logo' && navbarLogoPreview" :src="navbarLogoPreview" alt="Logo navbar preview" />
                  <div v-else class="text-preview text-preview-with-icon">
                    <span class="preview-logo-icon material-symbols-outlined">{{ navbarTextIcon || 'auto_stories' }}</span>
                    <span class="preview-logo-text">
                      <strong>{{ navbarTextTitle || systemName || 'Sastra Arab' }}</strong>
                      <span>{{ navbarTextSubtitle || 'Program Studi' }}</span>
                    </span>
                  </div>
                </div>
                <label v-if="navbarBrandMode === 'logo'" class="upload-btn flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold cursor-pointer transition-all">
                  <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                  Upload Logo
                  <input class="sr-only" type="file" accept=".png,.jpg,.jpeg,.svg,.webp,image/*" @change="handleNavbarLogoChange" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <Transition name="fade">
          <div v-if="generalMessage.text" class="message-box" :class="generalMessage.type">
            <span class="material-symbols-outlined text-[18px]">{{ generalMessage.type === 'success' ? 'check_circle' : 'error' }}</span>
            <span>{{ generalMessage.text }}</span>
          </div>
        </Transition>
      </div>
    </div>

    <LandingSettings ref="landingSettingsRef" />

    <div class="settings-card rounded-xl overflow-hidden">
      <div class="card-header px-6 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-accent text-[20px]">login</span>
          <h3 class="font-bold" style="color: var(--text-heading)">Pengaturan Login</h3>
        </div>
        <button type="button" class="section-save-btn" :disabled="savingLogin" @click="saveLoginSettings">
          <span class="material-symbols-outlined text-[16px]">save</span>
          Simpan Login
        </button>
      </div>
      <div class="px-6 py-5 space-y-6">
        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Gambar Panel Login</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Gambar besar di sisi kiri halaman login desktop. Format: png, jpg, jpeg, atau webp.</p>
          </div>
          <div class="setting-control">
            <div class="login-image-control">
              <div class="login-image-preview">
                <img v-if="loginImagePreview" :src="loginImagePreview" alt="Preview gambar login" />
                <div v-else class="login-image-empty">
                  <span class="material-symbols-outlined">image</span>
                  <p>Belum ada gambar custom</p>
                </div>
              </div>
              <label class="upload-btn flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold cursor-pointer transition-all">
                <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                Upload Gambar Login
                <input class="sr-only" type="file" accept=".png,.jpg,.jpeg,.webp,image/*" @change="handleLoginImageChange" />
              </label>
            </div>
          </div>
        </div>

        <Transition name="fade">
          <div v-if="loginMessage.text" class="message-box" :class="loginMessage.type">
            <span class="material-symbols-outlined text-[18px]">{{ loginMessage.type === 'success' ? 'check_circle' : 'error' }}</span>
            <span>{{ loginMessage.text }}</span>
          </div>
        </Transition>
      </div>
    </div>

    <div class="settings-card rounded-xl overflow-hidden">
      <div class="card-header px-6 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-accent text-[20px]">palette</span>
          <h3 class="font-bold" style="color: var(--text-heading)">Warna Tema</h3>
        </div>
        <button type="button" class="section-save-btn" :disabled="savingGeneral" @click="saveGeneralSettings">
          <span class="material-symbols-outlined text-[16px]">save</span>
          Simpan Warna
        </button>
      </div>
      <div class="px-6 py-5 space-y-6">
        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Warna Aksen</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Warna utama untuk tombol, badge, dan highlight</p>
          </div>
          <div class="setting-control">
            <div class="color-control">
              <label class="color-picker-shell" :style="{ '--picked-color': selectedAccent }" title="Pilih warna custom">
                <span class="color-preview"></span>
                <span class="color-picker-text">
                  <span class="material-symbols-outlined text-[16px]">palette</span>
                  Pilih Warna
                </span>
                <input v-model="selectedAccent" type="color" @input="applyAccentColor(selectedAccent)" />
              </label>
              <input
                v-model="selectedAccent"
                class="setting-input color-code rounded-lg py-2.5 px-4 text-sm font-bold uppercase focus:outline-none focus:ring-1 focus:ring-accent"
                maxlength="7"
                @input="handleAccentInput"
              />
            </div>
            <div class="mt-4 flex items-center gap-3 flex-wrap">
              <button v-for="color in accentColors" :key="color.value"
                      @click="chooseAccent(color.value)"
                      :class="['w-10 h-10 rounded-xl cursor-pointer transition-all border-2', selectedAccent === color.value ? 'border-white scale-110 shadow-lg' : 'border-transparent hover:scale-105']"
                      :style="{ background: color.value }"
                      :title="color.name">
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="settings-card rounded-xl overflow-hidden">
      <div class="card-header px-6 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-accent text-[20px]">outgoing_mail</span>
          <h3 class="font-bold" style="color: var(--text-heading)">Pengaturan Email SMTP</h3>
        </div>
        <div class="section-header-actions">
          <button type="button" class="section-save-btn" :disabled="savingEmail" @click="saveEmailSettings">
            <span class="material-symbols-outlined text-[16px]">save</span>
            Simpan Email
          </button>
          <button type="button" class="toggle-switch" :class="{ active: emailForm.smtp_enabled }" @click="emailForm.smtp_enabled = !emailForm.smtp_enabled">
            <span></span>
          </button>
        </div>
      </div>
      <div class="px-6 py-5 space-y-6">
        <div class="guide-panel">
          <div class="guide-head">
            <span class="material-symbols-outlined text-[20px]">mail_lock</span>
            <div>
              <h4>Panduan Menyiapkan SMTP</h4>
              <p>SMTP dipakai untuk mengirim kode verifikasi saat user daftar dengan email.</p>
            </div>
          </div>

          <div class="guide-grid">
            <div v-for="step in emailGuideSteps" :key="step.title" class="guide-step">
              <span class="guide-step-icon material-symbols-outlined">{{ step.icon }}</span>
              <div>
                <h5>{{ step.title }}</h5>
                <p>{{ step.description }}</p>
              </div>
            </div>
          </div>

          <div class="guide-notes">
            <div>
              <h5>Gmail / Google Workspace</h5>
              <p>Gunakan App Password, bukan password akun utama. Host: <code>smtp.gmail.com</code>, port <code>587</code>, encryption <code>tls</code>.</p>
            </div>
            <div>
              <h5>Mailtrap / Sandbox</h5>
              <p>Salin host, port, username, dan password dari inbox testing. Cocok untuk development sebelum memakai email production.</p>
            </div>
            <div>
              <h5>From Address</h5>
              <p>Gunakan email yang diizinkan provider SMTP. Beberapa provider menolak pengiriman jika From Address berbeda dari akun SMTP.</p>
            </div>
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Status SMTP</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Aktifkan agar sistem bisa mengirim kode verifikasi email</p>
          </div>
          <div class="setting-control">
            <label class="inline-flex items-center gap-3 cursor-pointer">
              <input v-model="emailForm.smtp_enabled" type="checkbox" class="sr-only" />
              <span class="checkbox-box">
                <span class="material-symbols-outlined text-[15px]">check</span>
              </span>
              <span class="text-sm font-bold" style="color: var(--text-heading)">
                {{ emailForm.smtp_enabled ? 'Aktif' : 'Nonaktif' }}
              </span>
            </label>
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Host SMTP</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Alamat server SMTP provider email</p>
          </div>
          <div class="setting-control">
            <input v-model="emailForm.smtp_host" class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent" placeholder="smtp.gmail.com" />
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Port & Encryption</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Umumnya 587/tls atau 465/ssl</p>
          </div>
          <div class="setting-control grid gap-3 sm:grid-cols-[1fr_1fr]">
            <input v-model.number="emailForm.smtp_port" class="setting-input rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent" type="number" min="1" max="65535" placeholder="587" />
            <select v-model="emailForm.smtp_encryption" class="setting-input rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent">
              <option value="tls">TLS</option>
              <option value="ssl">SSL</option>
              <option value="">Tanpa encryption</option>
            </select>
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Username SMTP</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Biasanya alamat email atau username dari provider</p>
          </div>
          <div class="setting-control">
            <input v-model="emailForm.smtp_username" class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent" placeholder="no-reply@domain.com" />
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Password SMTP</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Untuk Gmail gunakan App Password</p>
          </div>
          <div class="setting-control">
            <input v-model="emailForm.smtp_password" class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent" type="password" autocomplete="new-password" placeholder="••••••••" />
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">From Email & Nama</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Identitas pengirim email verifikasi</p>
          </div>
          <div class="setting-control grid gap-3 sm:grid-cols-[1fr_1fr]">
            <input v-model="emailForm.smtp_from_address" class="setting-input rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent" type="email" placeholder="no-reply@domain.com" />
            <input v-model="emailForm.smtp_from_name" class="setting-input rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent" placeholder="Sastra Arab" />
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Role Default Pendaftar Email</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Role user baru setelah kode email berhasil diverifikasi</p>
          </div>
          <div class="setting-control">
            <select v-model="emailForm.email_registration_default_role_id" class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent">
              <option :value="null">Pilih role default</option>
              <option v-for="role in emailRoles" :key="role.id" :value="role.id">{{ role.name }}</option>
            </select>
          </div>
        </div>

        <Transition name="fade">
          <div v-if="emailMessage.text" class="message-box" :class="emailMessage.type">
            <span class="material-symbols-outlined text-[18px]">{{ emailMessage.type === 'success' ? 'check_circle' : 'error' }}</span>
            <span>{{ emailMessage.text }}</span>
          </div>
        </Transition>
      </div>
    </div>

    <div class="settings-card rounded-xl overflow-hidden">
      <div class="card-header px-6 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-accent text-[20px]">passkey</span>
          <h3 class="font-bold" style="color: var(--text-heading)">Login Google</h3>
        </div>
        <div class="section-header-actions">
          <button type="button" class="section-save-btn" :disabled="savingGoogle" @click="saveGoogleSettings">
            <span class="material-symbols-outlined text-[16px]">save</span>
            Simpan Google
          </button>
          <button
            type="button"
            class="toggle-switch"
            :class="{ active: googleForm.google_login_enabled }"
            @click="googleForm.google_login_enabled = !googleForm.google_login_enabled"
          >
            <span></span>
          </button>
        </div>
      </div>
      <div class="px-6 py-5 space-y-6">
        <div class="guide-panel">
          <div class="guide-head">
            <span class="material-symbols-outlined text-[20px]">school</span>
            <div>
              <h4>Panduan Mendapatkan Konfigurasi Google</h4>
              <p>Ikuti urutan ini sebelum mengaktifkan tombol Login Google.</p>
            </div>
          </div>

          <div class="guide-grid">
            <div v-for="step in googleGuideSteps" :key="step.title" class="guide-step">
              <span class="guide-step-icon material-symbols-outlined">{{ step.icon }}</span>
              <div>
                <h5>{{ step.title }}</h5>
                <p>{{ step.description }}</p>
              </div>
            </div>
          </div>

          <div class="guide-notes">
            <div>
              <h5>Authorized JavaScript origins</h5>
              <p>Masukkan origin frontend, tanpa path. Contoh lokal: <code>http://localhost:5173</code>. Contoh production: <code>https://domain-anda.com</code>.</p>
            </div>
            <div>
              <h5>Field yang dipakai sistem</h5>
              <p><strong>Client ID</strong> wajib untuk tombol login dan verifikasi token. <strong>Client Secret</strong> disediakan untuk kebutuhan OAuth lanjutan, tetapi flow saat ini memakai ID token.</p>
            </div>
            <div>
              <h5>Domain dan user baru</h5>
              <p>Isi pembatas domain jika hanya email institusi yang boleh login. Jika auto buat user aktif, pilih role default agar akun Google baru langsung punya akses dashboard.</p>
            </div>
          </div>

          <a
            class="guide-link"
            href="https://developers.google.com/identity/gsi/web/guides/get-google-api-clientid"
            target="_blank"
            rel="noopener noreferrer"
          >
            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
            Dokumentasi resmi Google Identity Services
          </a>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Status Login Google</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Aktifkan tombol login Google di halaman login publik</p>
          </div>
          <div class="setting-control">
            <label class="inline-flex items-center gap-3 cursor-pointer">
              <input v-model="googleForm.google_login_enabled" type="checkbox" class="sr-only" />
              <span class="checkbox-box">
                <span class="material-symbols-outlined text-[15px]">check</span>
              </span>
              <span class="text-sm font-bold" style="color: var(--text-heading)">
                {{ googleForm.google_login_enabled ? 'Aktif' : 'Nonaktif' }}
              </span>
            </label>
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Google Client ID</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">OAuth Web Client ID dari Google Cloud Console</p>
          </div>
          <div class="setting-control">
            <input
              v-model="googleForm.google_client_id"
              class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent"
              placeholder="xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com"
            />
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Google Client Secret</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Disimpan untuk kebutuhan OAuth lanjutan jika nanti diperlukan</p>
          </div>
          <div class="setting-control">
            <input
              v-model="googleForm.google_client_secret"
              class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent"
              placeholder="GOCSPX-..."
              type="password"
              autocomplete="new-password"
            />
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Batasi Domain Email</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Opsional, contoh: uiidalwa.ac.id. Kosongkan untuk semua domain Google</p>
          </div>
          <div class="setting-control">
            <input
              v-model="googleForm.google_hosted_domain"
              class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent"
              placeholder="uiidalwa.ac.id"
            />
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Auto Buat User</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Jika aktif, akun Google baru akan dibuat otomatis dengan role default</p>
          </div>
          <div class="setting-control">
            <label class="inline-flex items-center gap-3 cursor-pointer">
              <input v-model="googleForm.google_auto_create_users" type="checkbox" class="sr-only" />
              <span class="checkbox-box">
                <span class="material-symbols-outlined text-[15px]">check</span>
              </span>
              <span class="text-sm font-bold" style="color: var(--text-heading)">
                {{ googleForm.google_auto_create_users ? 'Aktif' : 'Nonaktif' }}
              </span>
            </label>
          </div>
        </div>

        <div class="setting-row">
          <div class="setting-label">
            <h4 class="text-sm font-bold" style="color: var(--text-heading)">Role Default</h4>
            <p class="text-xs mt-0.5" style="color: var(--text-muted)">Role untuk user yang dibuat otomatis dari Google</p>
          </div>
          <div class="setting-control">
            <select
              v-model="googleForm.google_default_role_id"
              class="setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent"
            >
              <option :value="null">Pilih role default</option>
              <option v-for="role in googleRoles" :key="role.id" :value="role.id">{{ role.name }}</option>
            </select>
          </div>
        </div>

        <Transition name="fade">
          <div v-if="googleMessage.text" class="message-box" :class="googleMessage.type">
            <span class="material-symbols-outlined text-[18px]">{{ googleMessage.type === 'success' ? 'check_circle' : 'error' }}</span>
            <span>{{ googleMessage.text }}</span>
          </div>
        </Transition>
      </div>
    </div>

    <div class="save-bar rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 sticky bottom-0 z-10">
      <p class="text-sm" style="color: var(--text-muted)">
        <span class="material-symbols-outlined text-accent text-[16px] align-text-bottom mr-1">info</span>
        Pengaturan ini disiapkan untuk kebutuhan identitas Sastra Arab.
      </p>
      <button
        class="save-btn flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold cursor-pointer transition-all active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
        :disabled="isSaving"
        @click="saveAllSettings"
      >
        <span class="material-symbols-outlined text-[16px]">save</span>
        Simpan Pengaturan
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref } from 'vue'
import VueMultiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import api from '../../../axios'
import { applyAccentColor as applyGlobalAccentColor } from '../../../utils/appearance'
import { backendAssetUrl } from '../../../utils/asset'
import LandingSettings from './LandingSettings.vue'

const systemName = ref('Sastra Arab')
const selectedAccent = ref('#2563eb')
const faviconFile = ref(null)
const faviconPreview = ref('')
const navbarBrandMode = ref('text')
const navbarTextIcon = ref('auto_stories')
const navbarTextTitle = ref('Sastra Arab')
const navbarTextSubtitle = ref('Program Studi')
const navbarIconSelect = ref(null)
const navbarLogoFile = ref(null)
const navbarLogoPreview = ref('')
const loginImageFile = ref(null)
const loginImagePreview = ref('')
const savingGeneral = ref(false)
const savingLogin = ref(false)
const savingEmail = ref(false)
const savingGoogle = ref(false)
const savingAll = ref(false)
const googleRoles = ref([])
const emailRoles = ref([])
const landingSettingsRef = ref(null)
const isSaving = computed(() => savingAll.value || savingGeneral.value || savingLogin.value || savingEmail.value || savingGoogle.value)
const SAVE_TIMEOUT_MS = 20000
const MIN_LOADER_MS = 350
const generalMessage = reactive({
  type: '',
  text: '',
})
const emailMessage = reactive({
  type: '',
  text: '',
})
const loginMessage = reactive({
  type: '',
  text: '',
})
const googleMessage = reactive({
  type: '',
  text: '',
})
const googleForm = reactive({
  google_login_enabled: false,
  google_client_id: '',
  google_client_secret: '',
  google_hosted_domain: '',
  google_auto_create_users: false,
  google_default_role_id: null,
})
const emailForm = reactive({
  smtp_enabled: false,
  smtp_host: '',
  smtp_port: 587,
  smtp_username: '',
  smtp_password: '',
  smtp_encryption: 'tls',
  smtp_from_address: '',
  smtp_from_name: 'Sastra Arab',
  email_registration_default_role_id: null,
})
const emailGuideSteps = [
  {
    icon: 'alternate_email',
    title: 'Siapkan Akun Pengirim',
    description: 'Gunakan email resmi atau email khusus no-reply agar kode verifikasi tidak tercampur dengan akun pribadi.',
  },
  {
    icon: 'vpn_key',
    title: 'Ambil Kredensial SMTP',
    description: 'Salin host, port, username, password atau app password dari provider email yang digunakan.',
  },
  {
    icon: 'encrypted',
    title: 'Pilih Encryption',
    description: 'Pilih TLS untuk port 587, SSL untuk port 465, atau kosongkan hanya jika provider meminta tanpa enkripsi.',
  },
  {
    icon: 'outgoing_mail',
    title: 'Isi From Email',
    description: 'Pastikan From Address diizinkan oleh provider SMTP supaya email tidak ditolak.',
  },
  {
    icon: 'group_add',
    title: 'Pilih Role Default',
    description: 'Role ini diberikan ke user baru setelah berhasil memasukkan kode verifikasi email.',
  },
  {
    icon: 'save',
    title: 'Simpan dan Coba Daftar',
    description: 'Setelah disimpan, coba daftar akun dari halaman login untuk memastikan email kode terkirim.',
  },
]
const googleGuideSteps = [
  {
    icon: 'cloud',
    title: 'Buka Google Cloud Console',
    description: 'Buat project baru atau pilih project yang sudah dipakai untuk Sastra Arab.',
  },
  {
    icon: 'branding_watermark',
    title: 'Lengkapi OAuth Branding',
    description: 'Isi nama aplikasi, support email, authorized domain, homepage, privacy policy, dan terms jika tersedia.',
  },
  {
    icon: 'key',
    title: 'Buat OAuth Client',
    description: 'Masuk ke Clients atau Credentials, pilih Create client, lalu pilih Application type: Web application.',
  },
  {
    icon: 'language',
    title: 'Tambahkan Origin Frontend',
    description: 'Masukkan origin website ke Authorized JavaScript origins, termasuk origin lokal saat development.',
  },
  {
    icon: 'content_copy',
    title: 'Salin Client ID',
    description: 'Tempel Client ID ke form di bawah. Client ID biasanya berakhiran .apps.googleusercontent.com.',
  },
  {
    icon: 'toggle_on',
    title: 'Aktifkan dan Simpan',
    description: 'Aktifkan Login Google, atur domain atau auto-create jika perlu, lalu simpan pengaturan.',
  },
]
const accentColors = [
  { name: 'Blue', value: '#2563eb' },
  { name: 'Emerald', value: '#047857' },
  { name: 'Slate', value: '#0f172a' },
  { name: 'Rose', value: '#e11d48' },
  { name: 'Cyan', value: '#0891b2' }
]
const materialIconOptions = [
  'auto_stories',
  'menu_book',
  'school',
  'translate',
  'history_edu',
  'record_voice_over',
  'public',
  'groups',
  'diversity_3',
  'science',
  'forum',
  'campaign',
  'workspace_premium',
  'star',
  'support_agent',
  'contact_support',
  'handshake',
  'event',
]

function wait(ms) {
  return new Promise((resolve) => window.setTimeout(resolve, ms))
}

async function finishLoader(startedAt) {
  const elapsed = Date.now() - startedAt
  if (elapsed < MIN_LOADER_MS) {
    await wait(MIN_LOADER_MS - elapsed)
  }
}

function isHexColor(value) {
  return /^#[0-9a-fA-F]{6}$/.test(value)
}

function applyAccentColor(value) {
  if (!isHexColor(value)) return

  const root = document.querySelector('.admin-root, .author-root, .editor-root, .portal-root')
  root?.style.setProperty('--color-accent', value)
  applyGlobalAccentColor(value)
}

function applyFavicon(url) {
  if (!url) return

  let favicon = document.querySelector("link[rel='icon']")
  if (!favicon) {
    favicon = document.createElement('link')
    favicon.rel = 'icon'
    document.head.appendChild(favicon)
  }
  favicon.href = url
}

function fillGeneralForm(data) {
  systemName.value = data.system_name || 'Sastra Arab'
  selectedAccent.value = data.accent_color || '#2563eb'
  faviconPreview.value = backendAssetUrl(data.favicon_path || data.favicon_url)
  navbarBrandMode.value = data.navbar_brand_mode || 'text'
  navbarTextIcon.value = data.navbar_text_icon || 'auto_stories'
  navbarTextTitle.value = data.navbar_text_title || data.system_name || 'Sastra Arab'
  navbarTextSubtitle.value = data.navbar_text_subtitle || 'Program Studi'
  navbarLogoPreview.value = backendAssetUrl(data.navbar_logo_path || data.navbar_logo_url)
  applyAccentColor(selectedAccent.value)
  applyFavicon(faviconPreview.value)
}

function chooseAccent(value) {
  selectedAccent.value = value
  applyAccentColor(value)
}

function handleAccentInput() {
  selectedAccent.value = selectedAccent.value.trim()
  applyAccentColor(selectedAccent.value)
}

function handleFaviconChange(event) {
  const file = event.target.files?.[0]
  if (!file) return

  faviconFile.value = file
  faviconPreview.value = URL.createObjectURL(file)
}

function closeNavbarIconDropdown() {
  const multiselect = navbarIconSelect.value
  if (!multiselect) return

  multiselect.deactivate?.()
  multiselect.isOpen = false
  if ('search' in multiselect) {
    multiselect.search = ''
  }
  nextTick(() => {
    multiselect.deactivate?.()
    multiselect.isOpen = false
  })
  window.setTimeout(() => {
    multiselect.deactivate?.()
    multiselect.isOpen = false
  }, 0)
}

function addNavbarIcon(value) {
  if (!materialIconOptions.includes(value)) {
    materialIconOptions.push(value)
  }
  navbarTextIcon.value = value
  closeNavbarIconDropdown()
}

function handleNavbarLogoChange(event) {
  const file = event.target.files?.[0]
  if (!file) return

  navbarLogoFile.value = file
  navbarLogoPreview.value = URL.createObjectURL(file)
  navbarBrandMode.value = 'logo'
}

function handleLoginImageChange(event) {
  const file = event.target.files?.[0]
  if (!file) return

  loginImageFile.value = file
  loginImagePreview.value = URL.createObjectURL(file)
}

async function loadGeneralSettings() {
  try {
    const { data } = await api.get('/settings/general')
    fillGeneralForm(data)
  } catch {
    generalMessage.type = 'error'
    generalMessage.text = 'Gagal memuat pengaturan umum.'
  }
}

async function saveGeneralSettings() {
  if (savingGeneral.value) return false

  if (!isHexColor(selectedAccent.value)) {
    generalMessage.type = 'error'
    generalMessage.text = 'Warna aksen harus memakai format hex, contoh #2563eb.'
    return false
  }

  const startedAt = Date.now()
  savingGeneral.value = true
  generalMessage.type = ''
  generalMessage.text = ''

  const formData = new FormData()
  formData.append('system_name', systemName.value)
  formData.append('accent_color', selectedAccent.value)
  formData.append('navbar_brand_mode', navbarBrandMode.value)
  formData.append('navbar_text_icon', navbarTextIcon.value)
  formData.append('navbar_text_title', navbarTextTitle.value)
  formData.append('navbar_text_subtitle', navbarTextSubtitle.value)
  if (faviconFile.value) {
    formData.append('favicon', faviconFile.value)
  }
  if (navbarLogoFile.value) {
    formData.append('navbar_logo', navbarLogoFile.value)
  }

  try {
    const { data } = await api.post('/settings/general', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      timeout: SAVE_TIMEOUT_MS,
    })
    faviconFile.value = null
    navbarLogoFile.value = null
    fillGeneralForm(data)
    generalMessage.type = 'success'
    generalMessage.text = 'Pengaturan umum berhasil disimpan.'
    return true
  } catch (error) {
    const errors = error.response?.data?.errors || {}
    generalMessage.type = 'error'
    generalMessage.text = error.code === 'ECONNABORTED'
      ? 'Menyimpan pengaturan umum terlalu lama. Coba ulangi atau cek koneksi backend.'
      : Object.values(errors)?.[0]?.[0] || 'Gagal menyimpan pengaturan umum.'
    return false
  } finally {
    await finishLoader(startedAt)
    savingGeneral.value = false
  }
}

function fillLoginForm(data) {
  loginImagePreview.value = backendAssetUrl(data.image_path || data.image_url)
}

async function loadLoginSettings() {
  try {
    const { data } = await api.get('/settings/login')
    fillLoginForm(data)
  } catch {
    loginMessage.type = 'error'
    loginMessage.text = 'Gagal memuat pengaturan login.'
  }
}

async function saveLoginSettings() {
  if (savingLogin.value) return false

  const startedAt = Date.now()
  savingLogin.value = true
  loginMessage.type = ''
  loginMessage.text = ''

  const formData = new FormData()
  if (loginImageFile.value) {
    formData.append('login_image', loginImageFile.value)
  }

  try {
    const { data } = await api.post('/settings/login', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      timeout: SAVE_TIMEOUT_MS,
    })
    loginImageFile.value = null
    fillLoginForm(data)
    loginMessage.type = 'success'
    loginMessage.text = 'Pengaturan login berhasil disimpan.'
    return true
  } catch (error) {
    const errors = error.response?.data?.errors || {}
    loginMessage.type = 'error'
    loginMessage.text = error.code === 'ECONNABORTED'
      ? 'Menyimpan pengaturan login terlalu lama. Coba ulangi atau cek koneksi backend.'
      : Object.values(errors)?.[0]?.[0] || 'Gagal menyimpan pengaturan login.'
    return false
  } finally {
    await finishLoader(startedAt)
    savingLogin.value = false
  }
}

function fillGoogleForm(data) {
  googleForm.google_login_enabled = Boolean(data.google_login_enabled)
  googleForm.google_client_id = data.google_client_id || ''
  googleForm.google_client_secret = data.google_client_secret || ''
  googleForm.google_hosted_domain = data.google_hosted_domain || ''
  googleForm.google_auto_create_users = Boolean(data.google_auto_create_users)
  googleForm.google_default_role_id = data.google_default_role_id ? Number(data.google_default_role_id) : null
  googleRoles.value = data.roles || googleRoles.value
}

function fillEmailForm(data) {
  emailForm.smtp_enabled = Boolean(data.smtp_enabled)
  emailForm.smtp_host = data.smtp_host || ''
  emailForm.smtp_port = data.smtp_port || 587
  emailForm.smtp_username = data.smtp_username || ''
  emailForm.smtp_password = data.smtp_password || ''
  emailForm.smtp_encryption = data.smtp_encryption ?? 'tls'
  emailForm.smtp_from_address = data.smtp_from_address || ''
  emailForm.smtp_from_name = data.smtp_from_name || systemName.value
  emailForm.email_registration_default_role_id = data.email_registration_default_role_id ? Number(data.email_registration_default_role_id) : null
  emailRoles.value = data.roles || emailRoles.value
}

async function loadEmailSettings() {
  try {
    const { data } = await api.get('/settings/email')
    fillEmailForm(data)
  } catch {
    emailMessage.type = 'error'
    emailMessage.text = 'Gagal memuat pengaturan email.'
  }
}

async function saveEmailSettings() {
  if (savingEmail.value) return false

  const startedAt = Date.now()
  savingEmail.value = true
  emailMessage.type = ''
  emailMessage.text = ''

  try {
    const { data } = await api.put('/settings/email', {
      ...emailForm,
      email_registration_default_role_id: emailForm.email_registration_default_role_id || null,
    }, {
      timeout: SAVE_TIMEOUT_MS,
    })
    fillEmailForm(data)
    emailMessage.type = 'success'
    emailMessage.text = 'Pengaturan email berhasil disimpan.'
    return true
  } catch (error) {
    const errors = error.response?.data?.errors || {}
    emailMessage.type = 'error'
    emailMessage.text = error.code === 'ECONNABORTED'
      ? 'Menyimpan pengaturan email terlalu lama. Coba ulangi atau cek koneksi backend.'
      : Object.values(errors)?.[0]?.[0] || 'Gagal menyimpan pengaturan email.'
    return false
  } finally {
    await finishLoader(startedAt)
    savingEmail.value = false
  }
}

async function loadGoogleSettings() {
  try {
    const { data } = await api.get('/settings/google-login')
    fillGoogleForm(data)
  } catch {
    googleMessage.type = 'error'
    googleMessage.text = 'Gagal memuat pengaturan Login Google.'
  }
}

async function saveGoogleSettings() {
  if (savingGoogle.value) return false

  const startedAt = Date.now()
  savingGoogle.value = true
  googleMessage.type = ''
  googleMessage.text = ''

  try {
    const { data } = await api.put('/settings/google-login', {
      ...googleForm,
      google_default_role_id: googleForm.google_default_role_id || null,
    }, {
      timeout: SAVE_TIMEOUT_MS,
    })
    fillGoogleForm(data)
    googleMessage.type = 'success'
    googleMessage.text = 'Pengaturan Login Google berhasil disimpan.'
    return true
  } catch (error) {
    const errors = error.response?.data?.errors || {}
    googleMessage.type = 'error'
    googleMessage.text = error.code === 'ECONNABORTED'
      ? 'Menyimpan pengaturan Login Google terlalu lama. Coba ulangi atau cek koneksi backend.'
      : Object.values(errors)?.[0]?.[0] || 'Gagal menyimpan pengaturan Login Google.'
    return false
  } finally {
    await finishLoader(startedAt)
    savingGoogle.value = false
  }
}

async function saveAllSettings() {
  if (isSaving.value) return

  savingAll.value = true

  try {
    const generalSaved = await saveGeneralSettings()
    if (generalSaved) {
      const landingSaved = await landingSettingsRef.value?.saveLandingSettings?.() ?? true
      if (!landingSaved) return

      const loginSaved = await saveLoginSettings()
      if (loginSaved) {
        const emailSaved = await saveEmailSettings()
        if (emailSaved) {
          await saveGoogleSettings()
        }
      }
    }
  } finally {
    savingAll.value = false
  }
}

onMounted(() => {
  loadGeneralSettings()
  loadLoginSettings()
  loadEmailSettings()
  loadGoogleSettings()
})
</script>

<style scoped>
.settings-card { background: var(--bg-card); border: 1px solid var(--border); }
.card-header { border-bottom: 1px solid var(--border); }
.section-header-actions {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}
.section-save-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  min-height: 2.35rem;
  border: 1px solid color-mix(in srgb, var(--color-accent) 26%, var(--border));
  border-radius: 0.7rem;
  background: color-mix(in srgb, var(--color-accent) 10%, var(--bg-input));
  color: var(--text-heading);
  cursor: pointer;
  padding: 0 0.85rem;
  font-size: 0.78rem;
  font-weight: 900;
  transition: border-color 0.18s ease, background 0.18s ease, color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
}
.section-save-btn:hover:not(:disabled) {
  border-color: var(--color-accent);
  background: var(--color-accent);
  color: var(--text-btn);
  box-shadow: 0 10px 24px color-mix(in srgb, var(--color-accent) 24%, transparent);
  transform: translateY(-1px);
}
.section-save-btn:active:not(:disabled) {
  transform: translateY(0);
}
.section-save-btn:disabled {
  cursor: not-allowed;
  opacity: 0.58;
}
.guide-panel {
  border: 1px solid var(--border);
  border-radius: 1rem;
  background: color-mix(in srgb, var(--bg-input) 72%, transparent);
  padding: 1rem;
}
.guide-head {
  display: flex;
  gap: 0.8rem;
  align-items: flex-start;
  color: var(--text-heading);
}
.guide-head > span {
  display: inline-flex;
  width: 2.25rem;
  height: 2.25rem;
  align-items: center;
  justify-content: center;
  border-radius: 0.75rem;
  background: rgba(37, 99, 235, 0.12);
  color: var(--color-accent);
}
.guide-head h4 {
  font-size: 0.95rem;
  font-weight: 900;
}
.guide-head p,
.guide-step p,
.guide-notes p {
  color: var(--text-muted);
}
.guide-head p {
  margin-top: 0.15rem;
  font-size: 0.78rem;
  line-height: 1.5;
}
.guide-grid {
  display: grid;
  gap: 0.75rem;
  margin-top: 1rem;
}
.guide-step {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 0.7rem;
  border: 1px solid var(--border);
  border-radius: 0.85rem;
  background: var(--bg-card);
  padding: 0.85rem;
}
.guide-step-icon {
  color: var(--color-accent);
  font-size: 1.25rem;
}
.guide-step h5,
.guide-notes h5 {
  color: var(--text-heading);
  font-size: 0.8rem;
  font-weight: 900;
}
.guide-step p,
.guide-notes p {
  margin-top: 0.25rem;
  font-size: 0.76rem;
  line-height: 1.55;
}
.guide-notes {
  display: grid;
  gap: 0.75rem;
  margin-top: 1rem;
}
.guide-notes > div {
  border-left: 3px solid var(--color-accent);
  padding-left: 0.8rem;
}
.guide-notes code {
  border: 1px solid var(--border);
  border-radius: 0.35rem;
  background: var(--bg-card);
  color: var(--text-heading);
  padding: 0.08rem 0.28rem;
  font-size: 0.72rem;
}
.guide-link {
  display: inline-flex;
  width: fit-content;
  align-items: center;
  gap: 0.4rem;
  margin-top: 1rem;
  color: var(--color-accent);
  font-size: 0.78rem;
  font-weight: 900;
  transition: opacity 0.2s ease;
}
.guide-link:hover {
  opacity: 0.76;
}
.setting-row { display: flex; flex-direction: column; gap: 0.75rem; }
@media (max-width: 767px) {
  .card-header {
    align-items: flex-start;
    flex-direction: column;
  }
  .section-header-actions,
  .section-save-btn {
    width: 100%;
  }
}
@media (min-width: 768px) {
  .setting-row { flex-direction: row; align-items: flex-start; }
  .setting-label { width: 280px; flex-shrink: 0; padding-top: 0.5rem; }
  .setting-control { flex: 1; }
  .guide-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .guide-notes {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
.setting-input {
  background: var(--bg-input);
  border: 1px solid var(--border);
  color: var(--text-heading);
}
.setting-input:focus { border-color: var(--color-accent); box-shadow: 0 0 12px rgba(37, 99, 235, 0.3); }
.color-control {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.8rem;
}
.color-picker-shell {
  position: relative;
  display: inline-flex;
  width: fit-content;
  min-width: 10.25rem;
  height: 3rem;
  align-items: center;
  gap: 0.7rem;
  cursor: pointer;
  overflow: hidden;
  border-radius: 0.9rem;
  border: 2px solid var(--border);
  background: var(--bg-input);
  padding: 0 0.9rem 0 0.5rem;
  box-shadow: 0 12px 26px rgba(0, 0, 0, 0.12);
  transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
}
.color-picker-shell:hover {
  border-color: var(--color-accent);
  box-shadow: 0 12px 28px color-mix(in srgb, var(--color-accent) 20%, transparent);
  transform: translateY(-1px);
}
.color-preview {
  display: inline-flex;
  width: 2rem;
  height: 2rem;
  flex: 0 0 auto;
  border-radius: 0.65rem;
  border: 1px solid color-mix(in srgb, var(--picked-color) 38%, white);
  background:
    linear-gradient(135deg, rgba(255, 255, 255, 0.18), transparent),
    var(--picked-color);
  box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.08);
}
.color-picker-text {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  color: var(--text-heading);
  font-size: 0.84rem;
  font-weight: 900;
  white-space: nowrap;
}
.color-picker-shell input {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
  opacity: 0;
}
.color-code {
  width: min(12rem, 100%);
}
.upload-btn {
  background: var(--bg-input);
  color: var(--text-heading);
  border: 1px solid var(--border);
}
.upload-btn:hover { border-color: var(--color-accent); color: var(--color-accent); }
.login-image-control {
  display: grid;
  gap: 1rem;
}
.login-image-preview {
  display: flex;
  width: min(28rem, 100%);
  aspect-ratio: 16 / 9;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  border: 2px dashed color-mix(in srgb, var(--color-accent) 38%, var(--border));
  border-radius: 1rem;
  background: var(--bg-input);
}
.login-image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.login-image-empty {
  display: grid;
  gap: 0.45rem;
  place-items: center;
  color: var(--text-muted);
  font-size: 0.86rem;
  font-weight: 800;
}
.login-image-empty .material-symbols-outlined {
  color: var(--color-accent);
  font-size: 2rem;
}
.navbar-brand-control {
  display: grid;
  gap: 1rem;
}
.brand-mode-toggle {
  display: inline-grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.35rem;
  width: min(18rem, 100%);
  border: 1px solid var(--border);
  border-radius: 0.75rem;
  background: var(--bg-input);
  padding: 0.3rem;
}
.brand-mode-toggle button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  min-height: 2.25rem;
  border-radius: 0.55rem;
  color: var(--text-muted);
  cursor: pointer;
  font-size: 0.78rem;
  font-weight: 900;
  transition: background 0.18s ease, color 0.18s ease, transform 0.18s ease;
}
.brand-mode-toggle button:hover {
  color: var(--text-heading);
  transform: translateY(-1px);
}
.brand-mode-toggle button.active {
  background: var(--color-accent);
  color: var(--text-btn);
}
.navbar-text-fields {
  display: grid;
  gap: 0.8rem;
  max-width: 34rem;
}
.navbar-text-fields label {
  display: grid;
  gap: 0.4rem;
}
.navbar-text-fields span {
  color: var(--text-heading);
  font-size: 0.78rem;
  font-weight: 900;
}
.navbar-icon-select {
  min-height: 42px;
}
.navbar-icon-select :deep(.multiselect__tags) {
  min-height: 42px;
  border: 1px solid var(--border);
  border-radius: 0.5rem;
  background: var(--bg-input);
  padding: 7px 40px 0 12px;
}
.navbar-icon-select.multiselect--active :deep(.multiselect__tags) {
  border-color: var(--color-accent);
  background: var(--bg-card);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-accent) 14%, transparent);
}
.navbar-icon-select :deep(.multiselect__single),
.navbar-icon-select :deep(.multiselect__input),
.navbar-icon-select :deep(.multiselect__placeholder) {
  min-height: 24px;
  margin: 0;
  background: transparent;
  color: var(--text-heading);
  font-size: 0.875rem;
  line-height: 24px;
}
.navbar-icon-select :deep(.multiselect__placeholder) {
  color: var(--text-muted);
}
.navbar-icon-select :deep(.multiselect__content-wrapper) {
  border-color: var(--border);
  background: var(--bg-card);
}
.navbar-icon-select :deep(.multiselect__option--highlight) {
  background: var(--color-accent);
}
.icon-select-option {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}
.icon-select-option .material-symbols-outlined {
  color: var(--color-accent);
  font-size: 1.15rem;
}
.navbar-logo-preview {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 10.5rem;
  height: 4.25rem;
  overflow: hidden;
  border: 2px dashed color-mix(in srgb, var(--color-accent) 40%, var(--border));
  border-radius: 0.9rem;
  background: var(--bg-input);
}
.navbar-logo-preview img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 0.55rem;
}
.navbar-logo-preview .text-preview {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
  text-align: left;
}
.navbar-logo-preview .preview-logo-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  flex: 0 0 auto;
  border: 1px solid color-mix(in srgb, var(--color-accent) 42%, transparent);
  border-radius: 999px;
  background: color-mix(in srgb, var(--color-accent) 14%, transparent);
  color: var(--color-accent);
  font-size: 1.25rem;
}
.navbar-logo-preview .preview-logo-text {
  display: grid;
  gap: 0.15rem;
}
.navbar-logo-preview strong {
  color: var(--text-heading);
  font-size: 0.95rem;
  font-weight: 950;
}
.navbar-logo-preview span {
  color: var(--text-muted);
  font-size: 0.65rem;
  font-weight: 900;
  text-transform: uppercase;
}
.save-bar {
  background: var(--bg-card);
  border: 1px solid var(--border);
  backdrop-filter: blur(12px);
}
.save-btn {
  background: var(--color-accent);
  color: var(--text-btn);
  box-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
}
.toggle-switch {
  width: 3rem;
  height: 1.65rem;
  border-radius: 999px;
  padding: 0.2rem;
  background: var(--bg-input);
  border: 1px solid var(--border);
  transition: background 0.2s ease, border-color 0.2s ease;
}
.toggle-switch span {
  display: block;
  width: 1.15rem;
  height: 1.15rem;
  border-radius: 999px;
  background: var(--text-muted);
  transition: transform 0.2s ease, background 0.2s ease;
}
.toggle-switch.active {
  background: rgba(37, 99, 235, 0.16);
  border-color: var(--color-accent);
}
.toggle-switch.active span {
  transform: translateX(1.32rem);
  background: var(--color-accent);
}
.checkbox-box {
  display: inline-flex;
  width: 1.25rem;
  height: 1.25rem;
  align-items: center;
  justify-content: center;
  border-radius: 0.35rem;
  border: 1px solid var(--border);
  background: var(--bg-input);
  color: transparent;
  transition: all 0.2s ease;
}
input:checked + .checkbox-box {
  border-color: var(--color-accent);
  background: var(--color-accent);
  color: var(--text-btn);
}
.message-box {
  display: flex;
  align-items: flex-start;
  gap: 0.65rem;
  border-radius: 0.75rem;
  padding: 0.9rem 1rem;
  font-size: 0.86rem;
  font-weight: 700;
}
.message-box.success {
  border: 1px solid rgba(34, 197, 94, 0.28);
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}
.message-box.error {
  border: 1px solid rgba(239, 68, 68, 0.28);
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
