<?php

return [
    'actions' => [
        'dashboard' => 'Dashboard',
        'login' => 'Log In',
        'login_portal' => 'Log In to the Portal',
        'contact_admissions' => 'Contact Admissions',
        'open_dashboard' => 'Open Dashboard',
        'explore_programs' => 'Explore Programs',
        'apply_now' => 'Apply Now',
        'continue_dashboard' => 'Continue to Dashboard',
        'open_contact' => 'Open Contact Section',
        'send_message' => 'Send Message',
        'scroll_to_explore' => 'Scroll to Explore',
        'toggle_menu' => 'Toggle Menu',
    ],

    'hero' => [],

    'connected' => [
        'badge' => 'Connected Experience',
        'text' => 'Each section works together to give families a clearer, more confident understanding of the school and its values.',
    ],

    'token' => [
        'prompt_title' => 'Remember your homepage token?',
        'prompt_description' => 'We can store a small cookie so this browser remembers your homepage preference for the next visit.',
        'accept' => 'Remember Token',
        'dismiss' => 'Not Now',
        'success_title' => 'Token Saved',
        'success_description' => 'Your homepage token preference has been saved in this browser.',
        'close_label' => 'Close Token Message',
    ],

    'about' => [],

    'features' => [],

    'programs' => [],

    'facilities' => [],

    'admission' => [],

    'contact' => [
        'badge' => 'Contact Admissions',
        'title' => 'Send your inquiry directly from the homepage.',
        'description' => 'This contact form is simple, professional, and easy to use, allowing families to connect with the admissions team without difficulty.',
        'success' => 'Your message has been sent successfully. Our team will contact you shortly.',
        'form_error' => 'Please review the contact form and try again.',
        'form' => [
            'name' => 'Full Name',
            'name_placeholder' => 'Enter your full name',
            'phone' => 'Phone Number (Optional)',
            'phone_placeholder' => '+855 ...',
            'email' => 'Email Address',
            'email_placeholder' => 'you@example.com',
            'subject' => 'Subject',
            'subject_placeholder' => 'Question about admissions',
            'message' => 'Message',
            'message_placeholder' => 'Write your message here...',
        ],
    ],
    'chatbot' => [
        'chip' => 'Ask :schoolName',
        'toggle_label' => 'Open Chat Assistant',
        'assistant_label' => 'Assistant',
        'title' => 'School Information Assistant',
        'input_placeholder' => 'Ask a question...',
        'send' => 'Send',
        'thinking' => 'Typing...',
        'empty_question' => 'Please enter a question so I can assist you.',
        'fallback_answer' => 'I can help with admissions, academic programs, contact details, office hours, and platform features. Please ask about one of these topics.',
        'welcome' => 'Welcome to :schoolName. Ask me a question and I will be happy to help.',
        'quick_questions' => [
            'How do I apply for admission?',
            'What academic programs are available?',
            'What are your office hours?',
            'How can I contact the school?',
            'Can parents track student progress online?',
            'Can teachers manage attendance in the system?',
        ],
        'answers' => [
            [
                'keywords' => ['admission', 'apply', 'enroll', 'registration', 'register'],
                'answer' => 'You can apply through the Admissions section by submitting student information and the required documents. Our team will review the application and guide you through the next steps.',
            ],
            [
                'keywords' => ['program', 'primary', 'secondary', 'club', 'sports', 'curricular'],
                'answer' => ':schoolName offers Primary, Lower Secondary, Upper Secondary, and co-curricular programs, including clubs, arts, and sports.',
            ],
            [
                'keywords' => ['contact', 'phone', 'email', 'address', 'location'],
                'answer' => 'You can contact the admissions team through the website form, by phone at +855 XXX XXX XXX, or by email at admissions@schooli.edu. Our campus is located in Phnom Penh, Cambodia.',
            ],
            [
                'keywords' => ['hour', 'time', 'open', 'office', 'schedule'],
                'answer' => 'Office hours are Monday to Friday, from 8:00 AM to 5:00 PM.',
            ],
            [
                'keywords' => ['parent', 'progress', 'report', 'attendance'],
                'answer' => 'Yes. Parents can monitor attendance, announcements, and academic progress through the platform.',
            ],
            [
                'keywords' => ['teacher', 'attendance', 'manage'],
                'answer' => 'Yes. Teachers can record attendance daily and review attendance history and reports within the system.',
            ],
            [
                'keywords' => ['role', 'permission', 'dashboard', 'admin', 'student'],
                'answer' => 'The platform uses role-based access, providing separate dashboards and permissions for each user group.',
            ],
            [
                'keywords' => ['hello', 'hi', 'hey'],
                'answer' => 'Hello. You can ask me about admissions, academic programs, office hours, contact details, or student progress tracking.',
            ],
        ],
    ],

    'validation' => [
        'required' => 'The :attribute field is required.',
        'string' => 'The :attribute field must contain valid text.',
        'email' => 'The :attribute field must be a valid email address.',
        'max_string' => 'The :attribute field may not be greater than :max characters.',
        'min_string' => 'The :attribute field must be at least :min characters.',
    ],

    'back_to_top' => 'Back to Top',
];
