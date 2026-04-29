document.addEventListener("DOMContentLoaded", () => {
    const subjectSelect = document.getElementById("subject");
    const timeOptionsContainer = document.getElementById(
        "subject_time_options",
    );
    const requestedForInput = document.getElementById("requested_for");
    const requestedUntilInput = document.getElementById("requested_until");
    const teacherRecipientList = document.getElementById(
        "teacher_recipient_list",
    );
    const teacherRecipientEmpty = document.getElementById(
        "teacher_recipient_empty",
    );
    const dataNode = document.getElementById("student-law-request-data");
    const submitForm = document.querySelector(
        ".js-student-law-request-submit-form",
    );
    const deleteForms = Array.from(
        document.querySelectorAll(".js-student-law-request-delete-form"),
    );
    const hasSwal = typeof window.Swal !== "undefined";

    const defaultPageData = {
        validationErrors: [],
        subjectTimeOptionsBySubject: {},
        teacherRecipientsByTime: {},
        formDefaults: {
            subject_id: "",
            subject_time_keys: [],
            requested_for: "",
            requested_until: "",
        },
        flash: {
            success: "",
            warning: "",
            error: "",
        },
    };

    let pageData = defaultPageData;
    if (dataNode) {
        try {
            const parsed = JSON.parse(dataNode.textContent || "{}");
            pageData = {
                ...defaultPageData,
                ...parsed,
                formDefaults: {
                    ...defaultPageData.formDefaults,
                    ...(parsed.formDefaults &&
                    typeof parsed.formDefaults === "object"
                        ? parsed.formDefaults
                        : {}),
                },
                flash: {
                    ...defaultPageData.flash,
                    ...(parsed.flash && typeof parsed.flash === "object"
                        ? parsed.flash
                        : {}),
                },
            };
        } catch (error) {
            console.error("Unable to parse student law request data.", error);
            pageData = defaultPageData;
        }
    }

    const subjectTimeMap =
        pageData.subjectTimeOptionsBySubject &&
        typeof pageData.subjectTimeOptionsBySubject === "object"
            ? pageData.subjectTimeOptionsBySubject
            : {};
    const teacherRecipientsByTime =
        pageData.teacherRecipientsByTime &&
        typeof pageData.teacherRecipientsByTime === "object"
            ? pageData.teacherRecipientsByTime
            : {};

    const defaultTimeKeys =
        pageData.formDefaults &&
        Array.isArray(pageData.formDefaults.subject_time_keys)
            ? pageData.formDefaults.subject_time_keys
            : [];

    let lastAutoRequestedFor = "";
    const allTimeOptionKey = "__all_subject_times__";

    const queueAlerts = (items) => {
        const alerts = Array.isArray(items) ? items.filter(Boolean) : [];
        if (alerts.length === 0) {
            return Promise.resolve();
        }

        document.querySelectorAll(".js-inline-flash").forEach((element) => {
            element.classList.add("hidden");
        });

        return alerts.reduce((chain, config) => {
            return chain.then(() => showAlert(config));
        }, Promise.resolve());
    };

    const showAlert = (options) => {
        const config = {
            icon: options.icon || "success",
            title: options.title || "Notification",
            text: options.text || "",
            confirmButtonText: options.confirmButtonText || "OK",
            confirmButtonColor: options.confirmButtonColor || "#4f46e5",
        };

        if (hasSwal) {
            return window.Swal.fire(config);
        }

        window.alert(`${config.title}\n\n${config.text}`);
        return Promise.resolve({ isConfirmed: true });
    };

    const showConfirm = (options) => {
        const config = {
            icon: options.icon || "warning",
            title: options.title || "Are you sure?",
            text: options.text || "",
            showCancelButton: true,
            confirmButtonText: options.confirmButtonText || "Yes",
            cancelButtonText: options.cancelButtonText || "Cancel",
            confirmButtonColor: options.confirmButtonColor || "#4f46e5",
            cancelButtonColor: options.cancelButtonColor || "#94a3b8",
        };

        if (hasSwal) {
            return window.Swal.fire(config).then((result) =>
                Boolean(result && result.isConfirmed),
            );
        }

        return Promise.resolve(
            window.confirm(`${config.title}\n\n${config.text}`),
        );
    };

    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    };

    const getSubjectOptions = (targetSubjectSelect = subjectSelect) => {
        if (!targetSubjectSelect) {
            return [];
        }

        const subjectId = String(targetSubjectSelect.value || "");
        const options = subjectTimeMap[subjectId];
        return Array.isArray(options) ? options : [];
    };

    const getSelectedTimeItems = (
        targetSubjectSelect = subjectSelect,
        targetTimeOptionsContainer = timeOptionsContainer,
    ) => {
        if (!targetTimeOptionsContainer) {
            return [];
        }

        const checkedKeys = Array.from(
            targetTimeOptionsContainer.querySelectorAll(
                "input.js-time-checkbox[data-time-key]:checked",
            ),
        )
            .map((checkbox) => String(checkbox.dataset.timeKey || ""))
            .filter((key) => key !== "");

        const options = getSubjectOptions(targetSubjectSelect);
        return checkedKeys
            .map(
                (key) =>
                    options.find(
                        (item) =>
                            String(item && item.key ? item.key : "") === key,
                    ) || null,
            )
            .filter(Boolean);
    };

    const getAutoRequestedForDate = (
        targetSubjectSelect = subjectSelect,
        targetTimeOptionsContainer = timeOptionsContainer,
    ) => {
        const dayMap = {
            sunday: 0,
            monday: 1,
            tuesday: 2,
            wednesday: 3,
            thursday: 4,
            friday: 5,
            saturday: 6,
        };

        const selectedDays = Array.from(
            new Set(
                getSelectedTimeItems(
                    targetSubjectSelect,
                    targetTimeOptionsContainer,
                )
                    .map((item) =>
                        String(item && item.day_of_week ? item.day_of_week : "")
                            .toLowerCase()
                            .trim(),
                    )
                    .filter(
                        (dayKey) =>
                            dayKey &&
                            dayKey !== "all" &&
                            Object.prototype.hasOwnProperty.call(
                                dayMap,
                                dayKey,
                            ),
                    ),
            ),
        );

        const dayKey = selectedDays.length === 1 ? selectedDays[0] : "";
        if (
            !dayKey ||
            dayKey === "all" ||
            !Object.prototype.hasOwnProperty.call(dayMap, dayKey)
        ) {
            return "";
        }

        const today = new Date();
        const nextDate = new Date(
            today.getFullYear(),
            today.getMonth(),
            today.getDate(),
        );
        const targetDay = dayMap[dayKey];
        const delta = (targetDay - nextDate.getDay() + 7) % 7;
        nextDate.setDate(nextDate.getDate() + delta);

        return formatDate(nextDate);
    };

    const syncDateRange = (fromInput, untilInput) => {
        if (!fromInput || !untilInput || !fromInput.value) {
            return;
        }

        if (!untilInput.value || untilInput.value < fromInput.value) {
            untilInput.value = fromInput.value;
        }
    };

    const syncRequestedForDate = (
        targetSubjectSelect = subjectSelect,
        targetTimeOptionsContainer = timeOptionsContainer,
        targetRequestedForInput = requestedForInput,
        lastAutoRef = null,
        targetRequestedUntilInput = requestedUntilInput,
    ) => {
        const getLastAuto = () =>
            lastAutoRef ? lastAutoRef.value : lastAutoRequestedFor;
        const setLastAuto = (value) => {
            if (lastAutoRef) {
                lastAutoRef.value = value;
                return;
            }

            lastAutoRequestedFor = value;
        };

        if (!targetRequestedForInput) {
            return;
        }

        const autoDate = getAutoRequestedForDate(
            targetSubjectSelect,
            targetTimeOptionsContainer,
        );
        if (autoDate === "") {
            if (targetRequestedForInput.value === getLastAuto()) {
                targetRequestedForInput.value = "";
            }
            setLastAuto("");
            syncDateRange(targetRequestedForInput, targetRequestedUntilInput);
            return;
        }

        if (
            targetRequestedForInput.value === "" ||
            targetRequestedForInput.value === getLastAuto()
        ) {
            targetRequestedForInput.value = autoDate;
        }

        setLastAuto(autoDate);
        syncDateRange(targetRequestedForInput, targetRequestedUntilInput);
    };

    const setCardState = (card, checked) => {
        if (!card) {
            return;
        }

        card.classList.toggle("border-indigo-300", checked);
        card.classList.toggle("bg-indigo-50", checked);
        card.classList.toggle("border-slate-200", !checked);
        card.classList.toggle("bg-white", !checked);
    };

    const makeTimeOptionId = (prefix, optionKey, targetTimeOptionsContainer) => {
        const scope = String(
            targetTimeOptionsContainer && targetTimeOptionsContainer.id
                ? targetTimeOptionsContainer.id
                : "main",
        ).replace(/[^a-zA-Z0-9_-]/g, "-");
        const safeKey = String(optionKey || "option").replace(
            /[^a-zA-Z0-9_-]/g,
            "-",
        );

        return `${prefix}-${scope}-${safeKey}`;
    };

    const syncAllTimeCard = (
        targetTimeOptionsContainer = timeOptionsContainer,
    ) => {
        if (!targetTimeOptionsContainer) {
            return;
        }

        const allCheckbox = targetTimeOptionsContainer.querySelector(
            `input.js-time-all-checkbox[data-time-key="${allTimeOptionKey}"]`,
        );
        const allCard = allCheckbox
            ? allCheckbox.closest(".js-time-card")
            : null;
        const realCheckboxes = Array.from(
            targetTimeOptionsContainer.querySelectorAll(
                "input.js-time-checkbox[data-time-key]",
            ),
        );

        if (!allCheckbox || realCheckboxes.length === 0) {
            return;
        }

        const checkedCount = realCheckboxes.filter(
            (checkbox) => checkbox.checked,
        ).length;
        allCheckbox.checked = checkedCount === realCheckboxes.length;
        allCheckbox.indeterminate =
            checkedCount > 0 && checkedCount < realCheckboxes.length;
        setCardState(allCard, checkedCount === realCheckboxes.length);
    };

    const renderTeacherRecipients = (
        targetSubjectSelect = subjectSelect,
        targetTimeOptionsContainer = timeOptionsContainer,
        targetTeacherRecipientList = teacherRecipientList,
        targetTeacherRecipientEmpty = teacherRecipientEmpty,
    ) => {
        if (
            !targetTeacherRecipientList ||
            !targetTeacherRecipientEmpty ||
            !targetSubjectSelect
        ) {
            return;
        }

        const recipientsById = new Map();
        getSelectedTimeItems(
            targetSubjectSelect,
            targetTimeOptionsContainer,
        ).forEach((selectedItem) => {
            const timeKey = String(
                selectedItem && selectedItem.key ? selectedItem.key : "",
            );
            const recipients = Array.isArray(teacherRecipientsByTime[timeKey])
                ? teacherRecipientsByTime[timeKey]
                : [];

            recipients.forEach((recipient) => {
                const recipientId = String(
                    recipient && recipient.id ? recipient.id : "",
                );
                const recipientName = String(
                    recipient && recipient.name
                        ? recipient.name
                        : "Assigned Teacher",
                );
                const mapKey = recipientId !== "" ? recipientId : recipientName;
                recipientsById.set(mapKey, {
                    id: recipientId,
                    name: recipientName,
                });
            });
        });

        targetTeacherRecipientList.innerHTML = "";

        if (recipientsById.size === 0) {
            targetTeacherRecipientList.classList.add("hidden");
            targetTeacherRecipientEmpty.classList.remove("hidden");
            return;
        }

        recipientsById.forEach((recipient) => {
            const chip = document.createElement("span");
            chip.className =
                "inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700";
            chip.textContent = String(
                recipient && recipient.name
                    ? recipient.name
                    : "Assigned Teacher",
            );
            targetTeacherRecipientList.appendChild(chip);
        });

        targetTeacherRecipientList.classList.remove("hidden");
        targetTeacherRecipientEmpty.classList.add("hidden");
    };

    const renderTimeOptions = (
        preferredKeys,
        targetSubjectSelect = subjectSelect,
        targetTimeOptionsContainer = timeOptionsContainer,
        targetRequestedForInput = requestedForInput,
        lastAutoRef = null,
        targetRequestedUntilInput = requestedUntilInput,
        targetTeacherRecipientList = teacherRecipientList,
        targetTeacherRecipientEmpty = teacherRecipientEmpty,
    ) => {
        if (!targetTimeOptionsContainer) {
            return;
        }

        const options = getSubjectOptions(targetSubjectSelect);
        const normalizedKeys = Array.isArray(preferredKeys)
            ? preferredKeys
                  .map((key) => String(key || ""))
                  .filter((key) => key !== "")
            : [];
        const optionKeys = options
            .map((item) => String(item && item.key ? item.key : ""))
            .filter((key) => key !== "");
        const selectedKeySet = normalizedKeys.includes(allTimeOptionKey)
            ? new Set(optionKeys)
            : new Set(normalizedKeys);

        targetTimeOptionsContainer.innerHTML = "";

        if (options.length === 0) {
            const emptyState = document.createElement("div");
            emptyState.className =
                "rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-500";
            emptyState.textContent = "No time available";
            targetTimeOptionsContainer.appendChild(emptyState);
            syncRequestedForDate(
                targetSubjectSelect,
                targetTimeOptionsContainer,
                targetRequestedForInput,
                lastAutoRef,
                targetRequestedUntilInput,
            );
            renderTeacherRecipients(
                targetSubjectSelect,
                targetTimeOptionsContainer,
                targetTeacherRecipientList,
                targetTeacherRecipientEmpty,
            );
            return;
        }

        if (options.length > 1) {
            const allOptionId = makeTimeOptionId(
                "student-subject-time",
                "all",
                targetTimeOptionsContainer,
            );
            const allChecked =
                optionKeys.length > 0 &&
                optionKeys.every((optionKey) => selectedKeySet.has(optionKey));

            const label = document.createElement("label");
            label.setAttribute("for", allOptionId);
            label.className =
                "js-time-card flex cursor-pointer items-start gap-3 rounded-xl border px-3 py-3 text-sm transition hover:border-indigo-300 hover:bg-indigo-50/40";
            label.dataset.timeKey = allTimeOptionKey;

            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.id = allOptionId;
            checkbox.className =
                "js-time-all-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300";
            checkbox.checked = allChecked;
            checkbox.dataset.timeKey = allTimeOptionKey;

            const textWrap = document.createElement("div");
            textWrap.className = "min-w-0";

            const title = document.createElement("div");
            title.className = "font-semibold text-slate-800";
            title.textContent = "All";

            const meta = document.createElement("div");
            meta.className = "mt-1 text-xs font-semibold text-slate-500";
            meta.textContent = "Select every class time for this subject";

            textWrap.appendChild(title);
            textWrap.appendChild(meta);
            label.appendChild(checkbox);
            label.appendChild(textWrap);
            targetTimeOptionsContainer.appendChild(label);
            setCardState(label, allChecked);

            checkbox.addEventListener("change", () => {
                const checked = checkbox.checked;
                Array.from(
                    targetTimeOptionsContainer.querySelectorAll(
                        "input.js-time-checkbox[data-time-key]",
                    ),
                ).forEach((timeCheckbox) => {
                    timeCheckbox.checked = checked;
                    setCardState(
                        timeCheckbox.closest(".js-time-card"),
                        checked,
                    );
                });

                checkbox.indeterminate = false;
                setCardState(label, checked);
                syncRequestedForDate(
                    targetSubjectSelect,
                    targetTimeOptionsContainer,
                    targetRequestedForInput,
                    lastAutoRef,
                    targetRequestedUntilInput,
                );
                renderTeacherRecipients(
                    targetSubjectSelect,
                    targetTimeOptionsContainer,
                    targetTeacherRecipientList,
                    targetTeacherRecipientEmpty,
                );
            });
        }

        options.forEach((item) => {
            const optionKey = String(item && item.key ? item.key : "");
            const optionLabel = String(item && item.label ? item.label : "");
            const optionDay = String(
                item && item.day_of_week ? item.day_of_week : "",
            );
            const teacherName = String(
                item && item.teacher_name ? item.teacher_name : "",
            );
            const optionId = makeTimeOptionId(
                "student-subject-time",
                optionKey,
                targetTimeOptionsContainer,
            );
            const checked = selectedKeySet.has(optionKey);

            const label = document.createElement("label");
            label.setAttribute("for", optionId);
            label.className =
                "js-time-card flex cursor-pointer items-start gap-3 rounded-xl border px-3 py-3 text-sm transition hover:border-indigo-300 hover:bg-indigo-50/40";
            label.dataset.dayOfWeek = optionDay;
            label.dataset.timeKey = optionKey;

            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.id = optionId;
            checkbox.name = "subject_time_keys[]";
            checkbox.value = optionKey;
            checkbox.className =
                "js-time-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300";
            checkbox.checked = checked;
            checkbox.dataset.timeKey = optionKey;
            checkbox.dataset.dayOfWeek = optionDay;

            const textWrap = document.createElement("div");
            textWrap.className = "min-w-0";

            const title = document.createElement("div");
            title.className = "font-semibold text-slate-800";
            title.textContent = optionLabel;

            const meta = document.createElement("div");
            meta.className = "mt-1 text-xs font-semibold text-slate-500";
            meta.textContent = `Teacher: ${teacherName || "Not assigned yet"}`;

            textWrap.appendChild(title);
            textWrap.appendChild(meta);
            label.appendChild(checkbox);
            label.appendChild(textWrap);
            targetTimeOptionsContainer.appendChild(label);

            setCardState(label, checked);

            checkbox.addEventListener("change", () => {
                setCardState(label, checkbox.checked);
                syncAllTimeCard(targetTimeOptionsContainer);
                syncRequestedForDate(
                    targetSubjectSelect,
                    targetTimeOptionsContainer,
                    targetRequestedForInput,
                    lastAutoRef,
                    targetRequestedUntilInput,
                );
                renderTeacherRecipients(
                    targetSubjectSelect,
                    targetTimeOptionsContainer,
                    targetTeacherRecipientList,
                    targetTeacherRecipientEmpty,
                );
            });
        });

        syncAllTimeCard(targetTimeOptionsContainer);
        syncRequestedForDate(
            targetSubjectSelect,
            targetTimeOptionsContainer,
            targetRequestedForInput,
            lastAutoRef,
            targetRequestedUntilInput,
        );
        renderTeacherRecipients(
            targetSubjectSelect,
            targetTimeOptionsContainer,
            targetTeacherRecipientList,
            targetTeacherRecipientEmpty,
        );
    };

    if (submitForm) {
        submitForm.addEventListener("submit", (event) => {
            if (submitForm.dataset.confirmed === "1") {
                submitForm.dataset.confirmed = "0";
                return;
            }

            event.preventDefault();
            showConfirm({
                title: "Submit law request?",
                text: "Your teachers will be notified about this attendance request.",
                confirmButtonText: "Yes, submit",
                cancelButtonText: "Cancel",
            }).then((confirmed) => {
                if (!confirmed) {
                    return;
                }

                submitForm.dataset.confirmed = "1";
                submitForm.submit();
            });
        });
    }

    deleteForms.forEach((form) => {
        form.addEventListener("submit", (event) => {
            event.preventDefault();

            showConfirm({
                title: "Delete request?",
                text: `Remove ${String(form.dataset.requestLabel || "this law request")}?`,
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#dc2626",
            }).then((confirmed) => {
                if (!confirmed) {
                    return;
                }

                form.submit();
            });
        });
    });

    document
        .querySelectorAll(".js-student-law-request-edit-form")
        .forEach((editForm) => {
            const editSubjectSelect = editForm.querySelector(
                ".js-edit-student-law-subject",
            );
            const editTimeOptionsContainer = editForm.querySelector(
                ".js-edit-student-law-time-options",
            );
            const editRequestedForInput = editForm.querySelector(
                '[name="requested_for"]',
            );
            const editRequestedUntilInput = editForm.querySelector(
                '[name="requested_until"]',
            );
            const editTeacherRecipientList = editForm.querySelector(
                ".js-edit-teacher-recipient-list",
            );
            const editTeacherRecipientEmpty = editForm.querySelector(
                ".js-edit-teacher-recipient-empty",
            );
            const lastEditAutoRequestedFor = { value: "" };
            let selectedTimeKeys = [];

            try {
                selectedTimeKeys = JSON.parse(
                    editSubjectSelect?.dataset.selectedTimeKeys || "[]",
                );
            } catch (error) {
                selectedTimeKeys = [];
            }

            if (!Array.isArray(selectedTimeKeys)) {
                selectedTimeKeys = [];
            }

            if (editSubjectSelect && editTimeOptionsContainer) {
                renderTimeOptions(
                    selectedTimeKeys,
                    editSubjectSelect,
                    editTimeOptionsContainer,
                    editRequestedForInput,
                    lastEditAutoRequestedFor,
                    editRequestedUntilInput,
                    editTeacherRecipientList,
                    editTeacherRecipientEmpty,
                );

                editSubjectSelect.addEventListener("change", () => {
                    if (editRequestedForInput) {
                        editRequestedForInput.value = "";
                    }
                    lastEditAutoRequestedFor.value = "";
                    renderTimeOptions(
                        [],
                        editSubjectSelect,
                        editTimeOptionsContainer,
                        editRequestedForInput,
                        lastEditAutoRequestedFor,
                        editRequestedUntilInput,
                        editTeacherRecipientList,
                        editTeacherRecipientEmpty,
                    );
                });
            }

            if (editRequestedForInput) {
                editRequestedForInput.addEventListener("input", () => {
                    if (
                        editRequestedForInput.value !==
                        lastEditAutoRequestedFor.value
                    ) {
                        lastEditAutoRequestedFor.value = "";
                    }
                    syncDateRange(
                        editRequestedForInput,
                        editRequestedUntilInput,
                    );
                });

                editRequestedForInput.addEventListener("change", () => {
                    if (
                        editRequestedForInput.value !==
                        lastEditAutoRequestedFor.value
                    ) {
                        lastEditAutoRequestedFor.value = "";
                    }
                    syncDateRange(
                        editRequestedForInput,
                        editRequestedUntilInput,
                    );
                });
            }

            editRequestedUntilInput?.addEventListener("change", () => {
                syncDateRange(editRequestedForInput, editRequestedUntilInput);
            });
        });

    const alertQueue = [];
    const flash = pageData.flash || {};

    if (pageData.validationErrors.length > 0) {
        alertQueue.push({
            icon: "error",
            title: "Validation Error",
            text: String(pageData.validationErrors[0] || ""),
        });
    }

    if (String(flash.error || "").trim() !== "") {
        alertQueue.push({
            icon: "error",
            title: "Error",
            text: String(flash.error),
        });
    } else if (String(flash.warning || "").trim() !== "") {
        alertQueue.push({
            icon: "warning",
            title: "Warning",
            text: String(flash.warning),
        });
    } else if (String(flash.success || "").trim() !== "") {
        alertQueue.push({
            icon: "success",
            title: "Success",
            text: String(flash.success),
        });
    }

    if (subjectSelect && timeOptionsContainer) {
        const initialSubjectId = String(
            pageData.formDefaults && pageData.formDefaults.subject_id
                ? pageData.formDefaults.subject_id
                : subjectSelect.value || "",
        );

        if (subjectSelect.value !== initialSubjectId) {
            subjectSelect.value = initialSubjectId;
        }

        renderTimeOptions(defaultTimeKeys);

        if (requestedForInput) {
            requestedForInput.addEventListener("input", () => {
                if (requestedForInput.value !== lastAutoRequestedFor) {
                    lastAutoRequestedFor = "";
                }
                syncDateRange(requestedForInput, requestedUntilInput);
            });

            requestedForInput.addEventListener("change", () => {
                if (requestedForInput.value !== lastAutoRequestedFor) {
                    lastAutoRequestedFor = "";
                }
                syncDateRange(requestedForInput, requestedUntilInput);
            });
        }

        requestedUntilInput?.addEventListener("change", () => {
            syncDateRange(requestedForInput, requestedUntilInput);
        });

        subjectSelect.addEventListener("change", () => {
            if (requestedForInput) {
                requestedForInput.value = "";
            }

            lastAutoRequestedFor = "";
            renderTimeOptions([]);
        });
    }

    queueAlerts(alertQueue);
});
