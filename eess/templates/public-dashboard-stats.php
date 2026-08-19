<?php if (!defined('ABSPATH')) exit; ?>
<div class="sm-admin-panel" dir="rtl">
    <?php
    $user_roles = (array) wp_get_current_user()->roles;
    $is_parent = in_array('sm_parent', $user_roles) || in_array('sm_student', $user_roles);
    ?>

    <!-- Header Title & Quick Actions Bar -->
    <div style="background: #ffffff; padding: 20px 24px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="margin: 0 0 4px 0; font-size: 22px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #fee2e2; border-radius: 50%; color: #8b1e1e;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
                سجل المخالفات السلوكية
            </h2>
            <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 500;">
                متابعة وإدارة السلوك الطلابي والانضباط المدرسي
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <?php if (!$is_parent): ?>
                <!-- Export Reports Dropdown -->
                <div style="position: relative; display: inline-block;">
                    <button type="button" onclick="const d=document.getElementById('export-dropdown-menu'); d.style.display = d.style.display==='none'?'block':'none';" class="sm-btn sm-btn-outline" style="height: 40px; padding: 0 18px; border-radius: 9999px !important; border: 1px solid #cbd5e1; background: #ffffff; color: #1e293b; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        تصدير التقارير
                        <span style="font-size: 10px;">▼</span>
                    </button>
                    <div id="export-dropdown-menu" style="display: none; position: absolute; left: 0; top: 100%; margin-top: 6px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 50; width: 170px; overflow: hidden;">
                        <a href="<?php echo admin_url('admin-ajax.php?action=sm_export_violations_csv&nonce=' . wp_create_nonce('sm_export_action')); ?>" style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; color: #1e293b; font-size: 13px; font-weight: 600; text-decoration: none; border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                            📄 تصدير CSV
                        </a>
                        <a href="javascript:void(0)" onclick="exportViolationPDF()" style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; color: #1e293b; font-size: 13px; font-weight: 600; text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                            🖨️ طباعة / PDF
                        </a>
                    </div>
                </div>

                <!-- Green Outlined Import Button -->
                <button type="button" onclick="const f=document.getElementById('violation-import-form'); f.style.display = f.style.display==='none'?'block':'none';" class="sm-btn" style="height: 40px; padding: 0 18px; border-radius: 9999px !important; border: 1.5px solid #16a34a !important; background: transparent !important; color: #16a34a !important; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    استيراد
                </button>

                <?php if (current_user_can('تسجيل_مخالفة') || current_user_can('إدارة_المخالفات') || current_user_can('manage_options')): ?>
                <!-- Solid Red Log Violation Primary Button -->
                <button type="button" onclick="if(document.getElementById('sm-global-violation-modal')){document.getElementById('sm-global-violation-modal').style.display='flex';}" class="sm-btn" style="height: 40px; padding: 0 22px; border-radius: 9999px !important; background: #8b1e1e !important; color: #ffffff !important; border: none !important; font-size: 13px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    تسجيل مخالفة
                </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Bar - Flat Input Area -->
    <div style="background: #ffffff; padding: 18px 20px; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 20px;">
        <form id="violation-filter-form" method="get">
            <input type="hidden" name="page" value="sm-dashboard">
            <input type="hidden" name="sm_tab" value="stats">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; align-items: end;">
                <?php if (!$is_parent): ?>
                <!-- Student Search -->
                <div>
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #475569;">البحث (الاسم / الكود)</label>
                    <input type="text" name="student_search" value="<?php echo esc_attr($_GET['student_search'] ?? ''); ?>" placeholder="ابحث باسم الطالب أو الكود..." class="sm-input" style="height: 40px; font-size: 13px;">
                </div>

                <!-- Grade Filter -->
                <div>
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #475569;">الصف</label>
                    <select name="class_filter" class="sm-select" style="height: 40px; font-size: 13px;">
                        <option value="">جميع الصفوف</option>
                        <?php
                        global $wpdb;
                        $classes = $wpdb->get_col("SELECT DISTINCT class_name FROM {$wpdb->prefix}sm_students ORDER BY CAST(REPLACE(class_name, 'الصف ', '') AS UNSIGNED) ASC");
                        foreach ($classes as $c): ?>
                            <option value="<?php echo esc_attr($c); ?>" <?php selected(isset($_GET['class_filter']) && $_GET['class_filter'] == $c); ?>><?php echo esc_html($c); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Section Filter -->
                <div>
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #475569;">الشعبة</label>
                    <select name="section_filter" class="sm-select" style="height: 40px; font-size: 13px;">
                        <option value="">جميع الشعب</option>
                        <?php
                        $sections = $wpdb->get_col("SELECT DISTINCT section FROM {$wpdb->prefix}sm_students WHERE section != '' ORDER BY section ASC");
                        foreach ($sections as $s): ?>
                            <option value="<?php echo esc_attr($s); ?>" <?php selected(isset($_GET['section_filter']) && $_GET['section_filter'] == $s); ?>><?php echo esc_html($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Violation Type Filter -->
                <div>
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: #475569;">نوع المخالفة</label>
                    <select name="type_filter" class="sm-select" style="height: 40px; font-size: 13px;">
                        <option value="">جميع الأنواع</option>
                        <?php foreach (SM_Settings::get_violation_types() as $k => $v): ?>
                            <option value="<?php echo esc_attr($k); ?>" <?php selected(isset($_GET['type_filter']) && $_GET['type_filter'] == $k); ?>><?php echo esc_html($v); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Submit Filter Button -->
                <div>
                    <button type="submit" class="sm-btn" style="height: 40px; border-radius: 9999px !important; background: #8b1e1e !important; color: #ffffff !important; border: none; font-size: 13px; font-weight: 800; width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer;">
                        تطبيق الفلترة
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- CSV Import Form -->
    <div id="violation-import-form" style="display:none; background: #f8fafc; padding: 20px; border: 1px solid #cbd5e1; border-radius: 10px; margin-bottom: 20px;">
        <h3 style="margin-top:0; font-size: 14px; font-weight: 800; color: #0f172a;">استيراد سجلات المخالفات (CSV)</h3>
        <form method="post" enctype="multipart/form-data" onsubmit="return handleImportSubmit(this, 'sm_import_violations_csv')">
            <?php wp_nonce_field('sm_admin_action', 'sm_admin_nonce'); ?>
            <div class="sm-form-group">
                <label class="sm-label" style="font-size: 12px;">اختر ملف CSV:</label>
                <input type="file" name="csv_file" accept=".csv" required class="sm-input" style="height: auto; padding: 6px;">
            </div>
            <div style="display:flex; gap:10px; margin-top:15px;">
                <button type="submit" name="sm_import_violations_csv" class="sm-btn" style="background:#16a34a !important; height: 36px; padding: 0 16px;">بدء الاستيراد</button>
                <button type="button" onclick="this.parentElement.parentElement.parentElement.style.display='none'" class="sm-btn sm-btn-outline" style="height: 36px; padding: 0 16px;">إلغاء</button>
            </div>
        </form>
    </div>

    <!-- Edit Record Modal -->
    <div id="edit-record-modal" class="sm-modal-overlay">
        <div class="sm-modal-content" style="max-width: 700px;">
            <div class="sm-modal-header">
                <h3>تعديل بيانات المخالفة</h3>
                <button class="sm-modal-close" onclick="document.getElementById('edit-record-modal').style.display='none'">&times;</button>
            </div>
            <form method="post" id="edit-record-form">
                <?php wp_nonce_field('sm_record_action', 'sm_nonce'); ?>
                <input type="hidden" name="record_id" id="edit_record_id">

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 15px;">
                    <div class="sm-form-group" style="margin-bottom:0;">
                        <label class="sm-label">درجة المخالفة:</label>
                        <select name="degree" id="edit_violation_degree" class="sm-select" onchange="updateEditHierarchicalViolations()" required>
                            <option value="1">المستوى الأول (بسيطة)</option>
                            <option value="2">المستوى الثاني (متوسطة)</option>
                            <option value="3">المستوى الثالث (جسيمة)</option>
                            <option value="4">المستوى الرابع (شديدة الخطورة)</option>
                        </select>
                    </div>

                    <div class="sm-form-group" style="margin-bottom:0;">
                        <label class="sm-label">نوع المخالفة / البند:</label>
                        <select name="violation_code" id="edit_violation_code_select" class="sm-select" onchange="onEditViolationSelected()" required>
                            <option value="">-- اختر البند --</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="sm-form-group">
                        <label class="sm-label">تصنيف الموقف:</label>
                        <select name="classification" id="edit_classification" class="sm-select">
                            <option value="general">عام</option>
                            <option value="inside_class">داخل الفصل</option>
                            <option value="yard">في الساحة</option>
                            <option value="labs">في المختبرات</option>
                            <option value="bus">الحافلة المدرسية</option>
                        </select>
                    </div>

                    <div class="sm-form-group">
                        <label class="sm-label">النقاط المستحقة:</label>
                        <input type="number" name="points" id="edit_violation_points" class="sm-input" value="0">
                    </div>
                    <input type="hidden" name="type" id="edit_hidden_violation_type">
                    <input type="hidden" name="severity" id="edit_violation_severity">
                </div>

                <div class="sm-form-group">
                    <label class="sm-label">الإجراء المتخذ:</label>
                    <input type="text" name="action_taken" id="edit_action_taken" class="sm-input">
                </div>

                <div class="sm-form-group">
                    <label class="sm-label">التفاصيل:</label>
                    <textarea name="details" id="edit_details" class="sm-textarea" rows="3"></textarea>
                </div>

                <div style="display:flex; gap:10px; margin-top: 15px; justify-content: flex-end;">
                    <button type="submit" name="sm_update_record" class="sm-btn" style="height: 38px; padding: 0 20px;">حفظ التغييرات</button>
                    <button type="button" onclick="document.getElementById('edit-record-modal').style.display='none'" class="sm-btn sm-btn-outline" style="height: 38px; padding: 0 16px;">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Container -->
    <div style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 16px;">
        <!-- Table Header Bar with Sorting Trigger -->
        <div style="padding: 12px 18px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 13px; font-weight: 800; color: #1e293b;">سجلات المخالفات المسجلة</div>
            <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #64748b;">
                <span>الترتيب:</span>
                <select id="violation-sort-order" onchange="document.getElementById('violation-filter-form').dispatchEvent(new Event('submit'))" class="sm-select" style="height: 32px; font-size: 12px; padding: 0 8px; width: auto;">
                    <option value="DESC">الأحدث أولاً</option>
                    <option value="ASC">الأقدم أولاً</option>
                </select>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="sm-table" style="margin: 0; width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 25%;">الطالب</th>
                        <th style="width: 15%;">المدرسة / الصف / الشعبة</th>
                        <th style="width: 15%;">التاريخ واليوم</th>
                        <th style="width: 20%;">بند المخالفة والدرجة</th>
                        <th style="width: 8%; text-align: center;">تكرار</th>
                        <th style="width: 10%;">الحالة / الشدة</th>
                        <th style="width: 17%; text-align: left;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="violations-table-body">
                    <?php include SM_PLUGIN_DIR . 'templates/partials/violations-table-rows.php'; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Summary & Pagination Control Bar -->
        <div style="padding: 12px 18px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; font-size: 13px; color: #475569;">
            <div>
                <strong>إجمالي المخالفات:</strong> <span id="total-violations-count" style="font-weight: 800; color: #8b1e1e;"><?php echo count($records ?? array()); ?></span> سجل
            </div>

            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span>عناصر الصفحة:</span>
                    <select id="violations-per-page" class="sm-select" style="height: 32px; font-size: 12px; width: auto; padding: 0 8px;">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div style="display: flex; align-items: center; gap: 6px;">
                    <button type="button" class="sm-btn sm-btn-outline" style="height: 30px; padding: 0 10px; font-size: 11px !important;">السابق</button>
                    <span style="font-weight: 700; font-size: 12px;">صفحة 1 من 1</span>
                    <button type="button" class="sm-btn sm-btn-outline" style="height: 30px; padding: 0 10px; font-size: 11px !important;">التالي</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div id="view-record-modal" class="sm-modal-overlay" style="display: none;">
        <div class="sm-modal-content">
            <div class="sm-modal-header">
                <h3>تفاصيل المخالفة السلوكية</h3>
                <button type="button" class="sm-modal-close" onclick="document.getElementById('view-record-modal').style.display='none'">&times;</button>
            </div>
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="background: #f8fafc; border-radius: 10px; padding: 14px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;">
                    <div id="view_stu_photo" style="width: 44px; height: 44px; border-radius: 50%; overflow: hidden; background: #cbd5e1; display: flex; align-items: center; justify-content: center;"></div>
                    <div>
                        <div id="view_stu_name" style="font-size: 15px; font-weight: 800; color: #0f172a;"></div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                            المدرسة: <span id="view_school_name" style="font-weight: 700;"></span> | الصف والشعبة: <span id="view_class_sec" style="font-weight: 700;"></span>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 10px 14px; border-radius: 8px;">
                        <span style="font-size: 11px; color: #64748b; font-weight: 700; display: block;">كود المخالفة</span>
                        <span id="view_violation_code" style="font-size: 13px; font-weight: 800; color: #0f172a;"></span>
                    </div>
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 10px 14px; border-radius: 8px;">
                        <span style="font-size: 11px; color: #64748b; font-weight: 700; display: block;">الدرجة</span>
                        <span id="view_degree" style="font-size: 13px; font-weight: 800; color: #8b1e1e;"></span>
                    </div>
                </div>

                <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 12px 14px; border-radius: 8px;">
                    <span style="font-size: 11px; color: #64748b; font-weight: 700; display: block; margin-bottom: 4px;">التفاصيل</span>
                    <p id="view_details_text" style="margin: 0; font-size: 13px; color: #1e293b; line-height: 1.5;"></p>
                </div>

                <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 12px 14px; border-radius: 8px;">
                    <span style="font-size: 11px; color: #64748b; font-weight: 700; display: block; margin-bottom: 4px;">الإجراء المتخذ</span>
                    <p id="view_action_text" style="margin: 0; font-size: 13px; color: #16a34a; font-weight: 700;"></p>
                </div>
            </div>
            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('view-record-modal').style.display='none'" class="sm-btn sm-btn-outline" style="height: 38px;">إغلاق</button>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="delete-record-modal" class="sm-modal-overlay">
        <div class="sm-modal-content" style="max-width: 400px; text-align: center;">
            <h3 style="margin:0 0 10px 0; font-size: 16px; font-weight: 800; color: #0f172a;">تأكيد حذف المخالفة</h3>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">هل أنت متأكد من حذف هذا السجل نهائياً؟</p>
            <input type="hidden" id="confirm_delete_record_id">
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button onclick="executeDeleteRecord()" class="sm-btn sm-btn-destructive" style="height: 38px; padding: 0 20px;">حذف</button>
                <button onclick="document.getElementById('delete-record-modal').style.display='none'" class="sm-btn sm-btn-outline" style="height: 38px; padding: 0 20px;">تراجع</button>
            </div>
        </div>
    </div>

    <script>
    const hViolations = <?php echo json_encode(SM_Settings::get_hierarchical_violations()); ?>;

    function updateEditHierarchicalViolations(selectedCode) {
        selectedCode = selectedCode || '';
        const degree = document.getElementById('edit_violation_degree').value;
        const select = document.getElementById('edit_violation_code_select');

        select.innerHTML = '<option value="">-- اختر البند --</option>';
        if (!degree || !hViolations[degree]) return;

        Object.keys(hViolations[degree]).forEach(code => {
            const v = hViolations[degree][code];
            const opt = document.createElement('option');
            opt.value = code;
            opt.innerText = code + ' - ' + v.name;
            if (code === selectedCode) opt.selected = true;
            select.appendChild(opt);
        });
    }

    function onEditViolationSelected() {
        const degree = document.getElementById('edit_violation_degree').value;
        const code = document.getElementById('edit_violation_code_select').value;
        if (!degree || !code || !hViolations[degree][code]) return;

        const v = hViolations[degree][code];
        document.getElementById('edit_violation_points').value = v.points;
        document.getElementById('edit_action_taken').value = v.action;
        document.getElementById('edit_hidden_violation_type').value = v.name;

        const sev = document.getElementById('edit_violation_severity');
        if (degree == 1) sev.value = 'low';
        else if (degree == 2) sev.value = 'medium';
        else sev.value = 'high';
    }

    function editSmRecord(record) {
        document.getElementById('edit_record_id').value = record.id;
        document.getElementById('edit_violation_degree').value = record.degree || 1;
        document.getElementById('edit_classification').value = record.classification || 'general';
        document.getElementById('edit_violation_points').value = record.points || 0;
        document.getElementById('edit_action_taken').value = record.action_taken || '';
        document.getElementById('edit_details').value = record.details || '';
        document.getElementById('edit_hidden_violation_type').value = record.type || '';
        document.getElementById('edit_violation_severity').value = record.severity || 'low';

        updateEditHierarchicalViolations(record.violation_code);
        document.getElementById('edit-record-modal').style.display = 'flex';
    }

    function exportViolationPDF() {
        const student = document.querySelector('input[name="student_search"]')?.value || '';
        const grade = document.querySelector('select[name="class_filter"]')?.value || '';
        const section = document.querySelector('select[name="section_filter"]')?.value || '';
        const type = document.querySelector('select[name="type_filter"]')?.value || '';

        let url = '<?php echo admin_url('admin-ajax.php?action=sm_print&print_type=violation_report'); ?>';
        if (student) url += '&search=' + encodeURIComponent(student);
        if (grade) url += '&class_filter=' + encodeURIComponent(grade);
        if (section) url += '&section_filter=' + encodeURIComponent(section);
        if (type) url += '&type_filter=' + encodeURIComponent(type);

        window.open(url, '_blank');
    }

    (function() {
        const filterForm = document.getElementById('violation-filter-form');
        if (filterForm) {
            filterForm.onsubmit = function(e) {
                e.preventDefault();
                const tbody = document.getElementById('violations-table-body');
                if (tbody) tbody.style.opacity = '0.5';

                const formData = new FormData(this);
                formData.append('action', 'sm_filter_violations');
                formData.append('nonce', '<?php echo wp_create_nonce("sm_record_action"); ?>');

                const sortOrder = document.getElementById('violation-sort-order')?.value;
                if (sortOrder) formData.append('sort_order', sortOrder);

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success && tbody) {
                        tbody.innerHTML = res.data.html;
                    }
                })
                .finally(() => {
                    if (tbody) tbody.style.opacity = '1';
                });
            };
        }

        window.markAsContacted = function(recordId) {
            const formData = new FormData();
            formData.append('action', 'sm_mark_contacted');
            formData.append('record_id', recordId);
            formData.append('nonce', '<?php echo wp_create_nonce("sm_record_action"); ?>');

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.success && filterForm) {
                    filterForm.dispatchEvent(new Event('submit'));
                }
            });
        };

        window.viewViolationDetails = function(record) {
            document.getElementById('view_stu_name').innerText = record.student_name || '---';
            document.getElementById('view_school_name').innerText = record.school_name || 'المدرسة الرئيسية';
            document.getElementById('view_class_sec').innerText = (record.class_name || '') + ' ' + (record.section || '');
            document.getElementById('view_violation_code').innerText = record.violation_code || record.type || '---';
            document.getElementById('view_degree').innerText = 'المستوى ' + (record.degree || 1);
            document.getElementById('view_details_text').innerText = record.details || 'لا توجد تفاصيل مسجلة.';
            document.getElementById('view_action_text').innerText = record.action_taken || 'لم يتم تسجيل إجراء إداري بعد.';

            const photoBox = document.getElementById('view_stu_photo');
            if (record.photo_url) {
                photoBox.innerHTML = '<img src="' + record.photo_url + '" style="width:100%; height:100%; object-fit:cover;" />';
            } else {
                photoBox.innerHTML = '<span class="dashicons dashicons-admin-users"></span>';
            }

            document.getElementById('view-record-modal').style.display = 'flex';
        };

        window.confirmDeleteRecord = function(id) {
            document.getElementById('confirm_delete_record_id').value = id;
            document.getElementById('delete-record-modal').style.display = 'flex';
        };

        window.executeDeleteRecord = function() {
            const id = document.getElementById('confirm_delete_record_id').value;
            const formData = new FormData();
            formData.append('action', 'sm_delete_record_ajax');
            formData.append('record_id', id);
            formData.append('nonce', '<?php echo wp_create_nonce("sm_record_action"); ?>');

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    if (typeof smShowNotification === 'function') smShowNotification('تم حذف السجل بنجاح');
                    const row = document.getElementById('record-row-' + id);
                    if (row) row.remove();
                    document.getElementById('delete-record-modal').style.display = 'none';
                }
            });
        };
    })();
    </script>
</div>
