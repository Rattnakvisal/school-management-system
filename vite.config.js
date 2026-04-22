import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/chat-bot.js',
                'resources/js/admin/classes.js',
                'resources/js/admin/students.js',
                'resources/js/admin/teacher.js',
                'resources/js/admin/settings.js',
                'resources/js/admin/contact.js',
                'resources/js/admin/subject.js',
                'resources/js/admin/time-studies.js',
                'resources/js/admin/teacher-attendance.js',
                'resources/js/Teacher/Attendence.js',
                'resources/js/Teacher/dashboard.js',
                'resources/js/Teacher/LawRequests.js',
                'resources/js/Teacher/schedule.js',
                'resources/js/Teacher/settings.js',
                'resources/js/Student/dashboard.js',
                'resources/js/Student/LawRequests.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
