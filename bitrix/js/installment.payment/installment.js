/**
 * Installment Payment Module
 * Main JavaScript functionality for the module
 */
var InstallmentPayment = (function() {
    // Default configuration parameters
    var defaultConfig = {
        detailsLink: '/installment-info',
        minOrderAmount: 100,
        // Markup percentages for different payment periods
        markupPercentByPeriod: {
            2: 0,
            3: 0,
            4: 0,
            5: 0,
            6: 0,
            12: 5,
            18: 7,
            24: 10,
            36: 15,
            48: 20
        }
    };
    
    // Periods for payment in months
    var periodMonths = [2, 3, 4, 5, 6, 12, 18, 24, 36, 48];
    
    // Initialize the module
    function init(config) {
        var moduleConfig = Object.assign({}, defaultConfig, config);
        
        var isMouseDown = false;
        var isInsideModal = false;
        
        // Elements for work with modal window
        var openModalBtn = document.getElementById('openModalBtn');
        var installmentModal = document.getElementById('installmentModal');
        var closeModal = document.getElementById('closeModal');
        var detailsLink = document.getElementById('detailsLink');
        var addToCartBtn = document.getElementById('addToCartBtn');
        var periodTextValue = document.getElementById('periodTextValue');
        var initialPaymentText = document.getElementById('initialPaymentText');
        var todayPayment = document.getElementById('todayPayment');
        var monthlyPayment = document.getElementById('monthlyPayment');
        var monthlyPaymentCount = document.getElementById('monthlyPaymentCount');
        var currentPrice = document.getElementById('currentPrice');
        var installmentPrice = document.getElementById('installmentPrice');
        var installmentMonthly = document.querySelector('.installment-monthly');
        var noMarkupText = document.getElementById('noMarkupText');
        var initialPaymentValues = document.getElementById('initialPaymentValues');
        var periodValuesContainer = document.getElementById('periodValues');
        
        if (!openModalBtn || !installmentModal) {
            console.error('Installment Payment: Required elements not found');
            return;
        }
        
        // Set link from configuration
        if (detailsLink) {
            detailsLink.href = moduleConfig.detailsLink;
        }
        
        // Opening modal window
        openModalBtn.addEventListener('click', function() {
            installmentModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            initSliders();
        });
        
        // Closing modal window
        function closeModalWindow() {
            installmentModal.style.display = 'none';
            document.body.style.overflow = '';
        }
        
        if (closeModal) {
            closeModal.addEventListener('click', closeModalWindow);
        }
        
        // Fix for closing when dragging outside the modal
        if (installmentModal) {
            installmentModal.addEventListener('mousedown', function(event) {
                isMouseDown = true;
                
                // Check if click is inside the modal content
                var modalContent = document.querySelector('.installment-modal');
                if (modalContent && modalContent.contains(event.target)) {
                    isInsideModal = true;
                } else {
                    isInsideModal = false;
                }
            });
            
            document.addEventListener('mouseup', function(event) {
                if (isMouseDown) {
                    isMouseDown = false;
                    
                    // Only close if both mousedown and mouseup were outside the modal content
                    if (!isInsideModal && installmentModal.style.display === 'flex') {
                        // Check if mouseup is on the overlay
                        var modalContent = document.querySelector('.installment-modal');
                        if (modalContent && !modalContent.contains(event.target)) {
                            closeModalWindow();
                        }
                    }
                }
            });
        }
        
        // Initialize sliders
        function initSliders() {
            // Initialize the first payment slider
            initInitialPaymentSlider();
            
            // Initialize the payment period slider  
            initPeriodSlider();
            
            // Initialize calculations
            updateCalculations();
            
            // Initialize ripple effect
            initRippleEffect();
        }
        
        // Initial payment slider
        function initInitialPaymentSlider() {
            // Initial payment values in percentages
            var initialPaymentPercents = [0, 10, 20, 30, 40, 50];
            
            // Create values under the slider for initial payment
            createInitialPaymentValues();
            
            // Function for creating and positioning values under the initial payment slider
            function createInitialPaymentValues() {
                if (!initialPaymentValues) return;
                
                initialPaymentValues.innerHTML = ''; // Clear container
                
                initialPaymentPercents.forEach(function(value, index) {
                    var valueElement = document.createElement('span');
                    valueElement.classList.add('installment-value', 'initial-payment-value');
                    valueElement.textContent = value; // No % sign
                    valueElement.setAttribute('data-value', value);
                    
                    // Calculate position in percent of slider width
                    var position = index / (initialPaymentPercents.length - 1) * 100;
                    valueElement.style.left = position + '%';
                    
                    initialPaymentValues.appendChild(valueElement);
                    
                    // Add click handler
                    valueElement.addEventListener('click', function() {
                        initialPaymentSlider.noUiSlider.set(value);
                    });
                });
            }
            
            // Initialize initial payment slider
            var initialPaymentSlider = document.getElementById('initialPaymentSlider');
            if (initialPaymentSlider) {
                noUiSlider.create(initialPaymentSlider, {
                    start: 0,
                    connect: [true, false],
                    step: 10,
                    range: {
                        'min': 0,
                        'max': 50
                    }
                });
                
                // Handle slider movement
                initialPaymentSlider.noUiSlider.on('update', function(values) {
                    var value = parseInt(values[0]);
                    
                    // Update initial payment percentage text
                    if (initialPaymentText) {
                        initialPaymentText.textContent = value + '%';
                    }
                    
                    // Update active state of values
                    updateActiveState('initial', value / 10);
                    
                    // Update payment calculations
                    updateCalculations();
                });
            }
        }
        
        // Payment period slider
        function initPeriodSlider() {
            // Create values under the slider for payment period
            createPeriodValues();
            
            // Function for creating and positioning values under the payment period slider
            function createPeriodValues() {
                if (!periodValuesContainer) return;
                
                periodValuesContainer.innerHTML = ''; // Clear container
                
                periodMonths.forEach(function(value, index) {
                    var valueElement = document.createElement('span');
                    valueElement.classList.add('installment-value', 'period-value');
                    valueElement.textContent = value;
                    valueElement.setAttribute('data-value', index);
                    
                    // Calculate position in percent of slider width
                    var position = index / (periodMonths.length - 1) * 100;
                    valueElement.style.left = position + '%';
                    
                    periodValuesContainer.appendChild(valueElement);
                    
                    // Add click handler
                    valueElement.addEventListener('click', function() {
                        periodSlider.noUiSlider.set(index);
                    });
                });
            }
            
            // Initialize payment period slider
            var periodSlider = document.getElementById('periodSlider');
            if (periodSlider) {
                noUiSlider.create(periodSlider, {
                    start: 4, // Index for 6 months (default)
                    connect: [true, false],
                    step: 1,
                    range: {
                        'min': 0,
                        'max': periodMonths.length - 1
                    }
                });
                
                // Handle slider movement
                periodSlider.noUiSlider.on('update', function(values, handle) {
                    var index = Math.round(parseFloat(values[handle]));
                    var period = periodMonths[index];
                    
                    // Update period text
                    if (periodTextValue) {
                        periodTextValue.textContent = period + getPeriodText(period);
                    }
                    
                    // Update active state of months
                    updateActiveState('period', index);
                    
                    // Update payment calculations
                    updateCalculations();
                });
            }
        }
        
        // Update active state of values under sliders
        function updateActiveState(sliderType, index) {
            var valuesSelector = sliderType === 'initial' ? '.initial-payment-value' : '.period-value';
            var valueElements = document.querySelectorAll(valuesSelector);
            
            valueElements.forEach(function(element, i) {
                if (i === index) {
                    element.classList.add('installment-value-active');
                } else {
                    element.classList.remove('installment-value-active');
                }
            });
        }
        
        // Update payment calculations
        function updateCalculations() {
            // Get slider values
            var initialPaymentSlider = document.getElementById('initialPaymentSlider');
            var periodSlider = document.getElementById('periodSlider');
            
            if (!initialPaymentSlider || !periodSlider) {
                return;
            }
            
            var initialPercent = parseFloat(initialPaymentSlider.noUiSlider.get());
            var periodIndex = Math.round(parseFloat(periodSlider.noUiSlider.get()));
            var period = periodMonths[periodIndex];
            
            // Calculate payments
            var initialPaymentAmount = (initialPercent / 100) * moduleConfig.productPrice;
            var remainingAmount = moduleConfig.productPrice - initialPaymentAmount;
            
            // Get markup percentage for selected period from settings
            var markupPercent = moduleConfig.markupPercentByPeriod[period];
            
            // Calculate amount with markup
            var totalWithMarkup = remainingAmount * (1 + markupPercent / 100);
            var monthlyAmount = totalWithMarkup / period;
            var totalPrice = initialPaymentAmount + totalWithMarkup;
            
            // Update display
            if (todayPayment) {
                todayPayment.textContent = formatCurrency(initialPaymentAmount, true);
            }
            
            // Update monthly payment text with tilde (~) if there's markup
            if (monthlyPayment) {
                if (markupPercent === 0) {
                    monthlyPayment.textContent = formatCurrency(monthlyAmount, true);
                    if (noMarkupText) {
                        noMarkupText.style.display = 'block';
                    }
                } else {
                    monthlyPayment.textContent = '~' + formatCurrency(monthlyAmount, true);
                    if (noMarkupText) {
                        noMarkupText.style.display = 'none';
                    }
                }
            }
            
            if (monthlyPaymentCount) {
                monthlyPaymentCount.textContent = period;
            }
            
            if (currentPrice) {
                currentPrice.textContent = formatCurrency(moduleConfig.productPrice);
            }
            
            if (installmentPrice) {
                installmentPrice.textContent = formatCurrency(totalPrice);
            }
            
            // Calculate minimum monthly payment for button
            var minMonthlyPayment = moduleConfig.productPrice / 48; // 48 months maximum period
            if (installmentMonthly) {
                installmentMonthly.textContent = 'От ' + formatCurrency(minMonthlyPayment, true) + ' в месяц потом';
            }
        }
        
        // Currency formatting
        function formatCurrency(amount, useRubShort) {
            var formatted = amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$& ').replace('.00', '').replace('.', ',');
            return formatted + (useRubShort ? ' р.' : ' руб.');
        }
        
        // Get declension for period
        function getPeriodText(period) {
            var lastDigit = period % 10;
            var lastTwoDigits = period % 100;
            
            if (lastTwoDigits >= 11 && lastTwoDigits <= 14) {
                return ' месяцев';
            }
            
            if (lastDigit === 1) {
                return ' месяц';
            }
            
            if (lastDigit >= 2 && lastDigit <= 4) {
                return ' месяца';
            }
            
            return ' месяцев';
        }
        
        // Handler for "Add to cart" button
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                addToCart();
            });
        }
        
        // Add product to cart with installment parameters
        function addToCart() {
            var initialPaymentSlider = document.getElementById('initialPaymentSlider');
            var periodSlider = document.getElementById('periodSlider');
            
            if (!initialPaymentSlider || !periodSlider) {
                return;
            }
            
            // Get selected parameters
            var initialPercent = parseFloat(initialPaymentSlider.noUiSlider.get());
            var periodIndex = Math.round(parseFloat(periodSlider.noUiSlider.get()));
            var period = periodMonths[periodIndex];
            
            // In real module there will be AJAX request here
            console.log('Adding product to cart with parameters:', {
                productId: moduleConfig.productId || 'current-product',
                initialPayment: initialPercent,
                installmentPeriod: period,
                markupPercent: moduleConfig.markupPercentByPeriod[period]
            });
            
            // For demonstration only
            if (typeof BX !== 'undefined' && BX.ajax) {
                BX.ajax.runAction('installment:payment.addToCart', {
                    data: {
                        productId: moduleConfig.productId,
                        initialPayment: initialPercent,
                        installmentPeriod: period
                    }
                }).then(function(response) {
                    if (response.status === 'success') {
                        closeModalWindow();
                    }
                });
            } else {
                // Fallback for demo
                alert('Товар добавлен в корзину с параметрами:\n- Первоначальный платеж: ' + initialPercent + 
                    '%\n- Срок рассрочки: ' + period + getPeriodText(period) + 
                    '\n- Наценка: ' + moduleConfig.markupPercentByPeriod[period] + '%');
                closeModalWindow();
            }
        }
        
        // Initialize ripple effect for buttons
        function initRippleEffect() {
            var rippleElements = document.querySelectorAll('.has-ripple');
            
            rippleElements.forEach(function(element) {
                element.addEventListener('click', function(e) {
                    // Parameters for calculation
                    var duration = 0.6,
                        delayBetweenRipples = 1;
                    
                    var rect = element.getBoundingClientRect(),
                        left = e.clientX - rect.left,
                        top = e.clientY - rect.top,
                        width = element.offsetWidth,
                        height = element.offsetHeight,
                        dx = left > width / 2 ? left : width - left,
                        dy = top > height / 2 ? top : height - top,
                        maxX = Math.max(dx, width - dx),
                        maxY = Math.max(dy, height - dy),
                        radius = Math.sqrt(maxX * maxX + maxY * maxY),
                        ripple = document.createElement('span'),
                        style = 'width: ' + (radius * 2) + 'px; height: ' + (radius * 2) + 'px; top: ' + (top - radius) + 'px; left: ' + (left - radius) + 'px;';
                    
                    // Add ripple element at the end
                    ripple.className = 'ripple';
                    ripple.setAttribute('style', style);
                    element.appendChild(ripple);
                    
                    // Start animation
                    setTimeout(function() {
                        ripple.classList.add('ripple-animate');
                        
                        // Clean up DOM
                        setTimeout(function() {
                            if (element.contains(ripple)) {
                                element.removeChild(ripple);
                            }
                        }, duration * 1000);
                    }, 0);
                });
            });
        }
    }
    
    // Open modal for the specified product
    function openModal(element) {
        var productId, productPrice, productName, productImage;
        
        if (element) {
            // Extract data from button or block attributes
            productId = element.getAttribute('data-product-id');
            productPrice = parseFloat(element.getAttribute('data-product-price'));
            productName = element.getAttribute('data-product-name');
            productImage = element.getAttribute('data-product-image');
        }
        
        var modal = document.getElementById('installmentModal');
        if (modal) {
            // Set product info in modal
            var nameElement = modal.querySelector('.installment-offer-name');
            var imageElement = modal.querySelector('.installment-image');
            
            if (nameElement && productName) {
                nameElement.textContent = productName;
            }
            
            if (imageElement && productImage) {
                imageElement.src = productImage;
            }
            
            // Show modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    
    return {
        init: init,
        openModal: openModal
    };
})();

// Auto-initialize if parameters are provided in global scope
document.addEventListener('DOMContentLoaded', function() {
    if (typeof InstallmentPaymentParams !== 'undefined') {
        InstallmentPayment.init(InstallmentPaymentParams);
    }
});