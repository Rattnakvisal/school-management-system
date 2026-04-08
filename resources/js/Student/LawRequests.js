document.addEventListener("DOMContentLoaded", () => {
    const subjectSelect = document.getElementById("subject");
    const timeOptionsContainer = document.getElementById("subject_time_options");
    const requestedForInput = document.getElementById("requested_for");
    const teacherRecipientList = document.getElementById("teacher_recipient_list");
    const teacherRecipientEmpty = document.getElementById("teacher_recipient_empty");
    const dataNode = document.getElementById("student-law-request-data");
    const submitForm = document.querySelector(".js-student-law-request-submit-form");
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
                    ...(parsed.formDefaults && typeof parsed.formDefaults === "object" ? parsed.formDefaults : {}),
                },
                flash: {
                    ...defaultPageData.flash,
                    ...(parsed.flash && typeof parsed.flash === "object" ? parsed.flash : {}),
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
        pageData.formDefaults && Array.isArray(pageData.formDefaults.subject_time_keys)
            ? pageData.formDefaults.subject_time_keys
            : [];

    let lastAutoRequestedFor = "";

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
            return window.Swal.fire(config).then((result) => Boolean(result && result.isConfirmed));
        }

        return Promise.resolve(window.confirm(`${config.title}\n\n${config.text}`));
    };

    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    };

    const getSubjectOptions = () => {
        if (!subjectSelect) {
            return [];
        }

        const subjectId = String(subjectSelect.value || "");
        const options = subjectTimeMap[subjectId];
        return Array.isArray(options) ? options : [];
    };

    const getSelectedTimeItems = () => {
        if (!timeOptionsContainer) {
            return [];
        }

        const checkedKeys = Array.from(
            timeOptionsContainer.querySelectorAll("input.js-time-checkbox[data-time-key]:checked"),
        )
            .map((checkbox) => String(checkbox.dataset.timeKey || ""))
            .filter((key) => key !== "");

        const options = getSubjectOptions();
        return checkedKeys
            .map((key) => options.find((item) => String(item && item.key ? item.key : "") === key) || null)
            .filter(Boolean);
    };

    const getAutoRequestedForDate = () => {
        const selectedItem = getSelectedTimeItems()[0] || null;
        const dayKey = String(selectedItem && selectedItem.day_of_week ? selectedItem.day_of_week : "")
            .toLowerCase()
            .trim();

        const dayMap = {
            sunday: 0,
            monday: 1,
            tuesday: 2,
            wednesday: 3,
            thursday: 4,
            friday: 5,
            saturday: 6,
        };

        if (!dayKey || dayKey === "all" || !Object.prototype.hasOwnProperty.call(dayMap, dayKey)) {
            return "";
        }

        const today = new Date();
        const nextDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const targetDay = dayMap[dayKey];
        const delta = (targetDay - nextDate.getDay() + 7) % 7;
        nextDate.setDate(nextDate.getDate() + delta);

        return formatDate(nextDate);
    };

    const syncRequestedForDate = () => {
        if (!requestedForInput) {
            return;
        }

        const autoDate = getAutoRequestedForDate();
        if (autoDate === "") {
            if (requestedForInput.value === lastAutoRequestedFor) {
                requestedForInput.value = "";
            }
            lastAutoRequestedFor = "";
            return;
        }

        if (requestedForInput.value === "" || requestedForInput.value === lastAutoRequestedFor) {
            requestedForInput.value = autoDate;
        }

        lastAutoRequestedFor = autoDate;
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

    const renderTeacherRecipients = () => {
        if (!teacherRecipientList || !teacherRecipientEmpty || !subjectSelect) {
            return;
        }

        const selectedItem = getSelectedTimeItems()[0] || null;
        const timeKey = String(selectedItem && selectedItem.key ? selectedItem.key : "");
        const recipients = Array.isArray(teacherRecipientsByTime[timeKey])
            ? teacherRecipientsByTime[timeKey]
            : [];

        teacherRecipientList.innerHTML = "";

        if (recipients.length === 0) {
            teacherRecipientList.classList.add("hidden");
            teacherRecipientEmpty.classList.remove("hidden");
            return;
        }

        recipients.forEach((recipient) => {
            const chip = document.createElement("span");
            chip.className =
                "inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700";
            chip.textContent = String(recipient && recipient.name ? recipient.name : "Assigned Teacher");
            teacherRecipientList.appendChild(chip);
        });

        teacherRecipientList.classList.remove("hidden");
        teacherRecipientEmpty.classList.add("hidden");
    };

    const renderTimeOptions = (preferredKeys) => {
        if (!timeOptionsContainer) {
            return;
        }

        const options = getSubjectOptions();
        const normalizedKeys = Array.isArray(preferredKeys)
            ? preferredKeys.map((key) => String(key || "")).filter((key) => key !== "")
            : [];
        const selectedKeySet = new Set(normalizedKeys.slice(0, 1));

        timeOptionsContainer.innerHTML = "";

        if (options.length === 0) {
            const emptyState = document.createElement("div");
            emptyState.className =
                "rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-500";
            emptyState.textContent = "No time available";
            timeOptionsContainer.appendChild(emptyState);
            syncRequestedForDate();
            return;
        }

        options.forEach((item) => {
            const optionKey = String(item && item.key ? item.key : "");
            const optionLabel = String(item && item.label ? item.label : "");
            const optionDay = String(item && item.day_of_week ? item.day_of_week : "");
            const teacherName = String(item && item.teacher_name ? item.teacher_name : "");
            const optionId = `student-subject-time-${optionKey.replace(/[^a-zA-Z0-9_-]/g, "-")}`;
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
            timeOptionsContainer.appendChild(label);

            setCardState(label, checked);

            checkbox.addEventListener("change", () => {
                if (checkbox.checked) {
                    Array.from(
                        timeOptionsContainer.querySelectorAll("input.js-time-checkbox"),
                    ).forEach((otherCheckbox) => {
                        if (otherCheckbox !== checkbox) {
                            otherCheckbox.checked = false;
                            const otherCard = otherCheckbox.closest(".js-time-card");
                            setCardState(otherCard, false);
                        }
                    });
                }

                setCardState(label, checkbox.checked);
                syncRequestedForDate();
                renderTeacherRecipients();
            });
        });

        syncRequestedForDate();
        renderTeacherRecipients();
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

    if (requestedForInput) {
        requestedForInput.addEventListener("input", () => {
            if (requestedForInput.value !== lastAutoRequestedFor) {
                lastAutoRequestedFor = "";
            }
        });

        requestedForInput.addEventListener("change", () => {
            if (requestedForInput.value !== lastAutoRequestedFor) {
                lastAutoRequestedFor = "";
            }
        });
    }

    if (subjectSelect) {
        const initialSubjectId = String(
            pageData.formDefaults && pageData.formDefaults.subject_id
                ? pageData.formDefaults.subject_id
                : subjectSelect.value || "",
        );

        if (subjectSelect.value !== initialSubjectId) {
            subjectSelect.value = initialSubjectId;
        }

            renderTimeOptions(defaultTimeKeys);
        renderTeacherRecipients();

        subjectSelect.addEventListener("change", () => {
            if (requestedForInput) {
                requestedForInput.value = "";
            }

            lastAutoRequestedFor = "";
            renderTimeOptions([]);
        });
    }

    if (alertQueue.length > 0) {
        document.querySelectorAll(".js-inline-flash").forEach((element) => {
            element.classList.add("hidden");
        });

        alertQueue.reduce((chain, config) => {
            return chain.then(() => showAlert(config));
        }, Promise.resolve());
    }
});
