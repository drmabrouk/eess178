/**
 * EESS Unified User & Employee Management Modal Controller
 * Synchronizes client-side behavior across System User Management, HR, and Employee Profile
 */

(function () {
    'use strict';

    window.eessCurrentStep = 1;
    window.eessIsEditMode = false;
    var eessAjaxUrl = (typeof ajaxurl !== 'undefined') ? ajaxurl : '/wp-admin/admin-ajax.php';

    window.eessOpenUnifiedUserModal = function(mode, userId) {
        mode = mode || 'add_user';
        userId = parseInt(userId, 10) || 0;

        window.eessIsEditMode = (mode === 'edit_user' || mode === 'edit_employee_profile') && userId > 0;

        var modal = document.getElementById('unified-user-modal');
        if (!modal) return;

        var form = document.getElementById('eess-unified-user-form');
        if (form) form.reset();

        var uIdEl = document.getElementById('u_user_id');
        var uModeEl = document.getElementById('u_form_mode');
        if (uIdEl) uIdEl.value = userId;
        if (uModeEl) uModeEl.value = mode;

        // Reset error labels
        var errors = document.querySelectorAll('.eess-field-error');
        errors.forEach(function(el) { el.style.display = 'none'; });

        // Set title according to mode
        var titleEl = document.getElementById('u_modal_title');
        if (titleEl) {
            if (mode === 'add_user') titleEl.innerText = '➕ إضافة مستخدم جديد في النظام';
            else if (mode === 'add_employee') titleEl.innerText = '➕ إضافة موظف جديد بملف الموارد البشرية';
            else if (mode === 'edit_employee_profile') titleEl.innerText = '⚙️ تعديل وتزامن معلومات الموظف والحساب';
            else titleEl.innerText = '✏️ تعديل بيانات حساب وتعيينات الموظف';
        }

        // Passwords & Change Password Toggle
        var passRow = document.getElementById('u_password_row');
        var passToggleBtn = document.getElementById('u_change_pass_toggle_container');
        var passReq = document.getElementById('u_pass_req');
        var passConfReq = document.getElementById('u_pass_confirm_req');
        var passInput = document.getElementById('u_user_pass');
        var passConfInput = document.getElementById('u_user_pass_confirm');
        var usernameInput = document.getElementById('u_username');

        if (window.eessIsEditMode) {
            if (passRow) passRow.style.display = 'none';
            if (passToggleBtn) passToggleBtn.style.display = 'block';
            if (passReq) passReq.style.display = 'none';
            if (passConfReq) passConfReq.style.display = 'none';
            if (passInput) passInput.required = false;
            if (passConfInput) passConfInput.required = false;
            if (usernameInput) usernameInput.readOnly = true;
        } else {
            if (passRow) passRow.style.display = 'grid';
            if (passToggleBtn) passToggleBtn.style.display = 'none';
            if (passReq) passReq.style.display = 'inline';
            if (passConfReq) passConfReq.style.display = 'inline';
            if (passInput) passInput.required = true;
            if (passConfInput) passConfInput.required = true;
            if (usernameInput) usernameInput.readOnly = false;
        }

        // Clear Avatar Preview
        var photoPreview = document.getElementById('u_photo_preview');
        if (photoPreview) {
            photoPreview.src = 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%2364748b\'><path d=\'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z\'/></svg>';
        }

        // Go to Step 1
        window.eessGoToStep(1);

        // If Edit Mode, Fetch User Data
        if (window.eessIsEditMode) {
            window.eessLoadUserData(userId);
        }

        modal.style.display = 'flex';
    };

    window.eessCloseUnifiedUserModal = function() {
        var modal = document.getElementById('unified-user-modal');
        if (modal) modal.style.display = 'none';
    };

    window.eessGoToStep = function(step) {
        if (step === 2) {
            if (!window.eessValidateStep1()) return;
        }

        window.eessCurrentStep = step;

        var step1Container = document.getElementById('u_step_1_container');
        var step2Container = document.getElementById('u_step_2_container');
        var ind1 = document.getElementById('u_indicator_step1');
        var ind2 = document.getElementById('u_indicator_step2');
        var btnPrev = document.getElementById('u_btn_prev');
        var btnNext = document.getElementById('u_btn_next');
        var btnSave = document.getElementById('u_btn_save');

        if (step === 1) {
            if (step1Container) step1Container.style.display = 'block';
            if (step2Container) step2Container.style.display = 'none';

            if (ind1) {
                ind1.style.background = 'var(--eess-black, #000000)';
                ind1.style.color = '#ffffff';
            }
            if (ind2) {
                ind2.style.background = 'var(--eess-bg-hover, #f1f5f9)';
                ind2.style.color = 'var(--eess-muted, #64748b)';
            }

            if (btnPrev) btnPrev.style.display = 'none';
            if (btnNext) btnNext.style.display = 'inline-flex';
            if (btnSave) btnSave.style.display = 'none';
        } else {
            if (step1Container) step1Container.style.display = 'none';
            if (step2Container) step2Container.style.display = 'block';

            if (ind1) {
                ind1.style.background = 'var(--eess-accent, #8b1e1e)';
                ind1.style.color = '#ffffff';
            }
            if (ind2) {
                ind2.style.background = 'var(--eess-black, #000000)';
                ind2.style.color = '#ffffff';
            }

            if (btnPrev) btnPrev.style.display = 'inline-flex';
            if (btnNext) btnNext.style.display = 'none';
            if (btnSave) btnSave.style.display = 'inline-flex';

            window.eessOnRoleChanged();
        }
    };

    window.eessToggleChangePassword = function() {
        var passRow = document.getElementById('u_password_row');
        if (passRow) {
            if (passRow.style.display === 'none' || passRow.style.display === '') {
                passRow.style.display = 'grid';
            } else {
                passRow.style.display = 'none';
            }
        }
    };

    window.eessSyncUsername = function(empInput) {
        if (!empInput) return;
        var clean = empInput.value.replace(/^(EMP|EMP-|_)+/i, '').trim();
        empInput.value = clean;
        var usernameInput = document.getElementById('u_username');
        if (usernameInput) usernameInput.value = clean;
        var errEl = document.getElementById('err_u_employee_id');
        if (errEl && clean !== '') errEl.style.display = 'none';
    };

    window.eessPreviewAvatar = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var photoPreview = document.getElementById('u_photo_preview');
                if (photoPreview) photoPreview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.eessValidateField = function(input) {
        if (!input) return;
        var errEl = document.getElementById('err_' + input.id);
        if (input.checkValidity() && input.value.trim() !== '') {
            if (errEl) errEl.style.display = 'none';
        }
    };

    window.eessValidateStep1 = function() {
        var valid = true;
        var firstName = document.getElementById('u_first_name');
        var lastName = document.getElementById('u_last_name');
        var username = document.getElementById('u_username');
        var email = document.getElementById('u_user_email');
        var phone = document.getElementById('u_phone_number');
        var empId = document.getElementById('u_employee_id');
        var pass = document.getElementById('u_user_pass');
        var passConf = document.getElementById('u_user_pass_confirm');
        var passRow = document.getElementById('u_password_row');

        if (firstName && !firstName.value.trim()) {
            var errFN = document.getElementById('err_u_first_name');
            if (errFN) errFN.style.display = 'block';
            valid = false;
        } else {
            var errFN2 = document.getElementById('err_u_first_name');
            if (errFN2) errFN2.style.display = 'none';
        }

        if (lastName && !lastName.value.trim()) {
            var errLN = document.getElementById('err_u_last_name');
            if (errLN) errLN.style.display = 'block';
            valid = false;
        } else {
            var errLN2 = document.getElementById('err_u_last_name');
            if (errLN2) errLN2.style.display = 'none';
        }

        if (username && empId && !username.value.trim() && empId.value.trim()) {
            username.value = empId.value.trim();
        }

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailPattern.test(email.value.trim())) {
            var errEM = document.getElementById('err_u_user_email');
            if (errEM) errEM.style.display = 'block';
            valid = false;
        } else {
            var errEM2 = document.getElementById('err_u_user_email');
            if (errEM2) errEM2.style.display = 'none';
        }

        if (phone && !phone.value.trim()) {
            var errPH = document.getElementById('err_u_phone_number');
            if (errPH) errPH.style.display = 'block';
            valid = false;
        } else {
            var errPH2 = document.getElementById('err_u_phone_number');
            if (errPH2) errPH2.style.display = 'none';
        }

        if (empId && !empId.value.trim()) {
            var errEI = document.getElementById('err_u_employee_id');
            if (errEI) errEI.style.display = 'block';
            valid = false;
        } else {
            var errEI2 = document.getElementById('err_u_employee_id');
            if (errEI2) errEI2.style.display = 'none';
        }

        // Validate password if visible
        if (passRow && passRow.style.display !== 'none') {
            if (!window.eessIsEditMode && pass && pass.value.length < 6) {
                var errP = document.getElementById('err_u_user_pass');
                if (errP) errP.style.display = 'block';
                valid = false;
            } else {
                var errP2 = document.getElementById('err_u_user_pass');
                if (errP2) errP2.style.display = 'none';
            }

            if (pass && passConf && pass.value !== passConf.value) {
                var errPC = document.getElementById('err_u_user_pass_confirm');
                if (errPC) errPC.style.display = 'block';
                valid = false;
            } else {
                var errPC2 = document.getElementById('err_u_user_pass_confirm');
                if (errPC2) errPC2.style.display = 'none';
            }
        }

        return valid;
    };

    window.eessCheckUniqueness = function(field) {
        var uIdEl = document.getElementById('u_user_id');
        var userId = uIdEl ? uIdEl.value : 0;
        var val = '';
        if (field === 'username') val = (document.getElementById('u_username') || {}).value || '';
        if (field === 'email') val = (document.getElementById('u_user_email') || {}).value || '';
        if (field === 'employee_id') val = (document.getElementById('u_employee_id') || {}).value || '';

        val = val.trim();
        if (!val) return;

        var nonceEl = document.querySelector('#eess-unified-user-form [name="sm_nonce"]');
        var nonce = nonceEl ? nonceEl.value : '';

        var formData = new FormData();
        formData.append('action', 'eess_check_user_uniqueness');
        formData.append('sm_nonce', nonce);
        formData.append('field', field);
        formData.append('value', val);
        formData.append('user_id', userId);

        fetch(eessAjaxUrl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success && res.data.exists) {
                var errEl = document.getElementById('err_u_' + (field === 'email' ? 'user_email' : field));
                if (errEl) {
                    errEl.innerText = res.data.message || 'القيمة مُستخدمة سابقاً في النظام.';
                    errEl.style.display = 'block';
                }
            }
        })
        .catch(function(e) { console.error(e); });
    };

    window.eessOnRoleChanged = function() {
        var roleEl = document.getElementById('u_user_role');
        var role = roleEl ? roleEl.value : '';
        var subjWrapper = document.getElementById('u_subject_wrapper');
        var deptWrapper = document.getElementById('u_department_wrapper');

        if (role === 'sm_teacher' || role === 'teachers' || role === 'sm_hod') {
            if (subjWrapper) subjWrapper.style.display = 'block';
        } else {
            if (subjWrapper) subjWrapper.style.display = 'none';
        }

        if (role === 'sm_principal' || role === 'school_manager' || role === 'administrator') {
            if (deptWrapper) deptWrapper.style.display = 'none';
        } else {
            if (deptWrapper) deptWrapper.style.display = 'block';
        }
    };

    window.eessOnScopeChanged = function() {
        var scopeEl = document.getElementById('u_access_scope');
        var scope = scopeEl ? scopeEl.value : '';
        var schoolSelect = document.getElementById('u_school_id');

        if (scope === 'institution') {
            if (schoolSelect) schoolSelect.required = false;
        } else {
            if (schoolSelect) schoolSelect.required = true;
        }
    };

    window.eessOnInstitutionChanged = function() {
        var instEl = document.getElementById('u_institution_id');
        var instId = instEl ? instEl.value : '';
        var schoolSelect = document.getElementById('u_school_id');

        if (!schoolSelect) return;

        for (var i = 0; i < schoolSelect.options.length; i++) {
            var opt = schoolSelect.options[i];
            if (!opt.value) continue;

            var optInst = opt.getAttribute('data-institution');
            if (!instId || optInst === instId) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        }
    };

    window.eessLoadUserData = function(userId) {
        var nonceEl = document.querySelector('#eess-unified-user-form [name="sm_nonce"]');
        var nonce = nonceEl ? nonceEl.value : '';

        var formData = new FormData();
        formData.append('action', 'eess_get_user_unified');
        formData.append('sm_nonce', nonce);
        formData.append('user_id', userId);

        fetch(eessAjaxUrl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success && res.data) {
                var u = res.data;
                if (document.getElementById('u_first_name')) document.getElementById('u_first_name').value = u.first_name || '';
                if (document.getElementById('u_last_name')) document.getElementById('u_last_name').value = u.last_name || '';
                var cleanEmpId = (u.employee_id || u.user_login || '').replace(/^(EMP|EMP-|_)+/i, '').trim();
                if (document.getElementById('u_employee_id')) document.getElementById('u_employee_id').value = cleanEmpId;
                if (document.getElementById('u_username')) document.getElementById('u_username').value = cleanEmpId;
                if (document.getElementById('u_user_email')) document.getElementById('u_user_email').value = u.user_email || '';
                if (u.country_code && document.getElementById('u_country_code')) {
                    document.getElementById('u_country_code').value = u.country_code;
                }
                if (document.getElementById('u_phone_number')) document.getElementById('u_phone_number').value = u.phone_number || '';
                if (document.getElementById('u_user_status')) document.getElementById('u_user_status').value = u.user_status || 'active';
                if (document.getElementById('u_civil_id')) document.getElementById('u_civil_id').value = u.civil_id || '';

                var normalizedRole = u.role || 'sm_teacher';
                if (normalizedRole === 'teachers') normalizedRole = 'sm_teacher';
                if (normalizedRole === 'school_manager') normalizedRole = 'sm_principal';
                if (normalizedRole === 'educational_supervisor') normalizedRole = 'sm_supervisor';
                if (normalizedRole === 'clinic') normalizedRole = 'sm_clinic';
                if (normalizedRole === 'accountant') normalizedRole = 'sm_accountant';

                if (document.getElementById('u_user_role')) document.getElementById('u_user_role').value = normalizedRole;
                if (document.getElementById('u_access_scope')) document.getElementById('u_access_scope').value = u.access_scope || 'school';
                if (document.getElementById('u_institution_id')) document.getElementById('u_institution_id').value = u.institution_id || '';
                if (document.getElementById('u_school_id')) document.getElementById('u_school_id').value = u.school_id || '';
                if (document.getElementById('u_department')) document.getElementById('u_department').value = u.department || '';
                if (document.getElementById('u_specialization')) document.getElementById('u_specialization').value = u.specialization || '';
                if (document.getElementById('u_official_title')) document.getElementById('u_official_title').value = u.official_title || '';

                if (u.photo_url && document.getElementById('u_photo_preview')) {
                    document.getElementById('u_photo_preview').src = u.photo_url;
                }

                window.eessOnInstitutionChanged();
                window.eessOnRoleChanged();
            }
        })
        .catch(function(e) { console.error(e); });
    };

    window.eessSubmitUnifiedUserForm = function() {
        var roleEl = document.getElementById('u_user_role');
        var role = roleEl ? roleEl.value : '';
        var instEl = document.getElementById('u_institution_id');
        var inst = instEl ? instEl.value : '';

        if (!role) {
            var errR = document.getElementById('err_u_user_role');
            if (errR) errR.style.display = 'block';
            return;
        } else {
            var errR2 = document.getElementById('err_u_user_role');
            if (errR2) errR2.style.display = 'none';
        }

        if (!inst) {
            var errI = document.getElementById('err_u_institution_id');
            if (errI) errI.style.display = 'block';
            return;
        } else {
            var errI2 = document.getElementById('err_u_institution_id');
            if (errI2) errI2.style.display = 'none';
        }

        var saveBtn = document.getElementById('u_btn_save');
        if (saveBtn) {
            saveBtn.innerText = '⏳ جاري الحفظ والتزامن...';
            saveBtn.disabled = true;
        }

        var form = document.getElementById('eess-unified-user-form');
        var formData = new FormData(form);
        formData.append('action', 'eess_save_user_unified');

        fetch(eessAjaxUrl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                alert('✅ ' + (res.data.message || 'تم حفظ وتزامن بيانات الموظف بنجاح في المنصة الرقمية.'));
                window.eessCloseUnifiedUserModal();
                location.reload();
            } else {
                alert('❌ خطأ: ' + (res.data || 'حدث خطأ أثناء حفظ البيانات.'));
                if (saveBtn) {
                    saveBtn.innerText = '💾 حفظ وتزامن البيانات';
                    saveBtn.disabled = false;
                }
            }
        })
        .catch(function() {
            alert('❌ حدث خطأ غير متوقع في الاتصال بالسيرفر.');
            if (saveBtn) {
                saveBtn.innerText = '💾 حفظ وتزامن البيانات';
                saveBtn.disabled = false;
            }
        });
    };
})();
