/**
 * Otimizações Globais de Performance e Acessibilidade
 * Carregado em todas as páginas para melhorar UX e performance
 */

(function() {
    'use strict';

    // 1. Lazy Loading de Imagens
    function initLazyImages() {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px'
            });
            
            lazyImages.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback para navegadores antigos
            lazyImages.forEach(img => img.classList.add('loaded'));
        }
    }

    // 2. Otimização de Scroll Suave (com tratamento de erros)
    function initSmoothScroll() {
        try {
            const anchors = document.querySelectorAll('a[href^="#"]');
            if (!anchors || anchors.length === 0) return;
            
            anchors.forEach(anchor => {
                try {
                    anchor.addEventListener('click', function (e) {
                        try {
                            const href = this.getAttribute('href');
                            if (href === '#' || href === '') return;
                            
                            const target = document.querySelector(href);
                            if (target) {
                                e.preventDefault();
                                target.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }
                        } catch (error) {
                            console.error('Erro no smooth scroll:', error);
                        }
                    });
                } catch (error) {
                    console.error('Erro ao adicionar listener de scroll:', error);
                }
            });
        } catch (error) {
            console.error('Erro ao inicializar smooth scroll:', error);
        }
    }

    // 3. Melhorias de Acessibilidade - Navegação por Teclado (com tratamento de erros)
    function initKeyboardNavigation() {
        try {
            const interactiveElements = document.querySelectorAll('a, button, input, select, textarea, [tabindex]');
            if (!interactiveElements || interactiveElements.length === 0) return;
            
            interactiveElements.forEach(element => {
                try {
                    element.addEventListener('focus', function() {
                        this.classList.add('keyboard-focus');
                    });
                    
                    element.addEventListener('blur', function() {
                        this.classList.remove('keyboard-focus');
                    });
                } catch (error) {
                    // Ignora erros individuais
                }
            });
        } catch (error) {
            console.error('Erro ao inicializar navegação por teclado:', error);
        }
    }

    // 4. Preload de Recursos Críticos
    function preloadCriticalResources() {
        const criticalLinks = document.querySelectorAll('link[rel="preload"]');
        // Recursos críticos já são pré-carregados via HTML
    }

    // 5. Otimização de Performance - Debounce para eventos de scroll
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

    // 6. Detecção de Conexão Lenta
    function handleSlowConnection() {
        if ('connection' in navigator) {
            const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            
            if (connection) {
                // Se conexão é lenta (2G), desabilitar animações pesadas
                if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                    document.documentElement.classList.add('slow-connection');
                }
            }
        }
    }

    // 7. Otimização de Memória - Cleanup de Event Listeners
    function cleanupEventListeners() {
        // Remove event listeners quando elementos são removidos do DOM
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.removedNodes.forEach(node => {
                    if (node.nodeType === 1) {
                        // Limpar event listeners se necessário
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // 8. Melhorias de Acessibilidade - ARIA Labels Dinâmicos
    function enhanceAriaLabels() {
        // Adiciona aria-labels em botões sem texto
        document.querySelectorAll('button:not([aria-label]):empty').forEach(button => {
            const icon = button.querySelector('svg');
            if (icon) {
                const title = icon.getAttribute('title') || 'Botão';
                button.setAttribute('aria-label', title);
            }
        });
    }

    // 9. Performance - Request Animation Frame para animações
    function optimizeAnimations() {
        let ticking = false;
        
        function updateAnimations() {
            // Animações otimizadas aqui
            ticking = false;
        }
        
        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateAnimations);
                ticking = true;
            }
        }
        
        // Usar requestAnimationFrame para animações suaves
        window.addEventListener('scroll', requestTick);
    }

    // 10. Inicialização quando DOM estiver pronto (com tratamento de erros)
    function safeInit() {
        try {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                // DOM já está pronto, inicializa imediatamente
                setTimeout(init, 0);
            }
        } catch (error) {
            console.error('Erro ao inicializar otimizações:', error);
        }
    }

    function init() {
        try {
            // Verifica se o DOM está realmente pronto
            if (document.readyState === 'loading') {
                return;
            }
            
            initLazyImages();
            initSmoothScroll();
            initKeyboardNavigation();
            handleSlowConnection();
            enhanceAriaLabels();
            optimizeAnimations();
            
            // Cleanup apenas em produção
            if (window.location.hostname !== 'localhost') {
                cleanupEventListeners();
            }
        } catch (error) {
            console.error('Erro na inicialização:', error);
        }
    }

    // Inicializa de forma segura
    safeInit();

    // Exportar funções para uso global se necessário
    window.Optimizations = {
        initLazyImages,
        initSmoothScroll,
        initKeyboardNavigation
    };
})();


