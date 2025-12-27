function showDeleteModal(taskId, taskTitle) {
    document.getElementById('deleteTaskTitle').textContent = '"' + taskTitle + '"';
    document.getElementById('confirmDeleteBtn').href = 'tasks/action.php?delete=' + taskId;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function validateTask() {
    const title = document.querySelector('input[name="title"]');
    if (!title) return true;

    if (title.value.trim().length < 3) {
        alert('Judul minimal 3 karakter');
        title.focus();
        return false;
    }
    return true;
}

// Engaging animations and interactions
document.addEventListener('DOMContentLoaded', function() {
    // Temporarily disable loading animation to fix delete functionality
    // Will re-enable after confirming delete works
    // const buttons = document.querySelectorAll('.btn:not([data-bs-toggle]):not([data-bs-dismiss]):not(#confirmDeleteBtn):not([onclick*="showDeleteModal"])');
    // buttons.forEach(button => {
    //     button.addEventListener('click', function() {
    //         if (this.tagName === 'A' && this.getAttribute('href') !== '#') {
    //             this.classList.add('btn-loading');
    //             setTimeout(() => {
    //                 this.classList.remove('btn-loading');
    //             }, 1000);
    //         }
    //     });
    // });

    // Add ripple effect to buttons for tactile feedback
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple-effect');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add scroll animations for dynamic loading
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observe task cards for scroll animations
    document.querySelectorAll('.task-card').forEach(card => {
        observer.observe(card);
    });

    // Add smooth scrolling for better navigation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
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

    // Add interactive hover effects for task cards
    document.querySelectorAll('.task-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
            this.style.boxShadow = '0 12px 30px rgba(0, 0, 0, 0.2)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });

    // Add progress bar animation on page load
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });

    // Add typing effect to main headings
    const headings = document.querySelectorAll('h2, h3');
    headings.forEach(heading => {
        const text = heading.textContent;
        heading.textContent = '';
        heading.style.borderRight = '2px solid #667eea';

        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                heading.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            } else {
                heading.style.borderRight = 'none';
            }
        };

        // Start typing animation after a delay
        setTimeout(typeWriter, 800);
    });

    // Add celebration effect for completed tasks
    const completedBadges = document.querySelectorAll('.badge.bg-success');
    completedBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            createConfetti(this);
        });
    });
});

// Create confetti effect for celebrations
function createConfetti(element) {
    for (let i = 0; i < 15; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.animationDelay = Math.random() * 1 + 's';
        confetti.style.backgroundColor = ['#28a745', '#20c997', '#17a2b8', '#ffc107', '#fd7e14'][Math.floor(Math.random() * 5)];

        element.parentNode.appendChild(confetti);

        setTimeout(() => {
            confetti.remove();
        }, 2500);
    }
}

// Add engaging CSS effects
const style = document.createElement('style');
style.textContent = `
    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.4);
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
        z-index: 1;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .animate-in {
        animation: fadeInUp 0.8s ease-out forwards;
    }

    .confetti {
        position: absolute;
        width: 6px;
        height: 6px;
        animation: confetti-fall 2.5s linear forwards;
        pointer-events: none;
        z-index: 10;
    }

    @keyframes confetti-fall {
        0% {
            transform: translateY(-10px) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }

    .task-card:hover .card-title {
        color: #667eea;
        transition: color 0.3s ease;
    }

    .progress-bar:hover {
        transform: scaleY(1.1);
        transition: transform 0.3s ease;
    }

    /* Add floating animation to badges */
    .badge {
        animation: float 4s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-5px);
        }
    }
`;
document.head.appendChild(style);
