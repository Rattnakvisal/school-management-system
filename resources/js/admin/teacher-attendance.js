document.addEventListener("DOMContentLoaded", () => {
    const hasSwal = typeof window.Swal !== "undefined";
    const pageDataNode = document.getElementById("admin-teacher-attendance-data");
    const submitForm = document.querySelector(".js-admin-teacher-attendance-form");
    const pageData = {
        validationErrors: [],
        flash: {
            success: "",
            warning: "",
            error: "",
        },
    };

    if (pageDataNode) {
        try {
            const parsed = JSON.parse(pageDataNode.textContent || "{}");
            if (parsed && typeof parsed === "object") {
                Object.assign(pageData, parsed);
                pageData.flash = {
                    ...pageData.flash,
                    ...(parsed.flash && typeof parsed.flash === "object"
                        ? parsed.flash
                        : {}),
                };
            }
        } catch (error) {
            console.error(
                "Unable to parse admin teacher attendance page data.",
                error,
            );
        }
    }

    const statusFields = Array.from(
        document.querySelectorAll(".js-admin-teacher-status"),
    );
    const tableRows = Array.from(
        document.querySelectorAll(".js-admin-teacher-row"),
    );

    const activeBoxClasses = [
        "border-indigo-300",
        "bg-indigo-50",
        "text-indigo-700",
        "shadow-sm",
    ];
    const inactiveBoxClasses = [
        "border-slate-200",
        "bg-white",
        "text-slate-600",
    ];
    const statusBadgeClasses = {
        present: ["border-emerald-200", "bg-emerald-50", "text-emerald-700"],
        absent: ["border-red-200", "bg-red-50", "text-red-700"],
        late: ["border-amber-200", "bg-amber-50", "text-amber-700"],
        excused: ["border-sky-200", "bg-sky-50", "text-sky-700"],
        pending: ["border-amber-200", "bg-amber-50", "text-amber-700"],
        approved: ["border-emerald-200", "bg-emerald-50", "text-emerald-700"],
        rejected: ["border-red-200", "bg-red-50", "text-red-700"],
        empty: ["border-slate-200", "bg-slate-50", "text-slate-700"],
    };
    const allBadgeClasses = Array.from(
        new Set(Object.values(statusBadgeClasses).flat()),
    );

    const syncStatusBoxes = (field) => {
        const row = field.closest(".js-admin-teacher-row");
        if (!row) {
            return;
        }

        const selectedStatus = String(field.value || "").toLowerCase();
        const statusButtons = Array.from(
            row.querySelectorAll(".js-admin-status-box"),
        );

        statusButtons.forEach((button) => {
            const boxStatus = String(
                button.dataset.statusOption || "",
            ).toLowerCase();
            const isActive = boxStatus === selectedStatus;

            button.setAttribute("aria-pressed", isActive ? "true" : "false");
            activeBoxClasses.forEach((className) => {
                button.classList.toggle(className, isActive);
            });
            inactiveBoxClasses.forEach((className) => {
                button.classList.toggle(className, !isActive);
            });
        });
    };

    const syncAllStatusBoxes = () => {
        statusFields.forEach((field) => syncStatusBoxes(field));
    };

    const showMessage = (kind, title, text, options = {}) => {
        if (!text) {
            return;
        }

        if (hasSwal) {
            window.Swal.fire({
                icon: kind,
                title,
                text,
                confirmButtonColor: "#4f46e5",
                ...options,
            });
            return;
        }

        window.alert(`${title}\n\n${text}`);
    };

    const showConfirm = (options = {}) => {
        const config = {
            icon: options.icon || "question",
            title: options.title || "Are you sure?",
            text: options.text || "",
            showCancelButton: true,
            confirmButtonText: options.confirmButtonText || "Submit",
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

    const applyBadgeClasses = (element, status) => {
        if (!element) {
            return;
        }

        allBadgeClasses.forEach((className) =>
            element.classList.remove(className),
        );
        (statusBadgeClasses[status] || statusBadgeClasses.empty).forEach(
            (className) => {
                element.classList.add(className);
            },
        );
    };

    const labelForAttendanceStatus = (status) => {
        const normalized = String(status || "").toLowerCase();
        return (
            {
                present: "Present",
                absent: "Absent",
                late: "Late",
                excused: "Excused",
            }[normalized] || "Not Checked Yet"
        );
    };

    const labelForLawRequestStatus = (status) => {
        const normalized = String(status || "").toLowerCase();
        return (
            {
                pending: "Pending",
                approved: "Approved",
                rejected: "Rejected",
            }[normalized] || "Pending"
        );
    };

    const updateTeacherRowFromLawAction = (row, payload = {}) => {
        if (!row || !payload) {
            return;
        }

        const attendanceStatus = String(
            payload.attendance_status || "",
        ).toLowerCase();
        const lawRequestStatus = String(
            payload.law_request_status || "",
        ).toLowerCase();
        const remark = typeof payload.remark === "string" ? payload.remark : "";

        const statusField = row.querySelector(".js-admin-teacher-status");
        if (statusField && attendanceStatus) {
            const option = statusField.querySelector(
                `option[value="${attendanceStatus}"]`,
            );
            if (option) {
                statusField.value = attendanceStatus;
                syncStatusBoxes(statusField);
            }
        }

        const remarkField = row.querySelector('input[name*="[remark]"]');
        if (remarkField && remark !== "") {
            remarkField.value = remark;
        }

        const attendanceIndicator = row.querySelector(
            "[data-attendance-indicator]",
        );
        if (attendanceIndicator) {
            attendanceIndicator.textContent =
                labelForAttendanceStatus(attendanceStatus);
            applyBadgeClasses(attendanceIndicator, attendanceStatus || "empty");
        }

        const lawBadge = row.querySelector("[data-law-request-badge]");
        if (lawBadge && lawRequestStatus) {
            lawBadge.textContent = labelForLawRequestStatus(lawRequestStatus);
            applyBadgeClasses(lawBadge, lawRequestStatus);
        }

        const actionsWrap = row.querySelector("[data-law-request-actions]");
        if (actionsWrap) {
            actionsWrap.remove();
        }

        let lawState = row.querySelector("[data-law-request-state]");
        if (!lawState) {
            const lawCell = row.children[1];
            const lawContent = lawCell ? lawCell.querySelector(".space-y-1.5") : null;
            if (lawContent) {
                lawState = document.createElement("div");
                lawState.setAttribute("data-law-request-state", "");
                lawState.className = "pt-1 text-[11px] font-semibold";
                lawContent.appendChild(lawState);
            }
        }

        if (lawState) {
            if (lawRequestStatus === "approved") {
                lawState.className =
                    "pt-1 text-[11px] font-semibold text-emerald-700";
                lawState.textContent = "Approved for attendance";
            } else if (lawRequestStatus === "rejected") {
                lawState.className =
                    "pt-1 text-[11px] font-semibold text-red-700";
                lawState.textContent = "Cancelled and marked absent";
            }
        }
    };

    document.querySelectorAll(".js-admin-status-box").forEach((button) => {
        button.addEventListener("click", () => {
            if (button.disabled) {
                return;
            }

            const row = button.closest(".js-admin-teacher-row");
            if (!row) {
                return;
            }

            const field = row.querySelector(".js-admin-teacher-status");
            if (!field || field.disabled) {
                return;
            }

            const status = String(
                button.dataset.statusOption || "",
            ).toLowerCase();
            const option = field.querySelector(`option[value="${status}"]`);
            if (!option) {
                return;
            }

            field.value = status;
            syncStatusBoxes(field);
        });
    });

    document.querySelectorAll("[data-set-all-status]").forEach((button) => {
        button.addEventListener("click", () => {
            const status = String(
                button.getAttribute("data-set-all-status") || "",
            );
            if (!status) {
                return;
            }

            statusFields.forEach((field) => {
                if (field.disabled) {
                    return;
                }

                const option = field.querySelector(`option[value="${status}"]`);
                if (option) {
                    field.value = status;
                    syncStatusBoxes(field);
                }
            });
        });
    });

    const alertQueue = [];

    if (pageData.validationErrors.length > 0) {
        alertQueue.push({
            icon: "error",
            title: "Validation Error",
            text: String(pageData.validationErrors[0] || ""),
        });
    }

    if (pageData.flash.error) {
        alertQueue.push({
            icon: "error",
            title: "Error",
            text: pageData.flash.error,
        });
    } else if (pageData.flash.warning) {
        alertQueue.push({
            icon: "warning",
            title: "Warning",
            text: pageData.flash.warning,
        });
    } else if (pageData.flash.success) {
        alertQueue.push({
            icon: "success",
            title: "Success",
            text: pageData.flash.success,
            timer: 2200,
            showConfirmButton: false,
        });
    }

    if (alertQueue.length > 0) {
        document.querySelectorAll(".js-inline-flash").forEach((element) => {
            element.classList.add("hidden");
        });

        if (hasSwal) {
            alertQueue.reduce((chain, config) => {
                return chain.then(() => window.Swal.fire(config));
            }, Promise.resolve());
        } else {
            alertQueue.forEach((config) => {
                showMessage(
                    config.icon || "info",
                    config.title || "Message",
                    config.text || "",
                );
            });
        }
    }

    if (submitForm) {
        submitForm.addEventListener("submit", (event) => {
            if (submitForm.dataset.confirmed === "1") {
                submitForm.dataset.confirmed = "0";
                return;
            }

            event.preventDefault();
            const submitter = event.submitter;
            const lawAction = String(
                submitter?.dataset?.lawAction || "",
            ).toLowerCase();
            const confirmOptions =
                lawAction === "approve"
                    ? {
                          title: "Approve law request?",
                          text: "This will approve the law request and mark the teacher as excused for this attendance date.",
                          confirmButtonText: "Approve",
                          cancelButtonText: "Back",
                          confirmButtonColor: "#059669",
                      }
                    : lawAction === "reject"
                      ? {
                            title: "Cancel law request?",
                            text: "This will cancel the law request and mark the teacher as absent for this attendance date.",
                            confirmButtonText: "Cancel Request",
                            cancelButtonText: "Back",
                            confirmButtonColor: "#dc2626",
                        }
                      : {
                            title: "Save teacher attendance?",
                            text: "This will submit the selected attendance records for the current date.",
                            confirmButtonText: "Yes, submit",
                            cancelButtonText: "Cancel",
                        };

            showConfirm(confirmOptions).then((confirmed) => {
                if (!confirmed) {
                    return;
                }

                if (lawAction === "approve" || lawAction === "reject") {
                    const formData = new FormData(submitForm);
                    const row = submitter?.closest(".js-admin-teacher-row");
                    const originalDisabled = submitter.disabled;
                    submitter.disabled = true;

                    fetch(submitter.formAction, {
                        method: "POST",
                        headers: {
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        body: formData,
                    })
                        .then(async (response) => {
                            const payload = await response
                                .json()
                                .catch(() => ({}));
                            if (!response.ok || payload.ok === false) {
                                throw new Error(
                                    String(
                                        payload.message ||
                                            "Unable to update law request.",
                                    ),
                                );
                            }

                            updateTeacherRowFromLawAction(row, payload);
                            showMessage(
                                payload.flash_type || "success",
                                payload.flash_type === "warning"
                                    ? "Warning"
                                    : "Success",
                                String(
                                    payload.message || "Updated successfully.",
                                ),
                                {
                                    timer: 1800,
                                    showConfirmButton: false,
                                },
                            );
                        })
                        .catch((error) => {
                            showMessage(
                                "error",
                                "Error",
                                error.message ||
                                    "Unable to update law request.",
                            );
                        })
                        .finally(() => {
                            submitter.disabled = originalDisabled;
                        });

                    return;
                }

                submitForm.dataset.confirmed = "1";
                submitForm.submit();
            });
        });
    }

    syncAllStatusBoxes();
});
