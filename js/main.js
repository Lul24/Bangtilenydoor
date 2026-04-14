// ============================================
// BANGTILENYDOOR ACADEMY - MAIN JAVASCRIPT
// Complete with Backend API Integration
// ============================================

// API Base URL - Update this to match your server path
const API_BASE_URL = '/bangtilenydoor-academy/backend/api';

// ============================================
// MOBILE MENU TOGGLE
// ============================================
document.addEventListener('DOMContentLoaded', function() {
  const mobileToggle = document.getElementById('mobileToggle');
  const navLinks = document.getElementById('navLinks');

  if (mobileToggle && navLinks) {
    mobileToggle.addEventListener('click', function() {
      navLinks.classList.toggle('active');
    });
  }

  // Close mobile menu when clicking a link
  const allNavLinks = document.querySelectorAll('.nav-links a');
  allNavLinks.forEach(link => {
    link.addEventListener('click', function() {
      if (navLinks && navLinks.classList.contains('active')) {
        navLinks.classList.remove('active');
      }
    });
  });
});

// ============================================
// NEWSLETTER SUBSCRIPTION (Backend API)
// ============================================
function initNewsletter() {
  const newsForm = document.getElementById('newsletterForm');
  const newsMsg = document.getElementById('newsMsg');

  if (newsForm) {
    newsForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const emailInput = document.getElementById('newsEmail');
      if (!emailInput) return;
      
      const email = emailInput.value.trim();
      
      if (!email || !email.includes('@')) {
        if (newsMsg) {
          newsMsg.innerHTML = '<span style="color: #e67e22;">❌ Valid email required</span>';
          setTimeout(() => newsMsg.innerHTML = '', 3000);
        }
        return;
      }
      
      // Show loading state
      const submitBtn = newsForm.querySelector('button');
      const originalText = submitBtn.textContent;
      submitBtn.textContent = 'Subscribing...';
      submitBtn.disabled = true;
      
      try {
        const response = await fetch(`${API_BASE_URL}/subscribe.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        
        if (newsMsg) {
          if (data.status === 'success') {
            newsMsg.innerHTML = `<span style="color: #2ecc71;">✅ ${data.message}</span>`;
            emailInput.value = '';
          } else if (data.status === 'info') {
            newsMsg.innerHTML = `<span style="color: #f39c12;">ℹ️ ${data.message}</span>`;
          } else {
            newsMsg.innerHTML = `<span style="color: #e67e22;">❌ ${data.message}</span>`;
          }
        }
        setTimeout(() => { if (newsMsg) newsMsg.innerHTML = ''; }, 3000);
      } catch (error) {
        console.error('Newsletter error:', error);
        if (newsMsg) {
          newsMsg.innerHTML = '<span style="color: #e67e22;">❌ Network error. Please try again.</span>';
          setTimeout(() => newsMsg.innerHTML = '', 3000);
        }
      } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }
    });
  }
}

// ============================================
// HERO SLIDESHOW
// ============================================
function initHeroSlideshow() {
  let currentHeroSlide = 0;
  const heroSlides = document.querySelectorAll('.hero-slide');
  
  if (heroSlides.length > 1) {
    function nextHeroSlide() {
      heroSlides[currentHeroSlide].classList.remove('active');
      currentHeroSlide = (currentHeroSlide + 1) % heroSlides.length;
      heroSlides[currentHeroSlide].classList.add('active');
    }
    setInterval(nextHeroSlide, 5000);
  }
}

// ============================================
// DEADLINE TIMER
// ============================================
function initDeadlineTimer() {
  const daysEl = document.getElementById('days');
  const hoursEl = document.getElementById('hours');
  const minutesEl = document.getElementById('minutes');
  const secondsEl = document.getElementById('seconds');
  
  if (!daysEl) return;
  
  function updateDeadlineTimer() {
    const deadline = new Date();
    deadline.setDate(deadline.getDate() + 45);
    deadline.setHours(23, 59, 59);
    const now = new Date();
    const diff = deadline - now;
    
    if (diff <= 0) {
      if (daysEl) daysEl.textContent = '00';
      if (hoursEl) hoursEl.textContent = '00';
      if (minutesEl) minutesEl.textContent = '00';
      if (secondsEl) secondsEl.textContent = '00';
      return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (86400000)) / (3600000));
    const minutes = Math.floor((diff % 3600000) / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);
    
    if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
    if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
    if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
    if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
  }
  
  updateDeadlineTimer();
  setInterval(updateDeadlineTimer, 1000);
}

// ============================================
// SCHOLARSHIP SEARCH (for scholarships page)
// ============================================
function initScholarshipSearch() {
  const searchBtn = document.getElementById('searchScholarshipPageBtn');
  const searchInput = document.getElementById('scholarshipSearchPage');
  const searchMsg = document.getElementById('searchResultPageMsg');
  
  if (!searchBtn || !searchInput) return;
  
  const scholarships = [
    "Fulbright Foreign Student Program (USA)",
    "DAAD Scholarships (Germany)",
    "Chevening Scholarships (UK)",
    "Erasmus Mundus (Europe)",
    "Commonwealth Scholarships (UK)",
    "Vanier Canada Graduate Scholarships",
    "Utrecht Excellence Scholarship",
    "University of Auckland International",
    "Swedish Institute Scholarships"
  ];
  
  searchBtn.addEventListener('click', function() {
    const query = searchInput.value.toLowerCase().trim();
    if (!query) {
      if (searchMsg) searchMsg.innerHTML = '<span style="color: #e67e22;">✨ Type a keyword like "Germany", "UK", "USA"</span>';
      return;
    }
    
    const results = scholarships.filter(s => s.toLowerCase().includes(query));
    if (results.length > 0) {
      if (searchMsg) {
        searchMsg.innerHTML = `<span style="color: #2ecc71;">🔍 Found ${results.length} scholarship(s): ${results.slice(0,3).join(', ')}. <a href="inquiry.html" style="color: #3b82f6;">Apply for guidance →</a></span>`;
      }
    } else {
      if (searchMsg) searchMsg.innerHTML = '<span style="color: #e67e22;">No matching scholarships found. Contact our advisors for custom matches!</span>';
    }
  });
  
  if (searchInput) {
    searchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        searchBtn.click();
      }
    });
  }
}

// ============================================
// QUICK INQUIRY FORM (Index Page) - Backend API
// ============================================
function initQuickInquiryForm() {
  const quickForm = document.getElementById('quickInquiryForm');
  const quickFeedback = document.getElementById('quickFormFeedback');
  
  if (quickForm) {
    quickForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const name = document.getElementById('quickName')?.value.trim();
      const email = document.getElementById('quickEmail')?.value.trim();
      const country = document.getElementById('quickCountry')?.value || '';
      const interest = document.getElementById('quickInterest')?.value || '';
      
      if (!name || !email || !email.includes('@')) {
        if (quickFeedback) {
          quickFeedback.innerHTML = '<div style="color: #e67e22; padding: 10px; background: #f8d7da; border-radius: 8px;">⚠️ Please fill all required fields correctly.</div>';
          setTimeout(() => { if (quickFeedback) quickFeedback.innerHTML = ''; }, 3000);
        }
        return;
      }
      
      // Show loading state
      const submitBtn = quickForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.textContent = 'Submitting...';
      submitBtn.disabled = true;
      
      try {
        const response = await fetch(`${API_BASE_URL}/submit_inquiry.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ 
            name: name, 
            email: email, 
            country: country,
            interest: interest 
          })
        });
        
        const data = await response.json();
        
        if (quickFeedback) {
          if (data.status === 'success') {
            quickFeedback.innerHTML = `<div style="color: #2ecc71; padding: 10px; background: #d4edda; border-radius: 8px;">✅ ${data.message}</div>`;
            quickForm.reset();
          } else {
            quickFeedback.innerHTML = `<div style="color: #e67e22; padding: 10px; background: #f8d7da; border-radius: 8px;">❌ ${data.message}</div>`;
          }
        }
        setTimeout(() => { if (quickFeedback) quickFeedback.innerHTML = ''; }, 5000);
      } catch (error) {
        console.error('Form submission error:', error);
        if (quickFeedback) {
          quickFeedback.innerHTML = '<div style="color: #e67e22; padding: 10px;">❌ Network error. Please try again.</div>';
          setTimeout(() => { if (quickFeedback) quickFeedback.innerHTML = ''; }, 3000);
        }
      } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }
    });
  }
}

// ============================================
// INQUIRY FORM PAGE - DISABLED (Using inline script instead)
// ============================================
/* 
function initInquiryForm() {
  // THIS FUNCTION IS DISABLED TO PREVENT CONFLICT WITH INLINE SCRIPT
  // The inquiry.html page uses its own inline script for better control
  // and to avoid duplicate event listeners
}
*/

// ============================================
// CONTACT FORM - Backend API
// ============================================
function initContactForm() {
  const contactForm = document.getElementById('contactForm');
  const contactFeedback = document.getElementById('contactFeedback');
  
  if (contactForm) {
    contactForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const name = document.getElementById('contactName')?.value.trim();
      const email = document.getElementById('contactEmail')?.value.trim();
      const phone = document.getElementById('contactPhone')?.value || '';
      const subject = document.getElementById('contactSubject')?.value || 'General Inquiry';
      const message = document.getElementById('contactMessage')?.value.trim();
      
      if (!name || !email || !message) {
        if (contactFeedback) {
          contactFeedback.innerHTML = '<div class="alert alert-danger">⚠️ Please fill all required fields.</div>';
          setTimeout(() => { if (contactFeedback) contactFeedback.innerHTML = ''; }, 3000);
        }
        return;
      }
      
      if (!email.includes('@')) {
        if (contactFeedback) {
          contactFeedback.innerHTML = '<div class="alert alert-danger">⚠️ Please enter a valid email address.</div>';
          setTimeout(() => { if (contactFeedback) contactFeedback.innerHTML = ''; }, 3000);
        }
        return;
      }
      
      // Show loading state
      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.textContent = 'Sending...';
      submitBtn.disabled = true;
      
      try {
        const response = await fetch(`${API_BASE_URL}/contact.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ 
            name: name,
            email: email,
            phone: phone,
            subject: subject,
            message: message
          })
        });
        
        const data = await response.json();
        
        if (contactFeedback) {
          if (data.status === 'success') {
            contactFeedback.innerHTML = `<div class="alert alert-success">✅ ${data.message}</div>`;
            contactForm.reset();
          } else {
            contactFeedback.innerHTML = `<div class="alert alert-danger">❌ ${data.message}</div>`;
          }
        }
        setTimeout(() => { if (contactFeedback) contactFeedback.innerHTML = ''; }, 5000);
      } catch (error) {
        console.error('Contact form error:', error);
        if (contactFeedback) {
          contactFeedback.innerHTML = '<div class="alert alert-danger">❌ Network error. Please try again.</div>';
          setTimeout(() => { if (contactFeedback) contactFeedback.innerHTML = ''; }, 3000);
        }
      } finally {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }
    });
  }
}

// ============================================
// SMOOTH SCROLL FOR ANCHOR LINKS
// ============================================
function initSmoothScroll() {
  document.querySelectorAll('a[href="#about"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      const target = document.getElementById('about');
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
  
  // Smooth scroll for top link
  document.querySelectorAll('a[href="#top"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });
}

// ============================================
// STATS ANIMATION ON SCROLL
// ============================================
function initStatsAnimation() {
  const statNumbers = document.querySelectorAll('.stat-number');
  if (statNumbers.length === 0) return;
  
  const observerOptions = { threshold: 0.5, rootMargin: '0px' };
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);
  
  statNumbers.forEach(stat => {
    stat.style.opacity = '0';
    stat.style.transform = 'translateY(20px)';
    stat.style.transition = 'all 0.6s ease';
    observer.observe(stat);
  });
}

// ============================================
// SET ACTIVE NAVIGATION BASED ON CURRENT PAGE
// ============================================
function setActiveNavigation() {
  const currentPage = window.location.pathname.split('/').pop();
  const navItems = document.querySelectorAll('.nav-links a');
  
  navItems.forEach(item => {
    const href = item.getAttribute('href');
    if (href === currentPage || (currentPage === '' && href === 'index.html')) {
      item.classList.add('active');
    }
  });
}

// ============================================
// INITIALIZE ALL FUNCTIONS
// ============================================
document.addEventListener('DOMContentLoaded', function() {
  // Core functionality
  initNewsletter();
  initHeroSlideshow();
  initDeadlineTimer();
  initScholarshipSearch();
  initQuickInquiryForm();
  // initInquiryForm(); // DISABLED - Using inline script on inquiry.html page
  initContactForm();
  initSmoothScroll();
  initStatsAnimation();
  setActiveNavigation();
  
  console.log('✅ Bangtilenydoor Academy - All systems initialized');
  console.log('✅ Backend API URL:', API_BASE_URL);
  console.log('ℹ️ Inquiry form handler disabled in main.js - using inline script instead');
});