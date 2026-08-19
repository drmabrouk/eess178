<?php if (!defined('ABSPATH')) exit; ?>
<?php if (empty($records)): ?>
    <tr>
        <td colspan="7" style="padding: 40px 20px; text-align: center; color: #64748b;">
            <p style="margin: 0; font-size: 14px; font-weight: 700; color: #334155;">لا توجد سجلات مخالفات مطابقة حالياً.</p>
        </td>
    </tr>
<?php else: ?>
    <?php
    $type_labels = SM_Settings::get_violation_types();
    $severity_labels = SM_Settings::get_severities();
    $current_user = wp_get_current_user();
    $sender_name = $current_user->display_name;

    $weekdays = array(
        'Sunday'    => 'الأحد',
        'Monday'    => 'الاثنين',
        'Tuesday'   => 'الثلاثاء',
        'Wednesday' => 'الأربعاء',
        'Thursday'  => 'الخميس',
        'Friday'    => 'الجمعة',
        'Saturday'  => 'السبت',
    );

    foreach ($records as $row):
        $reg = SM_Settings::get_regulation_by_code($row->violation_code);
        $display_type = $reg ? $reg['name'] : $row->type;

        $created_timestamp = strtotime($row->created_at);
        $formatted_date = date('Y-m-d', $created_timestamp);
        $english_day = date('l', $created_timestamp);
        $day_name = $weekdays[$english_day] ?? '';

        // WhatsApp Message formatting
        $msg_text = "*السلام عليكم ورحمة الله وبركاته،*\n\n";
        $msg_text .= "إلى ولي أمر الطالب/ة: *{$row->student_name}*\n";
        $msg_text .= "الصف والشعبة: " . SM_Settings::format_grade_name($row->class_name, $row->section, 'short') . "\n";
        $msg_text .= "نوع المخالفة: *{$display_type}*\n\n";
        $msg_text .= "نرجو منكم المتابعة مع إدارة المدرسة.\n\n";
        $msg_text .= "*وتقبلوا فائق الاحترام والتقدير،*\n{$sender_name}";

        $waMsg = rawurlencode($msg_text);
        $raw_phone = $row->guardian_phone ?? '';
        $formatted_phone = SM_Settings::format_uae_phone($raw_phone);

        $school_display = !empty($row->school_name) ? $row->school_name : 'المدرسة الرئيسية';
        $class_sec_display = SM_Settings::format_grade_name($row->class_name, $row->section, 'short');
        if (empty($class_sec_display)) {
            $class_sec_display = trim(($row->class_name ?? '') . ' ' . ($row->section ?? ''));
        }

        $student_id_code = !empty($row->student_code) ? $row->student_code : '---';
        $nationality_str = !empty($row->nationality) ? $row->nationality : 'غير محدد';

        // Badge styling
        $badge_class = 'eess-badge-muted';
        if ($row->severity === 'low') {
            $badge_class = 'eess-badge-muted';
        } elseif ($row->severity === 'medium') {
            $badge_class = 'eess-badge-dark';
        } else {
            $badge_class = 'eess-badge-accent';
        }
    ?>
        <tr id="record-row-<?php echo $row->id; ?>">
            <!-- Student Column -->
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 36px; height: 36px; min-width: 36px; border-radius: 50%; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <?php if (!empty($row->photo_url)): ?>
                            <img src="<?php echo esc_url($row->photo_url); ?>" alt="<?php echo esc_attr($row->student_name); ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                        <?php else: ?>
                            <span class="dashicons dashicons-admin-users" style="color: #64748b; font-size: 18px;"></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 13px; color: #0f172a; border: none; background: none; padding: 0;">
                            <?php echo esc_html($row->student_name); ?>
                        </div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 1px;">
                            كود: <span style="font-weight: 700; color: #1e293b;"><?php echo esc_html($student_id_code); ?></span> | جنسية: <?php echo esc_html($nationality_str); ?>
                        </div>
                    </div>
                </div>
            </td>

            <!-- School / Grade / Section -->
            <td>
                <div style="font-weight: 700; font-size: 13px; color: #0f172a;"><?php echo esc_html($school_display); ?></div>
                <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-top: 1px;"><?php echo esc_html($class_sec_display); ?></div>
            </td>

            <!-- Date & Day -->
            <td>
                <div style="font-weight: 700; font-size: 13px; color: #0f172a;"><?php echo esc_html($formatted_date); ?></div>
                <div style="font-size: 11px; color: #8b1e1e; font-weight: 600; margin-top: 1px;"><?php echo esc_html($day_name); ?></div>
            </td>

            <!-- Violation Item & Degree -->
            <td>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span class="sm-badge eess-badge-accent" style="font-size: 10px; padding: 2px 8px;">درجة <?php echo (int)$row->degree; ?></span>
                    <span style="font-weight: 800; font-size: 12px; color: #0f172a;"><?php echo esc_html($row->violation_code); ?></span>
                </div>
                <div style="font-size: 12px; color: #475569; margin-top: 2px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;" title="<?php echo esc_attr($display_type); ?>">
                    <?php echo esc_html($display_type); ?>
                </div>
            </td>

            <!-- Repetition Frequency -->
            <td style="text-align: center;">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; background: #f1f5f9; color: #0f172a; border-radius: 50%; font-weight: 800; font-size: 11px; border: 1px solid #cbd5e1;">
                    <?php echo (int)$row->recurrence_count; ?>
                </span>
            </td>

            <!-- Status / Severity Badge -->
            <td>
                <span class="sm-badge <?php echo $badge_class; ?>">
                    <?php echo esc_html($severity_labels[$row->severity] ?? $row->severity); ?>
                </span>
            </td>

            <!-- Actions Fixed 5-Icon Column Set -->
            <td style="text-align: left;">
                <div style="display: inline-flex; align-items: center; gap: 4px; justify-content: flex-end; width: 160px;">
                    <!-- 1. Trash / Delete Icon -->
                    <?php if (current_user_can('إدارة_المخالفات') || current_user_can('manage_options')): ?>
                        <button type="button" onclick="confirmDeleteRecord(<?php echo $row->id; ?>)" class="sm-btn-outline" style="width: 28px !important; height: 28px !important; padding: 0 !important; border-radius: 9999px !important; border-color: #fca5a5 !important; color: #dc2626 !important;" title="حذف">
                            <span class="dashicons dashicons-trash" style="font-size: 14px; margin: 0;"></span>
                        </button>
                    <?php endif; ?>

                    <!-- 2. Print Icon -->
                    <a href="<?php echo admin_url('admin-ajax.php?action=sm_print&print_type=single_violation&record_id=' . $row->id); ?>" target="_blank" class="sm-btn-outline" style="width: 28px !important; height: 28px !important; padding: 0 !important; border-radius: 9999px !important; border-color: #cbd5e1 !important; color: #1e293b !important; display: inline-flex; align-items: center; justify-content: center;" title="طباعة">
                        <span class="dashicons dashicons-printer" style="font-size: 14px; margin: 0;"></span>
                    </a>

                    <!-- 3. Edit Icon -->
                    <?php if (current_user_can('إدارة_المخالفات') || current_user_can('manage_options')): ?>
                        <button type="button" onclick="editSmRecord(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="sm-btn-outline" style="width: 28px !important; height: 28px !important; padding: 0 !important; border-radius: 9999px !important; border-color: #cbd5e1 !important; color: #1e293b !important;" title="تعديل">
                            <span class="dashicons dashicons-edit" style="font-size: 14px; margin: 0;"></span>
                        </button>
                    <?php endif; ?>

                    <!-- 4. WhatsApp Icon -->
                    <?php if ($formatted_phone): ?>
                        <a href="https://wa.me/<?php echo $formatted_phone; ?>?text=<?php echo $waMsg; ?>" target="_blank" onclick="markAsContacted(<?php echo $row->id; ?>)" class="sm-btn-outline" style="width: 28px !important; height: 28px !important; padding: 0 !important; border-radius: 9999px !important; border-color: #86efac !important; color: #16a34a !important; display: inline-flex; align-items: center; justify-content: center;" title="واتساب">
                            <span class="dashicons dashicons-share" style="font-size: 14px; margin: 0;"></span>
                        </a>
                    <?php else: ?>
                        <button type="button" onclick="alert('رقم ولي الأمر غير متوفر')" class="sm-btn-outline" style="width: 28px !important; height: 28px !important; padding: 0 !important; border-radius: 9999px !important; border-color: #e2e8f0 !important; color: #cbd5e1 !important; cursor: not-allowed;" title="واتساب غير متاح">
                            <span class="dashicons dashicons-share" style="font-size: 14px; margin: 0;"></span>
                        </button>
                    <?php endif; ?>

                    <!-- 5. More / View Details Options Icon -->
                    <button type="button" onclick="viewViolationDetails(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="sm-btn-outline" style="width: 28px !important; height: 28px !important; padding: 0 !important; border-radius: 9999px !important; border-color: #cbd5e1 !important; color: #1e293b !important;" title="عرض التفاصيل والخيارات">
                        <span class="dashicons dashicons-ellipsis" style="font-size: 14px; margin: 0;"></span>
                    </button>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
