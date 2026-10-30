<?php

return [

    'brand' => [
        'name' => 'LearnSphere ID',
        'copyright' => '© '.date('Y').' Auronsoft. All rights reserved.',
    ],

    'auth' => [
        'login_title' => 'Sign in',
        'login_subtitle' => 'to continue to :platform',
        'platform' => 'LearnSphere LMS',

        'email' => 'Email',
        'email_placeholder' => 'student@example.com',
        'email_invalid' => 'Please enter a valid email address.',
        'learn_sphere_id' => 'LearnSphere ID',
        'learn_sphere_id_placeholder' => 'LS-STU-1001',

        'password' => 'Password',
        'password_placeholder' => 'Enter your password',
        'forgot_password' => 'Forgot password?',

        'remember_me' => 'Keep me signed in',
        'login_button' => 'Sign in',

        'incognito_notice' => 'If this is not your device, sign in using',
        'incognito_highlight' => 'private browsing mode',
        'incognito_suffix' => 'to protect your account.',

        'account_suspended' => 'Your account has been suspended. Please contact the administrator.',
        'no_permission' => 'You do not have permission to access this area.',
    ],

    'registration' => [
        'title' => 'Create your account',
        'subtitle' => 'Join LearnSphere to access courses, classrooms, and learning tools.',
        'name' => 'Full Name',
        'email' => 'Email Address',
        'email_taken' => 'An account already exists with this email address.',
        'role' => 'I am registering as a',
        'student' => 'Student',
        'teacher' => 'Teacher',
        'password' => 'Password',
        'password_confirmation' => 'Confirm Password',
        'create_account' => 'Create Account',
        'already_account' => 'Already have an account?',
        'sign_in' => 'Sign in',
        'no_account' => "Don't have an account?",
        'register_here' => 'Register here',
        'welcome_title' => 'Your learning journey starts here',
        'welcome_description' => 'Create your account to connect with courses, teachers, and your learning community.',
        'complete_title' => 'Account created',
        'complete_description' => 'Your account is ready. This is your unique LearnSphere ID:',
        'your_id' => 'Your LearnSphere ID',
        'save_id_notice' => 'Save this ID somewhere safe. You will need it every time you sign in.',
        'password_notice' => 'Sign in with this ID and the password you just created. Your password is never displayed or emailed.',
    ],

    'sso' => [
        'divider' => 'Or continue with SSO',

        'google' => 'Sign in with Google',
        'microsoft' => 'Sign in with Microsoft',
        'apple' => 'Sign in with Apple',
        'saml' => 'Sign in with SAML',
    ],

    'support' => [
        'need_help' => 'Need help?',
        'contact_admin' => 'Contact administrator',
    ],

    'dashboard' => [
        'employees_active' => 'Active users',
        'today_up' => '↑ +12 today',

        'system_status' => 'System status',
        'system_online' => 'Running smoothly',

        'today_approval' => 'Today submissions',
        'approved' => '✓ 24 approved',
        'pending' => '⏳ 6 pending',

        'salary_this_month' => 'Progress this month',
        'salary_processed' => '↑ Completed',

        'recruitment' => 'Question bank',
        'candidates' => '12 tests',
        'offers' => '3 sets',

        'attendance_today' => 'Today activity',
        'attendance_ontime' => '● On time',

        'training' => 'Practice courses',
        'courses' => '8 courses',
        'training_running' => 'In progress',
    ],

    'onboarding' => [
        'label' => 'Introducing the LearnSphere platform',
        'navigation' => 'Onboarding navigation',
        'go_to' => 'Go to slide :slide',
        'live' => 'Live',
        'duration' => ':minutes min',
        'schedule' => [
            'eyebrow' => 'Smart scheduling',
            'title' => 'Plan classes and exams with confidence',
            'description' => 'Keep classes, exams and reminders in one clear schedule that stays up to date for everyone.',
            'card_title' => 'Today’s schedule',
            'card_meta' => 'Monday, the 18th',
            'note_one' => 'English class 11A',
            'note_one_meta' => '08:00 · Online classroom',
            'note_two' => 'Upcoming assessment',
            'note_two_meta' => '2 days left to prepare',
        ],
        'classroom' => [
            'eyebrow' => 'Connected classroom',
            'title' => 'Teach and learn in one shared space',
            'description' => 'Resources, assignments, discussions and feedback stay connected so every class runs smoothly.',
            'card_title' => 'My classroom',
            'card_meta' => '24 active learners',
            'note_one' => 'Assignment published',
            'note_one_meta' => 'Due today at 20:00',
            'note_two' => 'New discussion',
            'note_two_meta' => '8 classroom replies',
            'activity_label' => 'TODAY’S ACTIVITY',
            'activity_total' => '660 learning minutes',
            'course_one' => 'Conversational English',
            'course_one_meta' => '4 lessons completed',
            'course_two' => 'Skill practice',
            'course_two_meta' => '92% average score',
            'stat_label' => 'YOUR CLASSROOM',
            'stat_unit' => 'activities',
            'stat_change' => '+12% this week',
            'notice_title' => 'Your most productive week was found',
            'notice_meta' => '8 activities completed',
        ],
        'progress' => [
            'eyebrow' => 'Actionable progress',
            'title' => 'Turn learning data into better outcomes',
            'description' => 'Track results in real time, identify skills that need attention and support every learner at the right moment.',
            'card_title' => 'This week’s progress',
            'card_meta' => 'Updated in real time',
            'note_one' => 'Goals completed',
            'note_one_meta' => '12 of 14 activities',
            'note_two' => 'Positive momentum',
            'note_two_meta' => 'Up 8% from last week',
            'journey_label' => 'YOUR JOURNEY',
            'journey_title' => 'This week’s learning path',
            'task_one' => 'Complete the foundation lesson',
            'task_one_meta' => 'Completed today',
            'task_two' => 'Take the next skill challenge',
            'task_two_meta' => 'Ready to begin',
            'recommendation' => 'RECOMMENDED FOR YOU',
            'course_title' => 'Modern communication skills',
            'reward_title' => 'You earned a new reward',
            'reward_meta' => 'Completed a 7-day learning streak',
        ],
    ],

    'hero' => [
        'title_line_1' => 'Modern',
        'title_line_2' => 'exam practice',
        'title_highlight' => 'platform',

        'description' => 'Smart testing system with automatic grading and learning progress tracking.',

        'businesses' => 'Users',
        'uptime' => 'Uptime',
        'employees' => 'Students',
    ],

    'footer' => [
        'protected_by' => 'Protected by',
        'terms' => 'Terms',
        'privacy' => 'Privacy',
        'help' => 'Help',
    ],

    'title' => 'Forgot Password',

    'steps' => [

        'email' => [
            'title' => 'Forgot password?',
            'description' => 'Enter your email to receive a password reset OTP.',
            'send_otp' => 'Send OTP',
            'email_required' => 'Please enter your email address.',
            'otp_sent' => 'The OTP has been sent to your email.',
        ],

        'otp' => [
            'title' => 'Enter OTP',
            'description' => 'We sent a 6-digit code to your email',
            'not_received' => 'Didn’t receive the code?',
            'resend' => 'Resend',
            'confirm' => 'Confirm',
            'otp_required' => 'Please enter all 6 digits.',
            'otp_invalid' => 'Invalid OTP code.',
            'otp_success' => 'OTP verified successfully.',
        ],

        'reset' => [
            'title' => 'Reset Password',
            'description' => 'Set a new password to continue.',

            'new_password' => 'New Password',
            'new_password_placeholder' => 'Minimum 8 characters',

            'confirm_password' => 'Confirm Password',
            'confirm_password_placeholder' => 'Re-enter password',

            'submit' => 'Reset Password',

            'password_min' => 'Password must be at least 8 characters.',
            'password_not_match' => 'Password confirmation does not match.',

            'session_expired' => 'Your session has expired. Please try again.',
            'reset_success' => 'Password reset successfully. Redirecting to login...',
        ],

    ],

    'navigation' => [
        'back_login' => 'Back to login',
        'back' => 'Back',
    ],

    'toast' => [
        'login_success' => 'Login successful! Welcome back.',
        'logout_success' => 'Logged out successfully. See you again!',
        'logging_out' => 'Logging out...',
    ],

    'confirm' => [
        'logout' => [
            'title' => 'Logout',
            'message' => 'Are you sure you want to logout?',
            'confirm_text' => 'Logout',
            'cancel_text' => 'Cancel',
        ],
    ],

    'validation' => [
        'credentials' => 'The LearnSphere ID or password is incorrect.',
        'throttle' => 'Too many attempts. Please try again in :seconds seconds.',
        'learn_sphere_id' => [
            'required' => 'Please enter your LearnSphere ID.',
        ],
        'email' => [
            'required' => 'Please enter your email address.',
            'email' => 'Please enter a valid email address.',
            'exists' => 'No account found with this email.',
        ],
        'password' => [
            'required' => 'Please enter your password.',
            'min' => 'Password must be at least :min characters.',
            'confirmed' => 'Password confirmation does not match.',
        ],
        'otp' => [
            'required' => 'Please enter the OTP code.',
            'size' => 'OTP code must be :size digits.',
        ],
        'type' => [
            'in' => 'Invalid verification type.',
        ],
    ],

    'processing' => [
        'label' => 'Authenticating your account',
        'processing' => 'Signing you in...',
        'success' => 'Signed in successfully!',
        'failed' => 'Sign-in unsuccessful',
        'returning' => 'Returning to the sign-in screen...',
    ],

    'mail' => [
        'otp_forgot_password_subject' => 'LearnSphere password reset code',
    ],
];
