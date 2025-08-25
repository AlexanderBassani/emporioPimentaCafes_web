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
// createCoffeeBeans();

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

// Initialize EmailJS
(function () {
    emailjs.init('VHw9DtObStpCFbRMS'); // Substitua pela sua chave pública do EmailJS
})();

// Contact Form Functionality with EmailJS
document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Get form data
            const name = this.name.value.trim();
            const email = this.email.value.trim();
            const phone = this.phone.value.trim() || 'Não informado';
            const message = this.message.value.trim();

            // Validate required fields
            if (!name || !email || !message) {
                showFormMessage('Por favor, preencha todos os campos obrigatórios.', 'error');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showFormMessage('Por favor, insira um e-mail válido.', 'error');
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            submitBtn.disabled = true;

            // Prepare template parameters for EmailJS
            const templateParams = {
                from_name: name,
                from_email: email,
                phone: phone,
                message: message,
                to_email: 'alexanderba09@gmail.com' // Email que receberá as mensagens
            };

            // Send email using EmailJS
            emailjs.send('service_y4ktfg3', 'template_msor0x5', templateParams)
                .then(function (response) {
                    console.log('Email enviado com sucesso:', response);

                    // Reset form
                    contactForm.reset();

                    // Show success message
                    showFormMessage('Mensagem enviada com sucesso! Entraremos em contato em breve.', 'success');

                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, function (error) {
                    console.error('Erro ao enviar email:', error);

                    // Show error message
                    showFormMessage('Erro ao enviar mensagem. Tente novamente mais tarde.', 'error');

                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    }
});


// Show form messages
function showFormMessage(message, type) {
    // Remove existing message
    const existingMessage = document.querySelector('.form-message');
    if (existingMessage) {
        existingMessage.remove();
    }

    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `form-message ${type}`;
    messageDiv.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
        ${message}
    `;

    // Insert message before form
    const form = document.getElementById('contactForm');
    form.parentNode.insertBefore(messageDiv, form);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}

// Phone number formatting
document.getElementById('phone').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');

    if (value.length >= 11) {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (value.length >= 7) {
        value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    }

    e.target.value = value;
});