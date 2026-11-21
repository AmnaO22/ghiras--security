/* ===================================================
   غِراس (Ghiras) - Main JavaScript File
   ================================================   */

document.addEventListener('DOMContentLoaded', function () {

    // DELETE CONFIRMATION 
    const deleteLinks = document.querySelectorAll('a[href*="delete"], .delete-btn');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const confirmed = confirm('هل أنت متأكد من رغبتك في حذف هذه المبادرة؟');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });


    // FORM VALIDATION AND CONFIRMATION
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('initiativeForm');
    const titleInput = document.getElementById('titleInput');

    if (submitBtn && form && titleInput) {
        submitBtn.addEventListener('click', function () {
            // Check if title is empty
            if (titleInput.value.trim() === '') {
                alert('يرجى إدخال عنوان المبادرة قبل الحفظ.');
                titleInput.focus();
                return; // Stop here
            }

            // Ask for confirmation before saving/updating
            const saveConfirmed = confirm('هل أنت متأكد من رغبتك في حفظ هذه البيانات؟');
            if (saveConfirmed) {
                form.submit(); // Submit only if confirmed
            }
        });
    }

});

document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.card, .initiative-card, div[style*="background"]');

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            // 1. Update active button styles
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.style.backgroundColor = '#fff';
                btn.style.color = '#004d26';
            });
            
            this.classList.add('active');
            this.style.backgroundColor = '#004d26';
            this.style.color = '#fff';

            // 2. Filter the cards based on category text match
            const selectedCategory = this.getAttribute('data-filter');

            cards.forEach(card => {
                const text = card.textContent;
                if (text.length > 0) { // Skip empty wrapper divs
                    if (selectedCategory === 'all' || text.includes(selectedCategory)) {
                        card.style.display = ''; // Show card
                    } else {
                        card.style.display = 'none'; // Hide card
                    }
                }
            });
        });
    });
});


