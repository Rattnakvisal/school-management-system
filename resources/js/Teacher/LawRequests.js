document.addEventListener("DOMContentLoaded", () => {
    const subjectSelect = document.getElementById("subject");
    const timeOptionsContainer = document.getElementById("subject_time_options");
    const requestedForInput = document.getElementById("requested_for");
    const dataNode = document.getElementById("teacher-law-request-data");
    const submitForm = document.querySelector(".js-law-request-submit-form");
    const deleteForms = Array.from(
        document.querySelectorAll(".js-law-request-delete-form"),
    );
    const hasSwal = typeof window.Swal !== "undefined";

    const defaultPageData = {
        approvalAlerts: [],
        validationErrors: [],
        subjectTimeOptionsBySubject: {},
        formDefaults: {
            subject_id: "all",
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
            pageData = JSON.parse(dataNode.textContent || "{}");
        } catch (error) {
            console.error("Unable to parse teacher law request data.", error);
            pageData = defaultPageData;
        }
    }

    const subjectTimeMap =
        pageData.subjectTimeOptionsBySubject &&
        typeof pageData.subjectTimeOptionsBySubject === "object" ?
        pageData.subjectTimeOptionsBySubject : {};

    const defaultTimeKeys = pageData.formDefaults && Array.isArray(pageData.formDefaults.subject_time_keys) ?
        pageData.formDefaults.subject_time_keys : [];

    let lastAutoRequestedFor = "";

    const queueAlerts = (items) => {
        const alerts = Array.isArray(items) ? items.filter(Boolean) : [];
        if (alerts.length === 0) {
            return Promise.resolve();
        }

        document.querySelectorAll(".js-inline-flash").forEach((element) => {
            element.classList.add("hidden");
        });

        return alerts.reduce((chain, item) => {
            return chain.then(() => showAlert(item));
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
        return Promise.resolve({
            isConfirmed: true
        });
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

    const escapeHtml = (value) => {
        return String(value || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    };

    const getSubjectOptions = () => {
        const subjectId = String(subjectSelect.value || "all");
        const options = subjectTimeMap[subjectId];
        return Array.isArray(options) ? options : [];
    };

    const getSelectedTimeItems = () => {
        const checkedKeys = Array.from(
                timeOptionsContainer.querySelectorAll(
                    "input.js-time-checkbox[data-time-key]:checked",
                ),
            )
            .map((checkbox) => String(checkbox.dataset.timeKey || ""))
            .filter((key) => key !== "");

        const options = getSubjectOptions();
        return checkedKeys
            .map((key) => {
                return options.find((item) => String(item && item.key ? item.key : "") === key) || null;
            })
            .filter(Boolean);
    };

    const getAutoRequestedForDate = () => {
        const selectedItem = getSelectedTimeItems()[0] || null;
        const dayKey = String(
                selectedItem && selectedItem.day_of_week ? selectedItem.day_of_week : "",
            )
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

        if (
            requestedForInput.value === "" ||
            requestedForInput.value === lastAutoRequestedFor
        ) {
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

    const renderTimeOptions = (preferredKeys) => {
        const options = getSubjectOptions();
        const selectedKeySet = new Set(
            Array.isArray(preferredKeys) ?
            preferredKeys.map((key) => String(key || "")) : [],
        );

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
            const optionDay = String(
                item && item.day_of_week ? item.day_of_week : "",
            );
            const optionId = `subject-time-${optionKey.replace(
                /[^a-zA-Z0-9_-]/g,
                "-",
            )}`;
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

            textWrap.appendChild(title);
            label.appendChild(checkbox);
            label.appendChild(textWrap);
            timeOptionsContainer.appendChild(label);

            setCardState(label, checked);

            checkbox.addEventListener("change", () => {
                setCardState(label, checkbox.checked);
                syncRequestedForDate();
            });
        });

        syncRequestedForDate();
    };

    const flash = pageData.flash || {};
    const alertQueue = [];

    const approvalAlerts = Array.isArray(pageData.approvalAlerts)
        ? pageData.approvalAlerts
        : [];
    approvalAlerts.forEach((alertItem) => {
        alertQueue.push({
            icon: "success",
            title: String(
                alertItem && alertItem.title
                    ? alertItem.title
                    : "Law request approved",
            ),
            text: String(
                alertItem && alertItem.text
                    ? alertItem.text
                    : "Your law request has been approved.",
            ),
        });
    });

    if (String(flash.success || "").trim() !== "") {
        alertQueue.push({
            icon: "success",
            title: "Success",
            text: String(flash.success),
        });
    }

    if (String(flash.warning || "").trim() !== "") {
        alertQueue.push({
            icon: "warning",
            title: "Warning",
            text: String(flash.warning),
        });
    }

    if (String(flash.error || "").trim() !== "") {
        alertQueue.push({
            icon: "error",
            title: "Error",
            text: String(flash.error),
        });
    }

    const validationErrors = Array.isArray(pageData.validationErrors) ?
        pageData.validationErrors : [];
    if (validationErrors.length > 0) {
        alertQueue.push({
            icon: "error",
            title: "Validation Error",
            text: String(validationErrors[0] || ""),
        });
    }

    queueAlerts(alertQueue);

    if (submitForm) {
        submitForm.addEventListener("submit", (event) => {
            if (submitForm.dataset.confirmed === "1") {
                submitForm.dataset.confirmed = "0";
                return;
            }

            event.preventDefault();
            showConfirm({
                title: "Are you sure?",
                text: "Do you want to submit this law request?",
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
            const requestLabel = String(
                form.dataset.requestLabel || "this law request",
            );

            showConfirm({
                title: "Delete request?",
                text: `Remove ${requestLabel}? This cannot be undone.`,
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

    if (subjectSelect && timeOptionsContainer) {
        const initialSubjectId = String(
            pageData.formDefaults && pageData.formDefaults.subject_id ?
            pageData.formDefaults.subject_id :
            "all",
        );
        const initialTimeKeys = Array.isArray(defaultTimeKeys) ? defaultTimeKeys : [];

        if (subjectSelect.value !== initialSubjectId) {
            subjectSelect.value = initialSubjectId;
        }

        renderTimeOptions(initialTimeKeys);

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

        subjectSelect.addEventListener("change", () => {
            if (requestedForInput) {
                requestedForInput.value = "";
            }
            lastAutoRequestedFor = "";
            renderTimeOptions([]);
        });
    }
});
