import './bootstrap';

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

const isAnimatableChild = (node) => {
    return node instanceof HTMLElement
        && !node.matches('script, style, template, link, meta')
        && !node.hasAttribute('data-no-reveal')
        && !node.hasAttribute('x-cloak');
};

const animatePageBlocks = () => {
    if (prefersReducedMotion.matches) {
        return;
    }

    document.querySelectorAll('[data-page-animate]').forEach((container) => {
        if (!(container instanceof HTMLElement) || container.dataset.pageAnimated === '1') {
            return;
        }

        const children = Array.from(container.children).filter(isAnimatableChild);
        children.forEach((child, index) => {
            const delay = Math.min(index, 14) * 75 + 40;

            child.animate(
                [
                    { opacity: 0, transform: 'translateY(16px) scale(0.992)' },
                    { opacity: 1, transform: 'translateY(0) scale(1)' }
                ],
                {
                    duration: 620,
                    delay,
                    easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                    fill: 'both'
                }
            );
        });

        container.dataset.pageAnimated = '1';
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', animatePageBlocks);
} else {
    animatePageBlocks();
}
