import { onMounted, onUnmounted, nextTick } from 'vue';

/**
 * Reveals elements with a [data-reveal] attribute as they scroll into view
 * by adding the `is-in` class (styled globally in app.css).
 */
export function useReveal() {
    let observer;

    onMounted(async () => {
        await nextTick();

        if (typeof IntersectionObserver === 'undefined') {
            document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-in'));
            return;
        }

        observer = new IntersectionObserver(
            (entries) => entries.forEach((e) => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-in');
                    observer.unobserve(e.target);
                }
            }),
            { threshold: 0.12 }
        );

        document.querySelectorAll('[data-reveal]').forEach((el) => observer.observe(el));
    });

    onUnmounted(() => observer?.disconnect());
}
