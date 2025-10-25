/**
 * Dashboard Accordion Controller
 * Independent accordion behavior for dashboard sections
 * All sections can be toggled independently
 */

$(document).ready(function() {
    'use strict';
    
    // Initialize dashboard accordion
    initDashboardAccordion();
    
    function initDashboardAccordion() {
        var $accordion = $('#dashboardAccordion');
        
        if ($accordion.length === 0) {
            console.warn('Dashboard accordion not found');
            return;
        }
        
        // Add custom class for styling
        $accordion.addClass('dashboard-accordion');
        
        // Handle accordion toggle clicks using event delegation
        $accordion.on('click', '.panel-title a', function(e) {
            e.preventDefault();
            e.stopPropagation();
            handleAccordionToggle($(this));
        });
        
        // Handle Bootstrap collapse events
        $accordion.on('show.bs.collapse', function (e) {
            updateAccordionIcon($(e.target), 'open');
        });
        
        $accordion.on('hide.bs.collapse', function (e) {
            updateAccordionIcon($(e.target), 'close');
        });
        
        // Initialize icons based on current state
        initializeAccordionIcons();
        
        // Lazy load charts when sections are opened
        setupLazyLoading();
    }
    
    function handleAccordionToggle($link) {
        var target = $link.attr('href');
        var $target = $(target);
        var isExpanded = $link.attr('aria-expanded') === 'true';
        
        // Special handling for Statistik Utama
        if (target === '#collapseStats') {
            if ($target.hasClass('in')) {
                // Force close
                $target.removeClass('in').css('height', '').hide();
                $link.attr('aria-expanded', 'false');
                $link.find('.accordion-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            } else {
                // Force open
                $target.addClass('in').css('height', 'auto').show();
                $link.attr('aria-expanded', 'true');
                $link.find('.accordion-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }
            return;
        }
        
        // Normal handling for other sections
        if (isExpanded) {
            // Close the panel
            $target.collapse('hide');
        } else {
            // Open the panel (independent behavior)
            $target.collapse('show');
        }
    }
    
    function updateAccordionIcon($panel, state) {
        var $heading = $panel.prev('.panel-heading');
        var $icon = $heading.find('.accordion-icon');
        var $link = $heading.find('a');
        
        if (state === 'open') {
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $link.attr('aria-expanded', 'true');
        } else {
            $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $link.attr('aria-expanded', 'false');
        }
    }
    
    function initializeAccordionIcons() {
        $('#dashboardAccordion .panel-collapse').each(function() {
            var $panel = $(this);
            var $heading = $panel.prev('.panel-heading');
            var $icon = $heading.find('.accordion-icon');
            var $link = $heading.find('a');
            
            if ($panel.hasClass('in')) {
                // Panel is open
                $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                $link.attr('aria-expanded', 'true');
            } else {
                // Panel is closed
                $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                $link.attr('aria-expanded', 'false');
            }
        });
    }
    
    function setupLazyLoading() {
        // Lazy load charts when accordion sections are opened
        $('#collapseCharts').on('shown.bs.collapse', function () {
            // Redraw charts when charts section is opened
            setTimeout(function() {
                if (typeof $.plot !== 'undefined') {
                    if (typeof trenDataset !== 'undefined' && typeof trenOptions !== 'undefined') {
                        $.plot($("#tren-partisipasi-chart"), trenDataset, trenOptions);
                    }
                    if (typeof barDataset !== 'undefined' && typeof barOptions !== 'undefined') {
                        $.plot($("#distribusi-skor-chart"), barDataset, barOptions);
                    }
                }
            }, 100);
        });

        $('#collapseTrenPelanggan').on('shown.bs.collapse', function () {
            // Redraw tren pelanggan chart when opened
            setTimeout(function() {
                if (typeof $.plot !== 'undefined') {
                    if (typeof trenPelangganDataset !== 'undefined' && typeof trenPelangganOptions !== 'undefined') {
                        $.plot($("#tren-pelanggan-baru-chart"), trenPelangganDataset, trenPelangganOptions);
                    }
                }
            }, 100);
        });
    }
    
    // Optional: Remember accordion state in localStorage
    function saveAccordionState() {
        var activePanels = [];
        $('#dashboardAccordion .panel-collapse.in').each(function() {
            activePanels.push($(this).attr('id'));
        });
        localStorage.setItem('dashboardAccordionState', JSON.stringify(activePanels));
    }
    
    function restoreAccordionState() {
        var savedState = localStorage.getItem('dashboardAccordionState');
        if (savedState) {
            try {
                var activePanels = JSON.parse(savedState);
                // Close all panels first
                $('#dashboardAccordion .panel-collapse.in').collapse('hide');
                
                // Open saved panels
                activePanels.forEach(function(panelId) {
                    $('#' + panelId).collapse('show');
                });
            } catch (e) {
                console.warn('Could not restore accordion state:', e);
            }
        }
    }
    
    // Uncomment the lines below if you want to remember accordion state
    /*
    $('#dashboardAccordion').on('hidden.bs.collapse shown.bs.collapse', function () {
        saveAccordionState();
    });
    
    // Restore state on page load (after a short delay to ensure DOM is ready)
    setTimeout(restoreAccordionState, 100);
    */
});
