document.addEventListener("DOMContentLoaded", () => {
    requestAnimationFrame(() => {
        document.body.classList.add("page-ready");
    });

    document.body.classList.add("reveal-ready");
    const items = document.querySelectorAll("[data-reveal]");
    const firstFold = document.querySelectorAll("[data-first][data-reveal]");
    firstFold.forEach((el) => el.classList.add("is-visible"));
    if (!("IntersectionObserver" in window)) {
        items.forEach((el) => el.classList.add("is-visible"));
        return;
    }
    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    io.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.14, rootMargin: "0px 0px -6% 0px" },
    );

    items.forEach((el) => io.observe(el));

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

    const quickQuestions = [
        "How can I apply for admission?",
        "What programs are available?",
        "What are the office hours?",
        "How do I contact the school?",
        "Can parents track student progress online?",
        "Do teachers manage attendance in the system?",
    ];

    const answers = [
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

    const normalize = (text) =>
        text
            .toLowerCase()
            .replace(/[^a-z0-9\s]/g, " ")
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
        return bubble;
    };

    const pickAnswer = (question) => {
        const clean = normalize(question);
        if (!clean) {
            return "Please type a question so I can help you.";
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

        return `I can help with admission, programs, contact details, office hours, and platform features. Please ask one of those topics.`;
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
        const thinkingBubble = addMessage("bot", "Typing...");
        setTimeout(() => {
            thinkingBubble.textContent = pickAnswer(text);
            messages.scrollTop = messages.scrollHeight;
        }, 380);
        renderQuickQuestions();
        input.value = "";
    };

    toggle.addEventListener("click", () => {
        const isOpen = !panel.classList.contains("hidden");
        panel.classList.toggle("hidden", isOpen);
        toggle.setAttribute("aria-expanded", String(!isOpen));
        if (!isOpen) {
            input.focus();
        }
    });

    form.addEventListener("submit", (event) => {
        event.preventDefault();
        sendQuestion(input.value);
    });

    addMessage(
        "bot",
        `Hi, welcome to ${schoolName}. Ask me a question and I will answer automatically.`,
    );
    renderQuickQuestions();
});
