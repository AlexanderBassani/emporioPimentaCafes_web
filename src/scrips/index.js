// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Animation on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'slideUp 1s ease-out forwards';
        }
    });
}, observerOptions);

// Observe elements for animation
document.querySelectorAll('.slide-up, .feature-item, .stat-item').forEach(el => {
    observer.observe(el);
});

// Interactive category hover effects
// document.querySelectorAll('.category').forEach(category => {
//     category.addEventListener('mouseenter', function () {
//         this.style.transform = 'translateY(-5px) scale(1.05)';
//     });

//     category.addEventListener('mouseleave', function () {
//         this.style.transform = 'translateY(-3px) scale(1)';
//     });
// });

// Contact function
function openContact() {
    const message = encodeURIComponent("Olá! Gostaria de saber mais sobre as oportunidades de patrocínio do Empório Pimentas & Cafés.");
    window.open(`https://wa.me/5527999999999?text=${message}`, '_blank');
}

// Dynamic coffee bean generation
// function createCoffeeBeans() {
//     setInterval(() => {
//         if (Math.random() < 0.3) {
//             const bean = document.createElement('div');
//             bean.className = 'coffee-bean';
//             bean.style.left = Math.random() * 100 + '%';
//             bean.style.animationDelay = '0s';
//             bean.style.animationDuration = (6 + Math.random() * 4) + 's';
//             document.body.appendChild(bean);

//             setTimeout(() => {
//                 if (bean.parentNode) {
//                     bean.parentNode.removeChild(bean);
//                 }
//             }, 10000);
//         }
//     }, 3000);
// }

// Start coffee bean animation
createCoffeeBeans();

// Add parallax effect to background
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const rate = scrolled * -0.5;
    const coffeeBg = document.querySelector('.coffee-bg');
    if (coffeeBg) {
        coffeeBg.style.transform = `translateY(${rate}px)`;
    }
});

// Stats counter animation
function animateStats() {
    const stats = document.querySelectorAll('.stat-number');
    stats.forEach(stat => {
        const targetText = stat.textContent;
        const targetNumber = parseInt(targetText.replace(/\D/g, ''));
        const suffix = targetText.includes('mil') ? ' mil' : '';
        const prefix = targetText.includes('+') ? '+' : '';

        let current = 0;
        const increment = targetNumber / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= targetNumber) {
                current = targetNumber;
                clearInterval(timer);
            }
            stat.textContent = prefix + Math.floor(current) + suffix;
        }, 50);
    });
}

// Trigger stats animation when section is visible
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateStats();
            statsObserver.unobserve(entry.target);
        }
    });
});

const statsSection = document.querySelector('.stats');
if (statsSection) {
    statsObserver.observe(statsSection);
}