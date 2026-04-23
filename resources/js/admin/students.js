document.addEventListener('DOMContentLoaded', function () {
    const hasSwal = typeof window.Swal !== 'undefined';
    const pageDataNode = document.getElementById('admin-students-data');
    const pageData = {
        subjectsByClass: {},
        studyTimesByClass: {},
        subjectStudySlotsByClassSubject: {},
        classStudyTimeIdsByClassSubject: {},
        classStudyTimeIdsBySubjectAll: {},
        classLabelById: {},
        periodLabels: {},
        validationErrors: [],
        flash: {
            error: '',
            warning: '',
            success: '',
        },
    };

    if (pageDataNode) {
        try {
            const parsed = JSON.parse(pageDataNode.textContent || '{}');

            if (parsed && typeof parsed === 'object') {
                Object.assign(pageData, parsed);
                pageData.flash = {
                    ...pageData.flash,
                    ...(parsed.flash && typeof parsed.flash === 'object' ?
                        parsed.flash :
                        {}),
                };
            }
        } catch (error) {
            console.error('Unable to parse student page data.', error);
        }
    }

    const subjectsByClass = pageData.subjectsByClass || {};
    const studyTimesByClass = pageData.studyTimesByClass || {};
    const subjectStudySlotsByClassSubject =
        pageData.subjectStudySlotsByClassSubject || {};
    const classStudyTimeIdsByClassSubject =
        pageData.classStudyTimeIdsByClassSubject || {};
    const classStudyTimeIdsBySubjectAll =
        pageData.classStudyTimeIdsBySubjectAll || {};
    const classLabelById = pageData.classLabelById || {};
    const periodLabels = pageData.periodLabels || {};

    const dayLabels = {
        all: 'All Days',
        monday: 'Monday',
        tuesday: 'Tuesday',
        wednesday: 'Wednesday',
        thursday: 'Thursday',
        friday: 'Friday',
        saturday: 'Saturday',
        sunday: 'Sunday',
    };

    const to12Hour = (value) => {
        if (!value) {
            return '';
        }

        const [hourRaw, minuteRaw] = String(value).split(':');
        const hour = Number(hourRaw || 0);
        const minute = String(minuteRaw || '00').padStart(2, '0');
        const suffix = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 === 0 ? 12 : hour % 12;

        return `${hour12.toString().padStart(2, '0')}:${minute} ${suffix}`;
    };

    const resetSelect = (select, placeholder) => {
        if (!select) {
            return;
        }

        select.innerHTML = '';
        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        select.appendChild(option);
    };

    const parseSelectedIds = (rawValue) => {
        if (Array.isArray(rawValue)) {
            return rawValue
                .map((item) => String(item))
                .filter((item) => item !== '');
        }

        const text = String(rawValue || '').trim();
        if (text === '') {
            return [];
        }

        try {
            const parsed = JSON.parse(text);
            if (Array.isArray(parsed)) {
                return parsed
                    .map((item) => String(item))
                    .filter((item) => item !== '');
            }
        } catch (error) {
            // Fallback below handles legacy comma-separated values.
        }

        return text
            .split(',')
            .map((item) => item.trim())
            .filter((item) => item !== '');
    };

    const selectedIdsFromSelect = (select) => {
        if (!select) {
            return [];
        }

        return Array.from(select.selectedOptions || [])
            .map((option) => String(option.value || ''))
            .filter((value) => value !== '');
    };

    const renderSelectAsCheckboxes = (select) => {
        if (!select) {
            return;
        }

        const targetId = String(select.dataset.checkboxTarget || '').trim();
        if (targetId === '') {
            return;
        }

        const container = document.getElementById(targetId);
        if (!container) {
            return;
        }

        container.innerHTML = '';
        const options = Array.from(select.options || []).filter(
            (option) => String(option.value || '') !== '',
        );

        if (options.length === 0) {
            const placeholder = document.createElement('p');
            placeholder.className = 'text-xs font-medium text-slate-500';
            placeholder.textContent =
                String(select.options?.[0]?.textContent || 'No options available');
            container.appendChild(placeholder);
            return;
        }

        options.forEach((option, index) => {
            const label = document.createElement('label');
            label.className =
                'flex items-start gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm text-slate-700 transition hover:bg-slate-50';

            const input = document.createElement('input');
            input.type = 'checkbox';
            input.value = String(option.value || '');
            input.checked = option.selected;
            input.disabled = !!select.disabled;
            input.className =
                'mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500';
            input.addEventListener('change', () => {
                option.selected = input.checked;
                select.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            });

            const text = document.createElement('span');
            text.className = 'leading-5';
            text.textContent = String(option.textContent || `Option ${index + 1}`);

            label.appendChild(input);
            label.appendChild(text);
            container.appendChild(label);
        });
    };

    const normalizeDay = (value) => {
        const day = String(value || '').toLowerCase().trim();
        return day !== '' ? day : 'all';
    };

    const normalizeTime = (value) => String(value || '').slice(0, 5);

    const slotsShareBaseTime = (classSlot, subjectSlot) => {
        return (
            String(classSlot.period || '').toLowerCase() ===
            String(subjectSlot.period || '').toLowerCase() &&
            normalizeTime(classSlot.start_time) === normalizeTime(subjectSlot.start_time) &&
            normalizeTime(classSlot.end_time) === normalizeTime(subjectSlot.end_time)
        );
    };

    const slotDaysAreCompatible = (classSlot, subjectSlot) => {
        const classDay = normalizeDay(classSlot.day_of_week);
        const subjectDay = normalizeDay(subjectSlot.day_of_week);

        return classDay === subjectDay || classDay === 'all' || subjectDay === 'all';
    };

    const classSlotMatchesSubjectSchedule = (classSlot, subjectSlots) => {
        return subjectSlots.some((subjectSlot) => {
            return (
                slotsShareBaseTime(classSlot, subjectSlot) &&
                slotDaysAreCompatible(classSlot, subjectSlot)
            );
        });
    };

    const allStudyTimes = (() => {
        const rows = [];

        Object.entries(studyTimesByClass || {}).forEach(([classId, group]) => {
            if (!Array.isArray(group)) {
                return;
            }

            group.forEach((slot) => {
                rows.push({
                    ...slot,
                    school_class_id: Number(slot?.school_class_id || classId || 0),
                });
            });
        });

        return rows;
    })();

    const allSubjects = (() => {
        const subjects = [];
        const seen = new Set();

        Object.entries(subjectsByClass || {}).forEach(([classId, group]) => {
            if (!Array.isArray(group)) {
                return;
            }

            group.forEach((subject) => {
                const subjectId = String(subject?.id || '');
                const subjectClassId = String(subject?.school_class_id || classId || '');
                const key = `${subjectClassId}:${subjectId}`;

                if (subjectId === '' || seen.has(key)) {
                    return;
                }

                seen.add(key);
                subjects.push({
                    ...subject,
                    school_class_id: Number(subjectClassId || 0),
                });
            });
        });

        return subjects;
    })();

    const classIdsForSubjects = (subjectIds) => {
        const selectedSet = new Set(parseSelectedIds(subjectIds));
        if (selectedSet.size === 0) {
            return [];
        }

        return Array.from(new Set(
            allSubjects
                .filter((subject) => selectedSet.has(String(subject.id)))
                .map((subject) => String(subject.school_class_id || ''))
                .filter((classId) => classId !== ''),
        ));
    };

    const syncClassFromMajorSelection = (classSelect, majorSelect) => {
        if (!classSelect || !majorSelect) {
            return '';
        }

        const selectedClassIds = classIdsForSubjects(selectedIdsFromSelect(majorSelect));
        if (selectedClassIds.length === 1) {
            classSelect.value = selectedClassIds[0];
            return selectedClassIds[0];
        }

        return classSelect.value || '';
    };

    const renderMajorSubjects = (select, classId, selectedIds = []) => {
        if (!select) {
            return;
        }

        const key = String(classId || '');
        const subjects = key !== ''
            ? (Array.isArray(subjectsByClass[key]) ? subjectsByClass[key] : [])
            : allSubjects;
        let placeholder = 'Select major subjects';

        if (subjects.length > 0 && key !== '') {
            placeholder = 'Select major subjects';
        } else if (subjects.length === 0 && key !== '') {
            placeholder = 'No subjects in selected class';
        } else if (subjects.length === 0) {
            placeholder = 'No major subjects available';
        }

        resetSelect(select, placeholder);
        const selectedSet = new Set(parseSelectedIds(selectedIds));

        subjects.forEach((item) => {
            const option = document.createElement('option');
            option.value = String(item.id);
            const classLabel = classLabelById[String(item.school_class_id || '')] || '';
            option.textContent = classLabel ? `${item.name} (${classLabel})` : `${item.name}`;
            if (selectedSet.has(String(item.id))) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        select.disabled = subjects.length === 0;
        select.dataset.selectedList = '';
        select.dataset.selected = '';
        renderSelectAsCheckboxes(select);
    };

    const renderStudyTimes = (
        select,
        classId,
        selectedIds = [],
        subjectIds = [],
        requireSubjectMatch = false,
        autoSelectAll = false,
    ) => {
        if (!select) {
            return;
        }

        const key = String(classId || '');
        let slots = allStudyTimes;
        const selectedSubjectIds = parseSelectedIds(subjectIds);

        if (requireSubjectMatch && selectedSubjectIds.length === 0) {
            slots = [];
        } else if (selectedSubjectIds.length > 0) {
            const allowedStudyTimeIds = new Set();

            selectedSubjectIds.forEach((subjectKey) => {
                const idsForSubject = key !== ''
                    ? classStudyTimeIdsByClassSubject?.[key]?.[String(subjectKey)]
                    : classStudyTimeIdsBySubjectAll[String(subjectKey)];

                if (Array.isArray(idsForSubject)) {
                    idsForSubject.forEach((id) => allowedStudyTimeIds.add(String(id)));
                }
            });

            if (allowedStudyTimeIds.size > 0) {
                slots = allStudyTimes.filter((item) => {
                    const matchesAllowedStudyTime = allowedStudyTimeIds.has(String(item.id));
                    const matchesClass = key === '' || String(item.school_class_id || '') === key;
                    return matchesAllowedStudyTime && matchesClass;
                });
            } else {
                const subjectSlots = selectedSubjectIds.flatMap((subjectKey) => {
                    if (key !== '') {
                        const slotsForSubject = subjectStudySlotsByClassSubject?.[key]?.[String(subjectKey)];
                        return Array.isArray(slotsForSubject) ? slotsForSubject : [];
                    }

                    return Object.values(subjectStudySlotsByClassSubject || {}).flatMap((subjectMap) => {
                        const slotsForSubject = subjectMap?.[String(subjectKey)];
                        return Array.isArray(slotsForSubject) ? slotsForSubject : [];
                    });
                });

                slots = allStudyTimes.filter((item) => {
                    if (key !== '' && String(item.school_class_id || '') !== key) {
                        return false;
                    }

                    return classSlotMatchesSubjectSchedule(item, subjectSlots);
                });
            }
        } else if (key !== '') {
            slots = allStudyTimes.filter((item) => String(item.school_class_id || '') === key);
        } else {
            slots = [];
        }

        let placeholder = 'Select major subjects first';
        if (requireSubjectMatch && selectedSubjectIds.length === 0 && key === '') {
            placeholder = 'Select major subjects first';
        } else if (slots.length > 0) {
            placeholder = 'Select study time';
        } else if (selectedSubjectIds.length > 0) {
            placeholder = 'No study time for selected subjects';
        } else if (key !== '') {
            placeholder = 'No study times in selected class';
        }

        resetSelect(select, placeholder);
        const selectedSet = new Set(parseSelectedIds(selectedIds));

        slots.forEach((item) => {
            const option = document.createElement('option');
            option.value = String(item.id);
            const periodKey = String(item.period || '').toLowerCase();
            const period = periodLabels[periodKey] || (periodKey ?
                periodKey.charAt(0).toUpperCase() + periodKey.slice(1) :
                'Custom');
            const dayKey = String(item.day_of_week || 'all').toLowerCase();
            const dayLabel = dayLabels[dayKey] || (dayKey ?
                dayKey.charAt(0).toUpperCase() + dayKey.slice(1) :
                'All Days');
            const slotClassLabel = classLabelById[String(item.school_class_id || '')] || 'Class';

            option.textContent =
                `${slotClassLabel} | ${dayLabel} | ${period}: ${to12Hour(item.start_time)} -> ${to12Hour(item.end_time)}`;
            if (selectedSet.has(String(item.id))) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        if (autoSelectAll && slots.length > 0 && selectedSet.size === 0) {
            Array.from(select.options).forEach((option) => {
                if (String(option.value || '') !== '') {
                    option.selected = true;
                }
            });
        }

        select.disabled = slots.length === 0;
        select.dataset.selectedList = '';
        select.dataset.selected = '';
        renderSelectAsCheckboxes(select);
    };

    const wireClassDependentFields = (classSelect, majorSelect, studyTimeSelect) => {
        if (!classSelect) {
            return;
        }

        const apply = (useCurrentSelection = true) => {
            const classId = classSelect.value || '';
            const majorSelected = useCurrentSelection ?
                (() => {
                    const fromCurrentSelection = selectedIdsFromSelect(majorSelect);
                    if (fromCurrentSelection.length > 0) {
                        return fromCurrentSelection;
                    }

                    return parseSelectedIds(
                        majorSelect?.dataset.selectedList || majorSelect?.dataset.selected || '',
                    );
                })() :
                [];
            const studySelected = useCurrentSelection ?
                (() => {
                    const fromCurrentSelection = selectedIdsFromSelect(studyTimeSelect);
                    if (fromCurrentSelection.length > 0) {
                        return fromCurrentSelection;
                    }

                    return parseSelectedIds(
                        studyTimeSelect?.dataset.selectedList || studyTimeSelect?.dataset.selected || '',
                    );
                })() :
                [];

            if (majorSelect) {
                renderMajorSubjects(majorSelect, classId, majorSelected);
            }

            if (studyTimeSelect) {
                const selectedSubjectIds = majorSelect ?
                    selectedIdsFromSelect(majorSelect) :
                    [];

                renderStudyTimes(
                    studyTimeSelect,
                    classId,
                    studySelected,
                    selectedSubjectIds,
                    !!majorSelect,
                    !useCurrentSelection,
                );
            }
        };

        apply(true);
        classSelect.addEventListener('change', () => apply(false));

        if (majorSelect && studyTimeSelect) {
            majorSelect.addEventListener('change', () => {
                const selectedMajorIds = selectedIdsFromSelect(majorSelect);
                const classId = syncClassFromMajorSelection(classSelect, majorSelect);

                renderMajorSubjects(majorSelect, classId, selectedMajorIds);
                renderStudyTimes(
                    studyTimeSelect,
                    classId,
                    [],
                    selectedMajorIds,
                    true,
                    true,
                );
            });
        }
    };

    const manageStudyTimeInline = document.getElementById('manage_study_time_inline');

    const buildTimeStudiesUrl = (classId = '', subjectId = '') => {
        const baseUrl = manageStudyTimeInline?.dataset.baseUrl;
        if (!baseUrl) {
            return '#';
        }

        const url = new URL(baseUrl, window.location.origin);
        const selectedClassId = String(classId || '').trim();
        const selectedSubjectId = String(subjectId || '').trim();

        url.searchParams.set('tab', selectedSubjectId ? 'subject' : 'class');
        if (selectedClassId) {
            url.searchParams.set('class_id', selectedClassId);
        }
        if (selectedSubjectId) {
            url.searchParams.set('subject_id', selectedSubjectId);
        }

        return url.toString();
    };

    const syncManageStudyTimeLink = () => {
        const createClassSelect = document.getElementById('school_class_id');
        const createMajorSelect = document.getElementById('major_subject_id');
        const selectedMajorIds = selectedIdsFromSelect(createMajorSelect);
        const href = buildTimeStudiesUrl(
            createClassSelect?.value || '',
            selectedMajorIds[0] || '',
        );

        if (manageStudyTimeInline) {
            manageStudyTimeInline.href = href;
        }
    };

    wireClassDependentFields(
        document.getElementById('school_class_id'),
        document.getElementById('major_subject_id'),
        document.getElementById('class_study_time_id'),
    );

    syncManageStudyTimeLink();
    document
        .getElementById('school_class_id')
        ?.addEventListener('change', syncManageStudyTimeLink);
    document
        .getElementById('major_subject_id')
        ?.addEventListener('change', syncManageStudyTimeLink);

    document.querySelectorAll('[id^="edit_school_class_id_"]').forEach((classSelect) => {
        const suffix = classSelect.id.replace('edit_school_class_id_', '');
        wireClassDependentFields(
            classSelect,
            document.getElementById(`edit_major_subject_id_${suffix}`),
            document.getElementById(`edit_class_study_time_id_${suffix}`),
        );
    });

    const selectedValuesByName = (form, inputName) => {
        const select = form?.querySelector(`[name="${inputName}"]`);
        return selectedIdsFromSelect(select);
    };

    const validateStudentSelections = (form) => {
        if (!form) {
            return null;
        }

        const role = String(form.querySelector('[name="role"]')?.value || 'student').toLowerCase();
        if (role !== 'student') {
            return null;
        }

        const classId = String(form.querySelector('[name="school_class_id"]')?.value || '').trim();
        if (classId === '') {
            return null;
        }

        const selectedMajorIds = selectedValuesByName(form, 'major_subject_ids[]');
        if (selectedMajorIds.length === 0) {
            return 'Select at least one major subject for the selected class.';
        }

        const selectedStudyTimeIds = selectedValuesByName(form, 'class_study_time_ids[]');
        if (selectedStudyTimeIds.length === 0) {
            return 'Select at least one study time for the selected class.';
        }

        return null;
    };

    const fireMessage = (kind, title, text, options = {}) => {
        if (!text) {
            return;
        }

        if (hasSwal) {
            window.Swal.fire({
                icon: kind,
                title,
                text,
                ...options,
            });
            return;
        }

        window.alert(`${title}\n\n${text}`);
    };

    const confirmSubmit = (selector, buildConfig) => {
        document.querySelectorAll(selector).forEach((form) => {
            form.addEventListener('submit', function (event) {
                if (form.dataset.confirmed === '1') {
                    return;
                }

                event.preventDefault();

                const validationMessage = validateStudentSelections(form);
                if (validationMessage) {
                    fireMessage('warning', 'Selection Required', validationMessage);
                    return;
                }

                const config = buildConfig(form);
                const submitForm = () => {
                    form.dataset.confirmed = '1';
                    form.submit();
                };

                if (hasSwal) {
                    window.Swal.fire({
                        title: config.title,
                        text: config.text,
                        icon: config.icon,
                        showCancelButton: true,
                        confirmButtonText: config.confirmButtonText,
                        cancelButtonText: config.cancelButtonText,
                        confirmButtonColor: config.confirmButtonColor,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitForm();
                        }
                    });
                    return;
                }

                if (window.confirm(`${config.title}\n\n${config.text}`)) {
                    submitForm();
                }
            });
        });
    };

    confirmSubmit('.js-create-form', () => ({
        title: 'Create student account?',
        text: 'A new student user will be saved.',
        icon: 'question',
        confirmButtonText: 'Yes, create',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-edit-form', (form) => ({
        title: 'Save changes?',
        text: `Update profile for ${form.dataset.student || 'this student'}.`,
        icon: 'question',
        confirmButtonText: 'Yes, save',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-status-form', (form) => ({
        title: 'Change student status?',
        text: `This will ${form.dataset.action || 'change status'} for ${form.dataset.student || 'the student'}.`,
        icon: 'warning',
        confirmButtonText: 'Yes, continue',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f59e0b',
    }));

    confirmSubmit('.js-delete-form', (form) => ({
        title: 'Delete student?',
        text: `Delete ${form.dataset.student || 'this student'} permanently. This cannot be undone.`,
        icon: 'warning',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
    }));

    if (pageData.validationErrors.length > 0) {
        fireMessage('error', 'Validation Error', pageData.validationErrors[0]);
        return;
    }

    if (pageData.flash.error) {
        fireMessage('error', 'Error', pageData.flash.error);
    } else if (pageData.flash.warning) {
        fireMessage('warning', 'Warning', pageData.flash.warning);
    } else if (pageData.flash.success) {
        fireMessage('success', 'Success', pageData.flash.success, {
            timer: 2200,
            showConfirmButton: false,
        });
    }
});
