@php
  $universities = $adminMentorsData['universities'] ?? [];
  $programs = $adminMentorsData['programs'] ?? [];
@endphp

<style>
  #createMentorModal select,
  #createMentorModal select option {
    background-color: #1e293b !important;
    color: #f8fafc !important;
    border-color: #334155 !important;
  }
  #createMentorModal select option:hover,
  #createMentorModal select option:focus {
    background-color: #334155 !important;
    color: #ffffff !important;
  }
</style>

<dialog class="admin-bookings-dialog" id="createMentorModal" style="max-height: 90vh; overflow: hidden; padding: 0; border: none; border-radius: 12px; background: transparent;">
  <div class="modal-card admin-bookings-modal" role="dialog" aria-modal="true" aria-labelledby="createMentorModalTitle" style="max-width: 640px; max-height: 88vh; display: flex; flex-direction: column; overflow: hidden;">
    <button class="admin-bookings-modal__close" id="createMentorModalClose" type="button" aria-label="Close">×</button>

    <div class="admin-bookings-modal__header" style="flex-shrink: 0;">
      <div>
        <p class="admin-bookings-modal__eyebrow">Admin Actions</p>
        <h3 id="createMentorModalTitle">Create Mentor Account</h3>
        <p class="admin-bookings-modal__subtitle">Manually add a mentor account and automatically dispatch login details via email.</p>
      </div>
      <div class="admin-bookings-modal__status hidden" id="createMentorModalStatus"></div>
    </div>

    <div class="admin-bookings-modal__body" style="overflow-y: auto; max-height: calc(88vh - 110px); padding: 20px 24px 28px;">
      <form class="admin-bookings-form" id="createMentorForm" action="{{ route('admin.mentors.store') }}" method="POST">
        @csrf
        <div class="admin-bookings-form__grid">
          <label style="grid-column: span 2;">
            <span>Full Name <span style="color:#ef4444;">*</span></span>
            <input type="text" name="name" id="mentorNameInput" placeholder="e.g. Dr. Alex Smith" required />
            <small class="admin-bookings-form__error" data-error-for="name"></small>
          </label>

          <label style="grid-column: span 2;">
            <span>Email Address <span style="color:#ef4444;">*</span></span>
            <input type="email" name="email" id="mentorEmailInput" placeholder="e.g. alex.smith@example.com" required />
            <small class="admin-bookings-form__error" data-error-for="email"></small>
          </label>

          <label style="grid-column: span 2;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <span>Initial Password</span>
              <button type="button" id="generateMentorPasswordBtn" class="ghost-btn" style="padding: 2px 8px; font-size: 12px; height: auto;">🎲 Generate Random</button>
            </div>
            <input type="text" name="password" id="mentorPasswordInput" placeholder="Leave blank to auto-generate" minlength="8" />
            <small style="color: #9ca3af; font-size: 11px; margin-top: 4px; display: block;">If left blank, a secure random password will be auto-generated and emailed.</small>
            <small class="admin-bookings-form__error" data-error-for="password"></small>
          </label>

          <label style="grid-column: span 2;">
            <span>Mentor Type <span style="color:#ef4444;">*</span></span>
            <select name="mentor_type" id="mentorTypeSelect" required>
              <option value="graduate">Graduate Student</option>
              <option value="professional">Professional / Alum</option>
            </select>
            <small class="admin-bookings-form__error" data-error-for="mentor_type"></small>
          </label>

          <label style="grid-column: span 2;">
            <span>University / School (Searchable) <span style="color:#ef4444;">*</span></span>
            <input type="hidden" name="university_id" id="mentorUniversityId" value="" required />
            
            <div class="searchable-mentor-uni-wrapper" style="position: relative; margin-top: 4px;">
              <input type="text" id="mentorUniSearchInput" placeholder="🔍 Type to search university..." autocomplete="off" style="width: 100%; padding-right: 30px;" required />
              <button type="button" id="mentorUniClearBtn" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; display: none;">✕</button>
              
              <div id="mentorUniDropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; max-height: 200px; overflow-y: auto; background: #1e293b; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); z-index: 100; margin-top: 4px;">
                <div class="mentor-uni-options-list"></div>
              </div>
            </div>
            <small class="admin-bookings-form__error" data-error-for="university_id"></small>
          </label>

          <label>
            <span>Title / Tagline</span>
            <input type="text" name="title" id="mentorTitleInput" placeholder="e.g. MD Candidate @ Harvard" />
            <small class="admin-bookings-form__error" data-error-for="title"></small>
          </label>

          <label>
            <span>Grad School Display</span>
            <input type="text" name="grad_school_display" id="mentorGradSchoolInput" placeholder="e.g. Harvard Medical School" />
            <small class="admin-bookings-form__error" data-error-for="grad_school_display"></small>
          </label>

          <label>
            <span>University Program <span style="color:#ef4444;">*</span></span>
            <select name="university_program_id" id="mentorProgramSelect" required>
              <option value="">-- Select Program --</option>
              @foreach ($programs as $prog)
                <option value="{{ $prog['id'] }}" data-university-id="{{ $prog['university_id'] }}">{{ $prog['name'] }}</option>
              @endforeach
            </select>
            <small class="admin-bookings-form__error" data-error-for="university_program_id"></small>
          </label>

          <label>
            <span>Edu Email Address</span>
            <input type="email" name="edu_email" id="mentorEduEmailInput" placeholder="e.g. alex.smith@harvard.edu" />
            <small class="admin-bookings-form__error" data-error-for="edu_email"></small>
          </label>
        </div>

        <div style="margin-top: 28px; display: flex; justify-content: flex-end; gap: 12px;">
          <button class="ghost-btn" id="createMentorModalCancel" type="button">Cancel</button>
          <button class="primary-btn" id="createMentorSubmitBtn" type="submit">Create Mentor & Send Email</button>
        </div>
      </form>
    </div>
  </div>
</dialog>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('createMentorModal');
  const openBtn = document.getElementById('openCreateMentorModalBtn');
  const closeBtn = document.getElementById('createMentorModalClose');
  const cancelBtn = document.getElementById('createMentorModalCancel');
  const form = document.getElementById('createMentorForm');
  const statusEl = document.getElementById('createMentorModalStatus');
  const submitBtn = document.getElementById('createMentorSubmitBtn');

  const universitiesData = @json($universities);

  // Searchable dropdown elements
  const uniHiddenInput = document.getElementById('mentorUniversityId');
  const uniSearchInput = document.getElementById('mentorUniSearchInput');
  const uniClearBtn = document.getElementById('mentorUniClearBtn');
  const uniDropdown = document.getElementById('mentorUniDropdown');
  const uniOptionsList = uniDropdown ? uniDropdown.querySelector('.mentor-uni-options-list') : null;
  const programSelect = document.getElementById('mentorProgramSelect');

  // Password Generator
  const genPasswordBtn = document.getElementById('generateMentorPasswordBtn');
  const passwordInput = document.getElementById('mentorPasswordInput');

  if (genPasswordBtn && passwordInput) {
    genPasswordBtn.addEventListener('click', () => {
      const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
      let randPass = '';
      for (let i = 0; i < 12; i++) {
        randPass += chars.charAt(Math.floor(Math.random() * chars.length));
      }
      passwordInput.type = 'text';
      passwordInput.value = randPass;
    });
  }

  // Filter program dropdown by selected university
  const filterPrograms = (uniId) => {
    if (!programSelect) return;
    const options = programSelect.querySelectorAll('option');
    options.forEach(opt => {
      if (!opt.value) return; // Keep "-- Select Program --"
      const optUniId = opt.getAttribute('data-university-id');
      if (!uniId || optUniId == uniId) {
        opt.style.display = '';
      } else {
        opt.style.display = 'none';
      }
    });
  };

  // University Searchable Dropdown Logic
  const renderUniOptions = (filterText = '') => {
    if (!uniOptionsList) return;
    uniOptionsList.innerHTML = '';

    const query = filterText.toLowerCase().trim();
    let matches = universitiesData;

    if (query !== '') {
      matches = universitiesData.filter(u => u.name.toLowerCase().includes(query));
    }

    if (matches.length === 0) {
      const emptyItem = document.createElement('div');
      emptyItem.style.padding = '10px 14px';
      emptyItem.style.color = '#9ca3af';
      emptyItem.style.fontSize = '13px';
      emptyItem.textContent = 'No matching university found';
      uniOptionsList.appendChild(emptyItem);
      return;
    }

    // Show top 5 options if no filter text, otherwise show all matches (up to 30)
    const displayList = query === '' ? matches.slice(0, 5) : matches.slice(0, 30);

    displayList.forEach(u => {
      const item = document.createElement('div');
      item.className = 'uni-option-item';
      item.style.padding = '10px 14px';
      item.style.cursor = 'pointer';
      item.style.fontSize = '13px';
      item.style.borderBottom = '1px solid rgba(255,255,255,0.05)';
      item.style.transition = 'background 0.15s ease';
      item.textContent = u.name;

      item.addEventListener('mouseenter', () => item.style.background = '#334155');
      item.addEventListener('mouseleave', () => item.style.background = 'transparent');

      item.addEventListener('click', () => {
        uniHiddenInput.value = u.id;
        uniSearchInput.value = u.name;
        uniDropdown.style.display = 'none';
        if (uniClearBtn) uniClearBtn.style.display = 'block';
        filterPrograms(u.id);
      });

      uniOptionsList.appendChild(item);
    });
  };

  if (uniSearchInput && uniDropdown) {
    uniSearchInput.addEventListener('focus', () => {
      renderUniOptions(uniSearchInput.value);
      uniDropdown.style.display = 'block';
    });

    uniSearchInput.addEventListener('input', () => {
      renderUniOptions(uniSearchInput.value);
      uniDropdown.style.display = 'block';
      if (uniClearBtn) uniClearBtn.style.display = uniSearchInput.value ? 'block' : 'none';
    });

    if (uniClearBtn) {
      uniClearBtn.addEventListener('click', () => {
        uniSearchInput.value = '';
        uniHiddenInput.value = '';
        uniClearBtn.style.display = 'none';
        filterPrograms('');
        renderUniOptions('');
        uniDropdown.style.display = 'block';
      });
    }

    document.addEventListener('click', (e) => {
      if (!e.target.closest('.searchable-mentor-uni-wrapper')) {
        uniDropdown.style.display = 'none';
      }
    });
  }

  if (!modal || !form) return;

  const showModal = () => {
    form.reset();
    if (uniHiddenInput) uniHiddenInput.value = '';
    if (uniSearchInput) uniSearchInput.value = '';
    if (uniClearBtn) uniClearBtn.style.display = 'none';
    filterPrograms('');
    clearErrors();
    if (statusEl) {
      statusEl.classList.add('hidden');
      statusEl.textContent = '';
    }
    if (typeof modal.showModal === 'function') {
      modal.showModal();
    } else {
      modal.setAttribute('open', 'true');
    }
  };

  const closeModal = () => {
    if (typeof modal.close === 'function') {
      modal.close();
    } else {
      modal.removeAttribute('open');
    }
  };

  if (openBtn) openBtn.addEventListener('click', showModal);
  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

  const clearErrors = () => {
    form.querySelectorAll('.admin-bookings-form__error').forEach(el => el.textContent = '');
  };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    const uniVal = uniHiddenInput ? uniHiddenInput.value : '';
    const progVal = programSelect ? programSelect.value : '';

    let hasClientErr = false;
    if (!uniVal) {
      const errEl = form.querySelector('[data-error-for="university_id"]');
      if (errEl) errEl.textContent = 'Please search and select a university / school.';
      hasClientErr = true;
    }
    if (!progVal) {
      const errEl = form.querySelector('[data-error-for="university_program_id"]');
      if (errEl) errEl.textContent = 'Please select a university program.';
      hasClientErr = true;
    }

    if (hasClientErr) {
      if (statusEl) {
        statusEl.classList.remove('hidden');
        statusEl.style.color = '#ef4444';
        statusEl.textContent = 'Please select a required university and program.';
      }
      if (window.AppToast && typeof window.AppToast.show === 'function') {
        window.AppToast.show({
          type: 'error',
          title: 'Validation Error',
          message: 'University and Program are required.'
        });
      }
      return;
    }

    if (statusEl) {
      statusEl.classList.remove('hidden');
      statusEl.style.color = '#3b82f6';
      statusEl.textContent = 'Creating mentor account and sending email credentials...';
    }
    submitBtn.disabled = true;

    try {
      const formData = new FormData(form);
      const response = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: formData
      });

      const data = await response.json();

      if (response.ok) {
        const successMsg = data.message || 'Mentor account created successfully!';
        if (statusEl) {
          statusEl.style.color = '#10b981';
          statusEl.textContent = successMsg;
        }
        if (window.AppToast && typeof window.AppToast.show === 'function') {
          window.AppToast.show({
            type: 'success',
            title: 'Mentor Created',
            message: successMsg
          });
        }
        setTimeout(() => {
          closeModal();
          window.location.reload();
        }, 1000);
      } else {
        submitBtn.disabled = false;
        if (response.status === 422 && data.errors) {
          const firstErr = Object.values(data.errors)[0]?.[0] || 'Please fix the errors below.';
          if (statusEl) {
            statusEl.style.color = '#ef4444';
            statusEl.textContent = firstErr;
          }
          if (window.AppToast && typeof window.AppToast.show === 'function') {
            window.AppToast.show({
              type: 'error',
              title: 'Validation Error',
              message: firstErr
            });
          }
          Object.keys(data.errors).forEach(field => {
            const errEl = form.querySelector(`[data-error-for="${field}"]`);
            if (errEl) {
              errEl.textContent = data.errors[field][0];
            }
          });
        } else {
          const errMsg = data.message || 'An error occurred while creating the account.';
          if (statusEl) {
            statusEl.style.color = '#ef4444';
            statusEl.textContent = errMsg;
          }
          if (window.AppToast && typeof window.AppToast.show === 'function') {
            window.AppToast.show({
              type: 'error',
              title: 'Creation Failed',
              message: errMsg
            });
          }
        }
      }
    } catch (err) {
      submitBtn.disabled = false;
      const netErr = 'Network error. Please try again.';
      if (statusEl) {
        statusEl.style.color = '#ef4444';
        statusEl.textContent = netErr;
      }
      if (window.AppToast && typeof window.AppToast.show === 'function') {
        window.AppToast.show({
          type: 'error',
          title: 'Network Error',
          message: netErr
        });
      }
    }
  });
});
</script>
