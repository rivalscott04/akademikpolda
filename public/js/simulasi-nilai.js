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
        kecermatan: document.querySelector('input[name="kecermatan"]'),
        kecerdasan: document.querySelector('input[name="kecerdasan"]'),
        kepribadian: document.querySelector('input[name="kepribadian"]')
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
                `Bobot saat ini: Kecermatan ${settings.weights.kecermatan}%, Kecerdasan ${settings.weights.kecerdasan}%, Kepribadian ${settings.weights.kepribadian}%.`;
        }
        
        // Update passing grade display
        if (resultElements.passingGradeDisplay) {
            resultElements.passingGradeDisplay.textContent = settings.passing_grade;
        }
        
        // Update formula display
        if (resultElements.formulaDisplay) {
            resultElements.formulaDisplay.textContent = 
                `Rumus: (${settings.weights.kecermatan}% × Kecermatan) + (${settings.weights.kecerdasan}% × Kecerdasan) + (${settings.weights.kepribadian}% × Kepribadian)`;
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
            kecermatan: parseFloat(inputs.kecermatan?.value || 0),
            kecerdasan: parseFloat(inputs.kecerdasan?.value || 0),
            kepribadian: parseFloat(inputs.kepribadian?.value || 0)
        };
        
        // Validate inputs
        const hasValidInputs = Object.values(values).some(val => val > 0);
        if (!hasValidInputs) {
            resetDisplay();
            return;
        }
        
        // Calculate final score
        const w1 = settings.weights.kecermatan / 100;
        const w2 = settings.weights.kecerdasan / 100;
        const w3 = settings.weights.kepribadian / 100;
        
        const finalScore = (w1 * values.kecermatan) + (w2 * values.kecerdasan) + (w3 * values.kepribadian);
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
