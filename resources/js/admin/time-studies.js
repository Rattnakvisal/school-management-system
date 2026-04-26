document.addEventListener('DOMContentLoaded', () => {
    const hasSwal = typeof window.Swal !== 'undefined';
    const pageDataNode = document.getElementById('admin-time-studies-data');
    const pageData = {
        subjectOptionsByClass: {},
        subjectOptionsAll: [],
        teacherOptionsAll: [],
        classTimeOptionsByClass: {},
        occupiedClassSlotsByClass: {},
        teacherBusySlots: [],
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
                    ...(parsed.flash && typeof parsed.flash === 'object' ? parsed.flash : {}),
                };
            }
        } catch (error) {
            console.error('Unable to parse time studies page data.', error);
        }
    }

    const subjectOptionsByClass = pageData.subjectOptionsByClass || {};
    const subjectOptionsAll = pageData.subjectOptionsAll || [];
    const teacherOptionsAll = pageData.teacherOptionsAll || [];
    const classTimeOptionsByClass = pageData.classTimeOptionsByClass || {};
    const occupiedClassSlotsByClass = pageData.occupiedClassSlotsByClass || {};
    const teacherBusySlots = pageData.teacherBusySlots || [];
    const getSubjectsForClass = (classId, mode = 'class') => {
        if (mode === 'all') return subjectOptionsAll;
        if (mode === 'assignable') return subjectOptionsAll;
        if (!classId || classId === 'all') return subjectOptionsAll;

        const classSubjects = subjectOptionsByClass[classId] || [];
        return classSubjects;
    };

    const renderSubjectOptions = (subjectSelect, classId, selectedValue, includeAllOption = false, mode =
        'class') => {
        if (!subjectSelect) return;

        const subjects = getSubjectsForClass(classId, mode);
        const options = [];

        if (includeAllOption) {
            const selectedAll = String(selectedValue) === 'all' ? ' selected' : '';
            options.push(`<option value="all"${selectedAll}>All Subjects</option>`);
        }

        if (subjects.length === 0) {
            const emptyMessage = mode === 'all' ?
                'No subjects available' :
                'No subjects in selected class';
            options.push(`<option value="">${emptyMessage}</option>`);
            subjectSelect.innerHTML = options.join('');
            return;
        }

        for (const subject of subjects) {
            const selected = String(selectedValue) === String(subject.id) ? ' selected' : '';
            options.push(`<option value="${subject.id}"${selected}>${subject.label}</option>`);
        }

        subjectSelect.innerHTML = options.join('');
    };

    const renderClassTimeOptions = (timeSelect, classId, selectedValue, currentSubjectStudyTimeId =
        null) => {
        if (!timeSelect) return;

        const slots = classTimeOptionsByClass[classId] || [];
        const occupiedSlots = occupiedClassSlotsByClass[classId] || {};
        const options = [];

        if (slots.length === 0) {
            options.push('<option value="">No class times in selected class</option>');
            timeSelect.innerHTML = options.join('');
            return;
        }

        options.push('<option value="">Select class time</option>');

        for (const slot of slots) {
            const ownerId = occupiedSlots[slot.key];
            const isCurrentSlot = currentSubjectStudyTimeId !== null &&
                currentSubjectStudyTimeId !== '' &&
                String(ownerId) === String(currentSubjectStudyTimeId);
            const isOccupiedByAnotherSubject = Boolean(ownerId) && !isCurrentSlot;

            const selected = String(selectedValue) === String(slot.id) ? ' selected' : '';
            const disabled = isOccupiedByAnotherSubject ? ' disabled' : '';
            const label = isOccupiedByAnotherSubject ?
                `${slot.label} (Already used in this class)` :
                slot.label;
            options.push(`<option value="${slot.id}"${selected}${disabled}>${label}</option>`);
        }

        timeSelect.innerHTML = options.join('');
    };

    const getClassSlotById = (classId, classTimeId) => {
        if (!classId || !classTimeId) return null;

        const slots = classTimeOptionsByClass[classId] || [];
        for (const slot of slots) {
            if (String(slot.id) === String(classTimeId)) {
                return slot;
            }
        }

        return null;
    };

    const renderTeacherOptions = (teacherSelect, classId, classTimeId, selectedValue,
        currentSubjectStudyTimeId = null) => {
        if (!teacherSelect) return;

        const selectedSlot = getClassSlotById(classId, classTimeId);
        const options = ['<option value="">Select teacher</option>'];

        const selectedDay = String(selectedSlot?.day_of_week || 'all').toLowerCase();
        const selectedStart = String(selectedSlot?.start_time || '');
        const selectedEnd = String(selectedSlot?.end_time || '');

        const dayOverlaps = (busyDay) => {
            const normalizedBusyDay = String(busyDay || 'all').toLowerCase();
            if (!selectedSlot) return false;
            if (selectedDay === 'all' || normalizedBusyDay === 'all') return true;
            return selectedDay === normalizedBusyDay;
        };

        const timeOverlaps = (busyStart, busyEnd) => {
            if (!selectedSlot) return false;
            return selectedStart < String(busyEnd) && selectedEnd > String(busyStart);
        };

        for (const teacher of teacherOptionsAll) {
            const isBusyInAnotherClass = teacherBusySlots.some((busySlot) => {
                if (String(busySlot.teacher_id) !== String(teacher.id)) return false;
                if (!dayOverlaps(busySlot.day_of_week)) return false;
                if (!timeOverlaps(busySlot.start_time, busySlot.end_time)) return false;

                return !(currentSubjectStudyTimeId !== null &&
                    currentSubjectStudyTimeId !== '' &&
                    String(busySlot.subject_study_time_id) === String(
                        currentSubjectStudyTimeId));
            });

            const selected = String(selectedValue) === String(teacher.id) ? ' selected' : '';
            const disabled = isBusyInAnotherClass ? ' disabled' : '';
            const label = isBusyInAnotherClass ?
                `${teacher.label} (Busy at this time)` :
                teacher.label;
            options.push(`<option value="${teacher.id}"${selected}${disabled}>${label}</option>`);
        }

        teacherSelect.innerHTML = options.join('');
    };

    const normalizeFlexibleTime = (rawValue) => {
        const value = String(rawValue || '').trim();
        if (!value) return null;

        const twentyFourMatch = value.match(/^([01]?\d|2[0-3]):([0-5]\d)$/);
        if (twentyFourMatch) {
            const hour = String(twentyFourMatch[1]).padStart(2, '0');
            const minute = String(twentyFourMatch[2]).padStart(2, '0');
            return `${hour}:${minute}`;
        }

        const twelveHourMatch = value.match(/^(0?[1-9]|1[0-2]):([0-5]\d)\s*(AM|PM)$/i);
        if (twelveHourMatch) {
            let hour = Number(twelveHourMatch[1]);
            const minute = String(twelveHourMatch[2]).padStart(2, '0');
            const meridiem = String(twelveHourMatch[3]).toUpperCase();

            if (meridiem === 'AM') {
                hour = hour === 12 ? 0 : hour;
            } else {
                hour = hour === 12 ? 12 : hour + 12;
            }

            return `${String(hour).padStart(2, '0')}:${minute}`;
        }

        const hourOnlyTwelveHourMatch = value.match(/^(0?[1-9]|1[0-2])\s*(AM|PM)$/i);
        if (hourOnlyTwelveHourMatch) {
            let hour = Number(hourOnlyTwelveHourMatch[1]);
            const meridiem = String(hourOnlyTwelveHourMatch[2]).toUpperCase();

            if (meridiem === 'AM') {
                hour = hour === 12 ? 0 : hour;
            } else {
                hour = hour === 12 ? 12 : hour + 12;
            }

            return `${String(hour).padStart(2, '0')}:00`;
        }

        return null;
    };

    const periodDefaultTimes = {
        morning: {
            start: '07:00',
            end: '10:00'
        },
        afternoon: {
            start: '10:00',
            end: '13:00'
        },
        evening: {
            start: '13:00',
            end: '16:00'
        },
        night: {
            start: '17:00',
            end: '20:00'
        },
        custom: null,
    };

    const applyPeriodPreset = (periodValue, startInput, endInput, force = false) => {
        if (!startInput || !endInput) return;

        const preset = periodDefaultTimes[String(periodValue || '').toLowerCase()] || null;
        if (!preset) return;

        if (!force) {
            const hasStart = String(startInput.value || '').trim() !== '';
            const hasEnd = String(endInput.value || '').trim() !== '';
            if (hasStart || hasEnd) return;
        }

        startInput.value = preset.start;
        endInput.value = preset.end;
    };

    const bindClassSlotPeriodBehavior = (row) => {
        if (!row) return;

        const periodSelect = row.querySelector('.js-class-period-select');
        const startInput = row.querySelector('.js-class-start-input');
        const endInput = row.querySelector('.js-class-end-input');
        if (!periodSelect || !startInput || !endInput) return;

        applyPeriodPreset(periodSelect.value, startInput, endInput, false);

        periodSelect.addEventListener('change', () => {
            applyPeriodPreset(periodSelect.value, startInput, endInput, true);
        });
    };

    const bindClassEditPeriodBehavior = (form) => {
        if (!form) return;

        const periodSelect = form.querySelector('.js-edit-class-period');
        const startInput = form.querySelector('.js-edit-class-start');
        const endInput = form.querySelector('.js-edit-class-end');
        if (!periodSelect || !startInput || !endInput) return;

        periodSelect.addEventListener('change', () => {
            const preset = periodDefaultTimes[String(periodSelect.value || '').toLowerCase()] ||
                null;
            if (!preset) return;

            startInput.value = preset.start;
            endInput.value = preset.end;
        });
    };

    // Add more rows for Class Study Time
    const classSlotRows = document.getElementById('class_slot_rows');
    const classSlotTemplate = document.getElementById('class_slot_row_template');
    const addClassSlotBtn = document.getElementById('add_class_slot_btn');

    if (classSlotRows && classSlotTemplate && addClassSlotBtn) {
        let nextIndex = Number(classSlotRows.dataset.nextIndex || classSlotRows.querySelectorAll(
            '.js-class-slot-row').length);

        const syncRemoveButtons = () => {
            const rows = classSlotRows.querySelectorAll('.js-class-slot-row');
            rows.forEach((row) => {
                const removeButton = row.querySelector('.js-remove-class-slot');
                if (!removeButton) return;
                removeButton.classList.toggle('hidden', rows.length <= 1);
            });
        };

        addClassSlotBtn.addEventListener('click', () => {
            const html = classSlotTemplate.innerHTML.split('__INDEX__').join(String(nextIndex));
            classSlotRows.insertAdjacentHTML('beforeend', html);
            const addedRow = classSlotRows.querySelector('.js-class-slot-row:last-child');
            bindClassSlotPeriodBehavior(addedRow);
            nextIndex += 1;
            classSlotRows.dataset.nextIndex = String(nextIndex);
            syncRemoveButtons();
        });

        classSlotRows.addEventListener('click', (event) => {
            const clicked = event.target;
            if (!(clicked instanceof HTMLElement)) return;

            const removeButton = clicked.closest('.js-remove-class-slot');
            if (!removeButton) return;

            const rows = classSlotRows.querySelectorAll('.js-class-slot-row');
            if (rows.length <= 1) return;

            const row = removeButton.closest('.js-class-slot-row');
            if (row) {
                row.remove();
                syncRemoveButtons();
            }
        });

        syncRemoveButtons();

        classSlotRows.querySelectorAll('.js-class-slot-row').forEach((row) => {
            bindClassSlotPeriodBehavior(row);
        });
    }

    document.querySelectorAll('.js-class-edit-form').forEach((form) => {
        bindClassEditPeriodBehavior(form);
    });

    const classTimeCreateForm = document.getElementById('class_time_create_form');
    if (classTimeCreateForm) {
        classTimeCreateForm.addEventListener('submit', (event) => {
            const timeInputs = [...classTimeCreateForm.querySelectorAll('.js-class-time-input')];
            let firstInvalidInput = null;

            for (const input of timeInputs) {
                const normalized = normalizeFlexibleTime(input.value);
                if (!normalized) {
                    if (!firstInvalidInput) firstInvalidInput = input;
                    continue;
                }
                input.value = normalized;
            }

            if (!firstInvalidInput) return;

            event.preventDefault();
            event.stopImmediatePropagation();
            firstInvalidInput.focus();

            const message = 'Invalid time format. Use 07:30 AM or 19:30.';
            if (hasSwal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time',
                    text: message
                });
            } else {
                alert(message);
            }
        });
    }

    // Add Subject Time form
    const subjectFormClass = document.getElementById('subject_form_class_id');
    const subjectSlotRows = document.getElementById('subject_slot_rows');
    const subjectSlotTemplate = document.getElementById('subject_slot_row_template');
    const addSubjectSlotBtn = document.getElementById('add_subject_slot_btn');

    const bindSubjectSlotRow = (row, classId, useDatasetSelection = true) => {
        if (!row) return;

        const subjectSelect = row.querySelector('.js-subject-form-subject');
        const classTimeSelect = row.querySelector('.js-subject-form-class-time');
        const teacherSelect = row.querySelector('.js-subject-form-teacher');
        if (!subjectSelect || !classTimeSelect || !teacherSelect) return;

        const selectedSubject = useDatasetSelection ?
            (subjectSelect.dataset.selected || subjectSelect.value || '') :
            (subjectSelect.value || '');
        const selectedClassTime = useDatasetSelection ?
            (classTimeSelect.dataset.selected || classTimeSelect.value || '') :
            (classTimeSelect.value || '');
        const selectedTeacher = useDatasetSelection ?
            (teacherSelect.dataset.selected || teacherSelect.value || '') :
            (teacherSelect.value || '');

        renderSubjectOptions(subjectSelect, classId, selectedSubject, false, 'assignable');
        renderClassTimeOptions(classTimeSelect, classId, selectedClassTime, null);
        renderTeacherOptions(
            teacherSelect,
            classId,
            classTimeSelect.value || selectedClassTime,
            selectedTeacher,
            null
        );

        subjectSelect.dataset.selected = '';
        classTimeSelect.dataset.selected = '';
        teacherSelect.dataset.selected = '';

        if (classTimeSelect.dataset.bound !== '1') {
            classTimeSelect.addEventListener('change', () => {
                renderTeacherOptions(
                    teacherSelect,
                    subjectFormClass?.value || '',
                    classTimeSelect.value || '',
                    teacherSelect.value || '',
                    null
                );
            });
            classTimeSelect.dataset.bound = '1';
        }
    };

    if (subjectFormClass && subjectSlotRows && subjectSlotTemplate && addSubjectSlotBtn) {
        let nextIndex = Number(subjectSlotRows.dataset.nextIndex || subjectSlotRows.querySelectorAll(
            '.js-subject-slot-row').length);

        const syncSubjectSlotRemoveButtons = () => {
            const rows = subjectSlotRows.querySelectorAll('.js-subject-slot-row');
            rows.forEach((row) => {
                const removeButton = row.querySelector('.js-remove-subject-slot');
                if (!removeButton) return;
                removeButton.classList.toggle('hidden', rows.length <= 1);
            });
        };

        const refreshSubjectSlotRows = (useDatasetSelection = false) => {
            const currentClassId = subjectFormClass.value || '';
            subjectSlotRows.querySelectorAll('.js-subject-slot-row').forEach((row) => {
                bindSubjectSlotRow(row, currentClassId, useDatasetSelection);
            });
        };

        refreshSubjectSlotRows(true);
        syncSubjectSlotRemoveButtons();

        subjectFormClass.addEventListener('change', () => {
            refreshSubjectSlotRows(false);
        });

        addSubjectSlotBtn.addEventListener('click', () => {
            const html = subjectSlotTemplate.innerHTML.split('__INDEX__').join(String(nextIndex));
            subjectSlotRows.insertAdjacentHTML('beforeend', html);
            const addedRow = subjectSlotRows.querySelector('.js-subject-slot-row:last-child');
            bindSubjectSlotRow(addedRow, subjectFormClass.value || '', false);
            nextIndex += 1;
            subjectSlotRows.dataset.nextIndex = String(nextIndex);
            syncSubjectSlotRemoveButtons();
        });

        subjectSlotRows.addEventListener('click', (event) => {
            const clicked = event.target;
            if (!(clicked instanceof HTMLElement)) return;

            const removeButton = clicked.closest('.js-remove-subject-slot');
            if (!removeButton) return;

            const rows = subjectSlotRows.querySelectorAll('.js-subject-slot-row');
            if (rows.length <= 1) return;

            const row = removeButton.closest('.js-subject-slot-row');
            if (row) {
                row.remove();
                syncSubjectSlotRemoveButtons();
            }
        });
    }

    // Edit Subject forms
    document.querySelectorAll('.js-subject-edit-form').forEach((form) => {
        const classSelect = form.querySelector('.js-subject-edit-class');
        const subjectSelect = form.querySelector('.js-subject-edit-subject');
        const classTimeSelect = form.querySelector('.js-subject-edit-class-time');
        const teacherSelect = form.querySelector('.js-subject-edit-teacher');
        const currentSubjectStudyTimeId = form.dataset.subjectStudyTimeId || '';

        if (!classSelect || !subjectSelect || !classTimeSelect || !teacherSelect) return;

        const selectedSubject = subjectSelect.dataset.selected || '';
        const selectedClassTime = classTimeSelect.dataset.selected || '';
        const selectedTeacher = teacherSelect.dataset.selected || '';

        renderSubjectOptions(subjectSelect, classSelect.value, selectedSubject, false,
            'assignable');
        renderClassTimeOptions(classTimeSelect, classSelect.value, selectedClassTime,
            currentSubjectStudyTimeId);
        renderTeacherOptions(teacherSelect, classSelect.value, selectedClassTime, selectedTeacher,
            currentSubjectStudyTimeId);

        classSelect.addEventListener('change', () => {
            renderSubjectOptions(subjectSelect, classSelect.value, '', false, 'assignable');
            renderClassTimeOptions(classTimeSelect, classSelect.value, '',
                currentSubjectStudyTimeId);
            renderTeacherOptions(teacherSelect, classSelect.value, '', '',
                currentSubjectStudyTimeId);
        });

        classTimeSelect.addEventListener('change', () => {
            renderTeacherOptions(teacherSelect, classSelect.value, classTimeSelect.value,
                teacherSelect.value, currentSubjectStudyTimeId);
        });
    });

    // Filter subjects by class
    const filterClass = document.getElementById('filter_class_id');
    const filterSubject = document.getElementById('filter_subject_id');

    if (filterClass && filterSubject) {
        const selectedFilterSubject = filterSubject.dataset.selected || 'all';
        renderSubjectOptions(filterSubject, filterClass.value, selectedFilterSubject, true);

        filterClass.addEventListener('change', () => {
            renderSubjectOptions(filterSubject, filterClass.value, 'all', true);
        });
    }

    // SweetAlert confirmations (same behavior as Student page)
    const showFormError = (title, message) => {
        if (hasSwal) {
            window.Swal.fire({
                icon: 'error',
                title,
                text: message,
            });
            return;
        }

        window.alert(`${title}\n\n${message}`);
    };

    const getSubjectCreateFormIssue = (form) => {
        if (!form || form.id !== 'subject_time_create_form') return '';

        const rows = Array.from(form.querySelectorAll('.js-subject-slot-row'));
        for (const [index, row] of rows.entries()) {
            const rowNumber = index + 1;
            const subjectSelect = row.querySelector('.js-subject-form-subject');
            const classTimeSelect = row.querySelector('.js-subject-form-class-time');
            const teacherSelect = row.querySelector('.js-subject-form-teacher');

            if (!subjectSelect?.value) {
                return `Please select a subject for row ${rowNumber}.`;
            }

            if (!classTimeSelect?.value) {
                return `Please select an available class time for row ${rowNumber}.`;
            }

            if (classTimeSelect.selectedOptions?.[0]?.disabled) {
                return `The class time in row ${rowNumber} is already used. Choose another class time.`;
            }

            if (teacherSelect?.value && teacherSelect.selectedOptions?.[0]?.disabled) {
                return `The selected teacher in row ${rowNumber} is busy at this time. Choose another teacher or class time.`;
            }
        }

        return '';
    };

    const confirmSubmit = (selector, buildConfig) => {
        document.querySelectorAll(selector).forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.confirmed === '1') {
                    return;
                }

                const formIssue = getSubjectCreateFormIssue(form);
                if (formIssue) {
                    event.preventDefault();
                    showFormError('Cannot Add Subject Time', formIssue);
                    return;
                }

                event.preventDefault();
                const config = buildConfig(form);
                const proceed = () => {
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
                            proceed();
                        }
                    });
                    return;
                }

                if (window.confirm(`${config.title}\n\n${config.text}`)) {
                    proceed();
                }
            });
        });
    };

    confirmSubmit('.js-create-form', () => ({
        title: 'Create time slot?',
        text: 'A new time slot will be saved.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, create',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5'
    }));

    confirmSubmit('.js-edit-form', (form) => ({
        title: 'Save changes?',
        text: `Update details for ${form.dataset.subject || 'this schedule'}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, save',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5'
    }));

    confirmSubmit('.js-delete-time-form', (form) => ({
        title: 'Delete time slot?',
        text: `Remove schedule for ${form.dataset.label || 'this item'}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626'
    }));

    const validationErrors = Array.isArray(pageData.validationErrors) ?
        pageData.validationErrors : [];

    if (validationErrors.length > 0) {
        if (hasSwal) {
            window.Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: validationErrors[0],
            });
        } else {
            window.alert(`Validation Error\n\n${validationErrors[0]}`);
        }
        return;
    }

    const flash = pageData.flash || {};

    if (flash.error) {
        if (hasSwal) {
            window.Swal.fire({
                icon: 'error',
                title: 'Error',
                text: flash.error,
            });
        } else {
            window.alert(`Error\n\n${flash.error}`);
        }
    } else if (flash.warning) {
        if (hasSwal) {
            window.Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: flash.warning,
            });
        } else {
            window.alert(`Warning\n\n${flash.warning}`);
        }
    } else if (flash.success) {
        if (hasSwal) {
            window.Swal.fire({
                icon: 'success',
                title: 'Success',
                text: flash.success,
                timer: 2200,
                showConfirmButton: false,
            });
        } else {
            window.alert(`Success\n\n${flash.success}`);
        }
    }
});
