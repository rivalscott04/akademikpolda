/**
 * Simulasi Nilai Real-time Calculator
 * File JavaScript terpisah untuk menghindari konflik dengan kode yang sudah ada
 */
(function() {
    'use strict';
    
    // Cache DOM elements
    const form = document.querySelector('form[action*="simulasi/nilai"]');
    const btnHitung = document.getElementById('btnHitung');
    const btnReset = document.querySelector('button[formaction*="reset"]');
    const inputs = {
        bahasa_inggris: document.querySelector('input[name="bahasa_inggris"]'),
        pengetahuan_umum: document.querySelector('input[name="pengetahuan_umum"]'),
        twk: document.querySelector('input[name="twk"]'),
        numerik: document.querySelector('input[name="numerik"]')
    };
    
    // Cache result elements
    const resultElements = {
        scoreDisplay: document.querySelector('.score-display'),
        badgeContainer: document.querySelector('h3 .label'),
        weightsDisplay: document.querySelector('.weights-display'),
        passingGradeDisplay: document.querySelector('.passing-grade'),
        formulaDisplay: document.querySelector('.formula-display')
    };
    
    // Settings cache
    let settings = null;
    
    // Initialize
    function init() {
        if (!form) return;
        
        // Load settings from server
        loadSettings();
        
        // Add event listeners
        addEventListeners();
    }
    
    // Load scoring settings from server
    async function loadSettings() {
        try {
            const response = await fetch('/simulasi/nilai/settings');
            settings = await response.json();
            updateDisplayWithSettings();
        } catch (error) {
            console.error('Error loading settings:', error);
        }
    }
    
    // Update display elements with settings data
    function updateDisplayWithSettings() {
        if (!settings) return;
        
        // Update weights display
        if (resultElements.weightsDisplay) {
            resultElements.weightsDisplay.textContent = 
                `Bobot saat ini: Bahasa Inggris ${settings.weights.bahasa_inggris}%, Pengetahuan Umum ${settings.weights.pengetahuan_umum}%, TWK ${settings.weights.twk}%, Penalaran Numerik ${settings.weights.numerik}%.`;
        }
        
        // Update passing grade display
        if (resultElements.passingGradeDisplay) {
            resultElements.passingGradeDisplay.textContent = settings.passing_grade;
        }
        
        // Update formula display
        if (resultElements.formulaDisplay) {
            resultElements.formulaDisplay.textContent = 
                `Rumus: (${settings.weights.bahasa_inggris}% × Bahasa Inggris) + (${settings.weights.pengetahuan_umum}% × Pengetahuan Umum) + (${settings.weights.twk}% × TWK) + (${settings.weights.numerik}% × Penalaran Numerik)`;
        }
    }
    
    // Add event listeners
    function addEventListeners() {
        // Listen to input changes for real-time calculation
        Object.values(inputs).forEach(input => {
            if (input) {
                input.addEventListener('input', debounce(calculateRealTime, 300));
                input.addEventListener('blur', calculateRealTime);
            }
        });
        
        // Override form submit to show loading state
        if (form) {
            form.addEventListener('submit', handleFormSubmit);
        }
        
        // Handle reset button
        if (btnReset) {
            btnReset.addEventListener('click', handleReset);
        }
    }
    
    // Handle form submission
    function handleFormSubmit(e) {
        if (btnHitung) {
            btnHitung.disabled = true;
            btnHitung.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menghitung...';
        }
    }
    
    // Handle reset button
    function handleReset(e) {
        // Reset all inputs
        Object.values(inputs).forEach(input => {
            if (input) {
                input.value = '';
            }
        });
        
        // Reset display
        resetDisplay();
        
        // Prevent form submission for reset
        e.preventDefault();
        
        // Submit reset form
        const resetForm = document.createElement('form');
        resetForm.method = 'POST';
        resetForm.action = '/simulasi/nilai/reset';
        
        const csrfToken = document.querySelector('input[name="_token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.value;
            resetForm.appendChild(csrfInput);
        }
        
        document.body.appendChild(resetForm);
        resetForm.submit();
    }
    
    // Calculate score in real-time
    function calculateRealTime() {
        if (!settings) return;
        
        const values = {
            bahasa_inggris: parseFloat(inputs.bahasa_inggris?.value || 0),
            pengetahuan_umum: parseFloat(inputs.pengetahuan_umum?.value || 0),
            twk: parseFloat(inputs.twk?.value || 0),
            numerik: parseFloat(inputs.numerik?.value || 0)
        };
        
        // Validate inputs
        const hasValidInputs = Object.values(values).some(val => val > 0);
        if (!hasValidInputs) {
            resetDisplay();
            return;
        }
        
        // Calculate final score
        const w1 = settings.weights.bahasa_inggris / 100;
        const w2 = settings.weights.pengetahuan_umum / 100;
        const w3 = settings.weights.twk / 100;
        const w4 = settings.weights.numerik / 100;
        
        const finalScore = (w1 * values.bahasa_inggris) + (w2 * values.pengetahuan_umum) + (w3 * values.twk) + (w4 * values.numerik);
        const passed = finalScore >= settings.passing_grade;
        
        // Update display
        updateResultDisplay(finalScore, passed);
    }
    
    // Update result display
    function updateResultDisplay(score, passed) {
        // Update score display
        if (resultElements.scoreDisplay) {
            resultElements.scoreDisplay.textContent = score.toFixed(2);
        }
        
        // Update badge
        if (resultElements.badgeContainer) {
            resultElements.badgeContainer.className = 'label m-l-sm ' + (passed ? 'label-success' : 'label-danger');
            resultElements.badgeContainer.textContent = passed ? 'LULUS' : 'TIDAK LULUS';
        }
    }
    
    // Reset display to default state
    function resetDisplay() {
        // Reset score display
        if (resultElements.scoreDisplay) {
            resultElements.scoreDisplay.textContent = '0';
        }
        
        // Reset badge
        if (resultElements.badgeContainer) {
            resultElements.badgeContainer.className = 'label label-default m-l-sm';
            resultElements.badgeContainer.textContent = 'Belum dihitung';
        }
    }
    
    // Debounce function to limit function calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
