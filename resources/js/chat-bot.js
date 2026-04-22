document.addEventListener("DOMContentLoaded", () => {
    const prefersReducedMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
    );

    const chatbot = document.getElementById("school-chatbot");
    if (!chatbot) {
        return;
    }

    const schoolName = (chatbot.dataset.schoolName || "Schooli").trim();
    const toggle = document.getElementById("chatbot-toggle");
    const panel = document.getElementById("chatbot-panel");
    const form = document.getElementById("chatbot-form");
    const input = document.getElementById("chatbot-input");
    const messages = document.getElementById("chatbot-messages");
    const quickWrap = document.getElementById("chatbot-quick-questions");

    const parseJson = (value, fallback) => {
        if (!value) {
            return fallback;
        }

        try {
            return JSON.parse(value);
        } catch {
            return fallback;
        }
    };

    const animateNode = (node, frames, options) => {
        if (
            prefersReducedMotion.matches ||
            !(node instanceof HTMLElement) ||
            typeof node.animate !== "function"
        ) {
            return null;
        }

        return node.animate(frames, options);
    };

    const defaultQuickQuestions = [
        "How can I apply for admission?",
        "What programs are available?",
        "What are the office hours?",
        "How do I contact the school?",
        "Can parents track student progress online?",
        "Do teachers manage attendance in the system?",
    ];

    const defaultAnswers = [
        {
            keywords: [
                "admission",
                "apply",
                "enroll",
                "registration",
                "register",
            ],
            answer: `You can apply online from the Admission section, then submit student details and required documents. The school team reviews your request and confirms enrollment.`,
        },
        {
            keywords: [
                "program",
                "primary",
                "secondary",
                "club",
                "sports",
                "curricular",
            ],
            answer: `${schoolName} offers Primary, Lower Secondary, Upper Secondary, and Co-Curricular programs including clubs, arts, and sports.`,
        },
        {
            keywords: ["contact", "phone", "email", "address", "location"],
            answer: `Contact admissions by the website contact form, phone (+855 XXX XXX XXX), or email (admissions@schooli.edu). Campus location is Phnom Penh, Cambodia.`,
        },
        {
            keywords: ["hour", "time", "open", "office", "schedule"],
            answer: `Office hours are Monday to Friday, 8:00 AM to 5:00 PM.`,
        },
        {
            keywords: ["parent", "progress", "report", "attendance"],
            answer: `Yes. Parents can track attendance, notices, and academic progress through the platform.`,
        },
        {
            keywords: ["teacher", "attendance", "manage"],
            answer: `Yes. Teachers can record daily attendance and view attendance history reports in the system.`,
        },
        {
            keywords: ["role", "permission", "dashboard", "admin", "student"],
            answer: `The platform uses role-based access with separate dashboards for admin, teachers, and students.`,
        },
        {
            keywords: ["hello", "hi", "hey"],
            answer: `Hello! Ask me about admission, programs, office hours, contact details, or student tracking.`,
        },
    ];

    const quickQuestions = parseJson(
        chatbot.dataset.quickQuestions,
        defaultQuickQuestions,
    );
    const answers = parseJson(chatbot.dataset.answers, defaultAnswers);
    const thinkingText = chatbot.dataset.thinking || "Typing...";
    const emptyQuestionText =
        chatbot.dataset.emptyQuestion || "Please type a question so I can help you.";
    const fallbackAnswerText =
        chatbot.dataset.fallbackAnswer ||
        "I can help with admission, programs, contact details, office hours, and platform features. Please ask one of those topics.";
    const welcomeText =
        chatbot.dataset.welcome ||
        `Hi, welcome to ${schoolName}. Ask me a question and I will answer automatically.`;

    const normalize = (text) =>
        text
            .toLowerCase()
            .replace(/[^\p{L}\p{N}\s]/gu, " ")
            .trim();

    const addMessage = (role, text) => {
        const row = document.createElement("div");
        row.className =
            role === "user" ? "flex justify-end" : "flex justify-start";

        const bubble = document.createElement("div");
        bubble.className =
            role === "user"
                ? "max-w-[85%] rounded-2xl rounded-br-sm bg-cyan-600 px-3 py-2 text-sm text-white"
                : "max-w-[85%] rounded-2xl rounded-bl-sm bg-white px-3 py-2 text-sm text-slate-700 ring-1 ring-slate-200";
        bubble.textContent = text;

        row.appendChild(bubble);
        messages.appendChild(row);
        messages.scrollTop = messages.scrollHeight;

        animateNode(
            row,
            [
                { opacity: 0, transform: "translateY(12px) scale(0.98)" },
                { opacity: 1, transform: "translateY(0) scale(1)" },
            ],
            {
                duration: 320,
                easing: "cubic-bezier(0.22, 1, 0.36, 1)",
                fill: "both",
            },
        );

        return bubble;
    };

    const pickAnswer = (question) => {
        const clean = normalize(question);
        if (!clean) {
            return emptyQuestionText;
        }

        let best = null;
        let bestScore = 0;
        for (const item of answers) {
            let score = 0;
            for (const key of item.keywords) {
                if (clean.includes(key)) {
                    score += 1;
                }
            }
            if (score > bestScore) {
                best = item;
                bestScore = score;
            }
        }

        if (best && bestScore > 0) {
            return best.answer;
        }

        return fallbackAnswerText;
    };

    const renderQuickQuestions = () => {
        quickWrap.innerHTML = "";
        const shuffled = [...quickQuestions]
            .sort(() => Math.random() - 0.5)
            .slice(0, 3);
        shuffled.forEach((q) => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className =
                "rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-cyan-300 hover:text-cyan-700";
            btn.textContent = q;
            btn.addEventListener("click", () => sendQuestion(q));
            quickWrap.appendChild(btn);
        });
    };

    const sendQuestion = (question) => {
        const text = (question || "").trim();
        if (!text) {
            return;
        }

        addMessage("user", text);
        const thinkingBubble = addMessage("bot", thinkingText);
        setTimeout(() => {
            thinkingBubble.textContent = pickAnswer(text);
            messages.scrollTop = messages.scrollHeight;
        }, 380);
        renderQuickQuestions();
        input.value = "";
    };

    let panelClosing = false;

    const openPanel = () => {
        panelClosing = false;
        panel.classList.remove("hidden");
        toggle.classList.add("is-open");
        toggle.setAttribute("aria-expanded", "true");

        animateNode(
            panel,
            [
                { opacity: 0, transform: "translateY(16px) scale(0.96)" },
                { opacity: 1, transform: "translateY(0) scale(1)" },
            ],
            {
                duration: 260,
                easing: "cubic-bezier(0.22, 1, 0.36, 1)",
                fill: "both",
            },
        );

        requestAnimationFrame(() => input.focus());
    };

    const closePanel = () => {
        if (panel.classList.contains("hidden") || panelClosing) {
            return;
        }

        panelClosing = true;
        toggle.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");

        const animation = animateNode(
            panel,
            [
                { opacity: 1, transform: "translateY(0) scale(1)" },
                { opacity: 0, transform: "translateY(14px) scale(0.96)" },
            ],
            {
                duration: 220,
                easing: "ease-in",
                fill: "both",
            },
        );

        if (!animation) {
            panel.classList.add("hidden");
            panelClosing = false;
            return;
        }

        animation.addEventListener("finish", () => {
            panel.classList.add("hidden");
            panelClosing = false;
        });
    };

    toggle.addEventListener("click", () => {
        const isOpen = !panel.classList.contains("hidden");
        if (isOpen) {
            closePanel();
            return;
        }

        openPanel();
    });

    form.addEventListener("submit", (event) => {
        event.preventDefault();
        sendQuestion(input.value);
    });

    addMessage("bot", welcomeText);
    renderQuickQuestions();
});
