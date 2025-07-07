<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Document</title>
</head>

<body class="bg-neutral-50">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="text-2xl font-bold text-indigo-600">Engage<span class="text-neutral-800">English</span></a>
            <div class="hidden md:flex items-center space-x-6">
                <a href="#features" class="text-neutral-600 hover:text-indigo-600 transition duration-300">Features</a>
                <a href="#testimonials" class="text-neutral-600 hover:text-indigo-600 transition duration-300">Testimonials</a>
                <a href="#contact" class="text-neutral-600 hover:text-indigo-600 transition duration-300">Contact</a>
            </div>
            <a href="{{ route('login') }}" class="hidden md:block bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition duration-300">Get Started</a>
            <button class="md:hidden" id="mobile-menu-button">
                <i class="fas fa-bars text-2xl text-neutral-700"></i>
            </button>
        </nav>
        <!-- Mobile Menu -->
        <div class="hidden md:hidden" id="mobile-menu">
            <a href="#features" class="block py-2 px-4 text-sm hover:bg-neutral-100">Features</a>
            <a href="#testimonials" class="block py-2 px-4 text-sm hover:bg-neutral-100">Testimonials</a>
            <a href="#contact" class="block py-2 px-4 text-sm hover:bg-neutral-100">Contact</a>
            <a href="#" class="block py-2 px-4 text-sm bg-indigo-500 text-white text-center rounded-b-lg hover:bg-indigo-600">Get Started</a>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="py-20 md:py-32">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-neutral-800 leading-tight">
                Unlock Your English Fluency
            </h1>
            <p class="mt-4 text-lg md:text-xl text-neutral-600 max-w-2xl mx-auto">
                Join thousands of learners who are mastering English with our interactive lessons, personalized feedback, and supportive community.
            </p>
            <div class="mt-8 flex justify-center space-x-4">
                <a href="#" class="bg-indigo-600 text-white py-3 px-8 rounded-lg text-lg font-medium hover:bg-indigo-700 transition duration-300">Start Your Free Trial</a>
                <a href="#" class="bg-neutral-200 text-neutral-800 py-3 px-8 rounded-lg text-lg font-medium hover:bg-neutral-300 transition duration-300">How it Works</a>
            </div>
            <div class="mt-16">
                <img src="https://placehold.co/800x400/E9D5FF/6D28D9?text=Interactive+Learning+Platform" alt="Learning Platform Screenshot" class="mx-auto rounded-lg shadow-2xl">
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-neutral-800 mb-12">Why Choose EngageEnglish?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center p-8 bg-neutral-50 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                    <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-comments text-3xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-neutral-800 mb-2">Interactive Lessons</h3>
                    <p class="text-neutral-600">Engage in real conversations with our AI tutors and get instant feedback on your pronunciation and grammar.</p>
                </div>
                <!-- Feature 2 -->
                <div class="text-center p-8 bg-neutral-50 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                    <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-friends text-3xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-neutral-800 mb-2">Supportive Community</h3>
                    <p class="text-neutral-600">Connect with fellow learners from around the world, practice together, and stay motivated on your journey.</p>
                </div>
                <!-- Feature 3 -->
                <div class="text-center p-8 bg-neutral-50 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                    <div class="bg-indigo-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-3xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-neutral-800 mb-2">Personalized Progress</h3>
                    <p class="text-neutral-600">Track your learning progress, identify areas for improvement, and get a customized study plan tailored to your needs.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-20 bg-neutral-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-neutral-800 mb-12">What Our Learners Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <p class="text-neutral-600 mb-4">"EngageEnglish has been a game-changer for me. The interactive lessons are so much fun, and I feel more confident speaking English than ever before."</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full mr-4" src="https://i.pravatar.cc/150?img=1" alt="Avatar of Maria S.">
                        <div>
                            <p class="font-bold text-neutral-800">Maria S.</p>
                            <p class="text-sm text-neutral-500">Student from Spain</p>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 2 -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <p class="text-neutral-600 mb-4">"The community is amazing! I've made friends with people from all over the world and we practice speaking together every day."</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full mr-4" src="https://i.pravatar.cc/150?img=2" alt="Avatar of Kenji T.">
                        <div>
                            <p class="font-bold text-neutral-800">Kenji T.</p>
                            <p class="text-sm text-neutral-500">Professional from Japan</p>
                        </div>
                    </div>
                </div>
                <!-- Testimonial 3 -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <p class="text-neutral-600 mb-4">"I love how the platform tracks my progress. It helps me stay motivated and focus on what I need to improve."</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full mr-4" src="https://i.pravatar.cc/150?img=3" alt="Avatar of Fatima A.">
                        <div>
                            <p class="font-bold text-neutral-800">Fatima A.</p>
                            <p class="text-sm text-neutral-500">Traveler from UAE</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="contact" class="bg-indigo-700 text-white">
        <div class="container mx-auto px-6 py-20 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Start Learning?</h2>
            <p class="text-indigo-200 text-lg mb-8 max-w-2xl mx-auto">Join EngageEnglish today and take the first step towards fluency. It's fast, fun, and effective.</p>
            <a href="#" class="bg-white text-indigo-600 py-3 px-8 rounded-lg text-lg font-medium hover:bg-neutral-100 transition duration-300">Sign Up For Free</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-neutral-800 text-white">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <a href="#" class="text-2xl font-bold">Engage<span class="text-indigo-400">English</span></a>
                    <p class="text-neutral-400 mt-2">© 2024 EngageEnglish. All rights reserved.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="text-neutral-400 hover:text-white"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-neutral-400 hover:text-white"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-neutral-400 hover:text-white"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-neutral-400 hover:text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>

</html>