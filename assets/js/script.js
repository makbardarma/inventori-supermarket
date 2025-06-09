// script.js

// Initialize AOS animation
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true
});

// Update copyright year
document.getElementById('year').textContent = new Date().getFullYear();

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Floating button animation
const floatingBtn = document.querySelector('.floating-btn');
floatingBtn.addEventListener('mouseenter', function() {
    this.style.animation = 'none';
});
floatingBtn.addEventListener('mouseleave', function() {
    this.style.animation = 'pulse 2s infinite';
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});